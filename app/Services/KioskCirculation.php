<?php

namespace App\Services;

use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Server-owned session for ACTUAL QR scans. The browser never decides a match
 * and never writes loans/stock. Pilot checkout is explicitly opt-in.
 * A physical book QR can be copied: supervised pilot ONLY until station trust
 * and physical custody are addressed.
 */
final class KioskCirculation
{
    public function __construct(private readonly KioskQrResolver $resolver) {}

    private function enabled(): bool
    {
        return (bool) config('kiosk_circulation.live_loans', false)
            && (bool) config('kiosk_circulation.staff_pilot', false)
            && ! app()->environment('production');
    }

    public function reset(int $adminId): array
    {
        return DB::transaction(function () use ($adminId) {
            // Lock the administrator row to serialize initial session creation.
            DB::table('users')->where('id', $adminId)->lockForUpdate()->first();
            DB::table('library_kiosk_circulation_sessions')->where('admin_id', $adminId)->delete();

            return $this->reply('ready', null, null, 'Sesi dibatalkan. Siap untuk scan berikutnya.');
        }, 3);
    }

    public function scan(int $adminId, string $payload, string $channel): array
    {
        try {
            return DB::transaction(function () use ($adminId, $payload, $channel) {
                DB::table('users')->where('id', $adminId)->lockForUpdate()->first();
                $row = DB::table('library_kiosk_circulation_sessions')->where('admin_id', $adminId)
                    ->lockForUpdate()->first();
                $fresh = ! $row || $row->status !== 'waiting' || ! $row->expires_at
                    || Carbon::parse($row->expires_at)->isPast();
                if ($fresh) {
                    DB::table('library_kiosk_circulation_sessions')->updateOrInsert(
                        ['admin_id' => $adminId], ['member_id' => null, 'copy_id' => null, 'status' => 'waiting',
                            'last_loan_id' => null, 'expires_at' => now()->addSeconds((int) config('kiosk_circulation.session_seconds', 45)),
                            'updated_at' => now(), 'created_at' => now()]);
                    $row = DB::table('library_kiosk_circulation_sessions')->where('admin_id', $adminId)->lockForUpdate()->first();
                }
                $result = $this->resolver->resolve($payload, true);
                $this->resolver->log($result, $channel, $adminId);
                if (($result['outcome'] ?? '') !== 'identified') {
                    return $this->reply('rejected', $row->member_id, $row->copy_id,
                        'QR ditolak: '.($result['outcome'] ?? 'invalid'));
                }
                if (! in_array($result['kind'], ['member', 'book'], true)) {
                    return $this->reply('rejected', $row->member_id, $row->copy_id, 'Gunakan QR anggota LIBMEM1 atau QR buku LIBBOOK1.');
                }
                $memberId = $result['kind'] === 'member' ? (int) $result['member_id'] : $row->member_id;
                $copyId = $result['kind'] === 'book' ? (int) $result['copy_id'] : $row->copy_id;
                if ($result['kind'] === 'member' && $row->member_id && $memberId !== (int) $row->member_id) {
                    return $this->reply('rejected', $row->member_id, $row->copy_id, 'Sesi sudah milik anggota lain. Tekan RESET.');
                }
                if ($result['kind'] === 'book' && $row->copy_id && $copyId !== (int) $row->copy_id) {
                    return $this->reply('rejected', $row->member_id, $row->copy_id, 'Sesi satu buku. Tekan RESET untuk buku lain.');
                }
                DB::table('library_kiosk_circulation_sessions')->where('admin_id', $adminId)
                    ->update(['member_id' => $memberId, 'copy_id' => $copyId, 'updated_at' => now()]);
                if (! $memberId || ! $copyId) {
                    return $this->reply($memberId ? 'waiting_book' : 'waiting_member', $memberId, $copyId,
                        $memberId ? 'Anggota dikenali. Scan QR buku fisik.' : 'Buku dikenali. Scan QR anggota.');
                }
                $outcome = $this->finish((int) $adminId, (int) $memberId, (int) $copyId);
                DB::table('library_kiosk_circulation_sessions')->where('admin_id', $adminId)
                    ->update(['status' => 'complete', 'last_loan_id' => $outcome['loan_id'], 'updated_at' => now()]);

                return $this->reply($outcome['live'] ? 'borrowed' : 'validated', $memberId, $copyId,
                    $outcome['message'], $outcome['loan_id']);
            }, 3);
        } catch (DomainException $e) {
            // Domain failures roll back the entire transaction, including loan creation.
            return $this->reply('rejected', null, null, $e->getMessage());
        }
    }

    private function finish(int $adminId, int $memberId, int $copyId): array
    {
        // Uniform validation for preview and live. Serializes on catalogue book.
        $member = DB::table('users')->where('id', $memberId)->where('role', 'user')->where('status', 'aktif')->lockForUpdate()->first();
        $copyLookup = DB::table('library_kiosk_book_copies')->where('id', $copyId)->first();
        if (! $copyLookup) {
            throw new DomainException('Eksemplar tidak aktif atau tidak terdaftar.');
        }
        $book = DB::table('buku')->where('id_buku', $copyLookup->book_id)->lockForUpdate()->first();
        $copy = DB::table('library_kiosk_book_copies')->where('id', $copyId)->where('active', 1)->lockForUpdate()->first();
        if (! $copy || $copy->status !== 'available' || ! $book || ! $member) {
            throw new DomainException('Anggota atau buku tidak tersedia.');
        }
        if ((int) $book->stock <= 0) {
            throw new DomainException('Stok judul sedang kosong.');
        }
        $active = DB::table('peminjaman')->where('user_id', $memberId)
            ->whereIn('status', ['dipinjam', 'menunggu'])->count();
        if ($active >= (int) config('kiosk_circulation.max_active_loans', 3)) {
            throw new DomainException('Batas maksimum pinjaman aktif tercapai.');
        }
        if (DB::table('peminjaman')->where('user_id', $memberId)->where('id_buku', $copy->book_id)
            ->whereIn('status', ['dipinjam', 'menunggu'])->exists()) {
            throw new DomainException('Anggota sedang meminjam/mengajukan judul yang sama.');
        }
        $lastCheckout = DB::table('library_kiosk_checkout_audit as a')
            ->join('peminjaman as p', 'p.peminjaman_id', '=', 'a.loan_id')
            ->where('a.copy_id', $copyId)->where('a.mode', 'live')
            ->whereIn('p.status', ['menunggu', 'dipinjam'])->exists();
        if ($lastCheckout) {
            throw new DomainException('Eksemplar fisik masih tercatat dipinjam.');
        }
        $overdue = DB::table('peminjaman')->where('user_id', $memberId)->where('status', 'dipinjam')
            ->whereDate('tanggal_pinjam', '<', today()->subDays((int) config('kiosk_circulation.loan_days', 7)))->exists();
        if ($overdue) {
            throw new DomainException('Ada peminjaman terlambat yang belum diselesaikan.');
        }
        if (! $this->enabled()) {
            DB::table('library_kiosk_checkout_audit')->insert(['admin_id' => $adminId, 'member_id' => $memberId,
                'copy_id' => $copyId, 'loan_id' => null, 'mode' => 'validate', 'created_at' => now()]);

            return ['live' => false, 'loan_id' => null, 'message' => 'VALIDASI QR ASLI BERHASIL (tanpa transaksi). Aktifkan pilot bila siap.'];
        }
        // Never bypass the existing domain workflow: it maintains the aggregate stock.
        $workflow = app(LoanWorkflow::class);
        $loan = $workflow->request($memberId, (int) $copy->book_id);
        $loan->copy_id = $copyId;
        $loan->source = 'staff_pilot';
        $loan->save();
        $loan = $workflow->apply((int) $loan->peminjaman_id, 'dipinjam');
        DB::table('library_kiosk_checkout_audit')->insert(['admin_id' => $adminId, 'member_id' => $memberId,
            'copy_id' => $copyId, 'loan_id' => $loan->peminjaman_id, 'mode' => 'live', 'created_at' => now()]);

        return ['live' => true, 'loan_id' => $loan->peminjaman_id,
            'message' => 'PEMINJAMAN TERCATAT. Jatuh tempo: '.today()->addDays((int) config('kiosk_circulation.loan_days', 7))->format('d/m/Y')];
    }

    public function status(int $adminId): array
    {
        $row = DB::table('library_kiosk_circulation_sessions')->where('admin_id', $adminId)->first();
        if (! $row || ($row->expires_at && Carbon::parse($row->expires_at)->isPast() && $row->status === 'waiting')) {
            return $this->reply('ready', null, null, 'Menunggu QR siswa atau buku.');
        }

        return $this->reply($row->status === 'complete' ? 'complete' : ($row->member_id ? 'waiting_book' : ($row->copy_id ? 'waiting_member' : 'ready')),
            $row->member_id, $row->copy_id, 'Sesi aktif.');
    }

    private function reply(string $state, ?int $memberId, ?int $copyId, string $message, ?int $loanId = null): array
    {
        $memberLabel = null;
        $bookLabel = null;
        if ($memberId) {
            $u = DB::table('users')->where('id', $memberId)->first(['nama']);
            $memberLabel = $u ? strtok($u->nama, ' ') : null;
        }
        if ($copyId) {
            $b = DB::table('library_kiosk_book_copies as c')->join('buku as b', 'b.id_buku', '=', 'c.book_id')
                ->where('c.id',$copyId)->first(['c.label', 'b.judul']);
            $bookLabel = $b ? ($b->judul.' / '.$b->label) : null;
        }

        return ['state' => $state, 'message' => $message, 'member' => $memberLabel, 'book' => $bookLabel,
            'loan_id' => $loanId, 'live_enabled' => $this->enabled(), 'server_driven' => true];
    }
}
