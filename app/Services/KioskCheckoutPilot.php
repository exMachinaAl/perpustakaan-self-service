<?php
namespace App\Services;

use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

/** Supervised checkout for a device-validated QR pair. No public kiosk endpoint can execute it. */
final class KioskCheckoutPilot
{
    public function enabled(): bool
    {
        return (bool) config('kiosk_pilot.enabled', false) && !app()->environment('production');
    }

    public function approve(int $deviceId, int $adminId): array
    {
        if (!$this->enabled()) {
            throw new DomainException('Pilot peminjaman tidak diaktifkan. Mode validasi tetap aman.');
        }

        return DB::transaction(function () use ($deviceId, $adminId) {
            $device = DB::table('library_kiosk_devices')->where('id', $deviceId)->lockForUpdate()->first();
            if (!$device || !$device->active) throw new DomainException('Perangkat kiosk tidak aktif.');
            $session = DB::table('library_kiosk_device_sessions')->where('device_id', $deviceId)
                ->lockForUpdate()->first();
            if (!$session) throw new DomainException('Belum ada sesi scan pada perangkat.');
            if ($session->state === 'borrowed' && $session->checkout_loan_id) {
                return ['loan_id' => (int) $session->checkout_loan_id, 'already_done' => true,
                    'due_date' => $session->checkout_due_date];
            }
            if ($session->state !== 'validated' || !$session->member_id || !$session->copy_id
                || !$session->validated_event_id || !$session->expires_at
                || Carbon::parse($session->expires_at)->isPast()) {
                throw new DomainException('Sesi tidak valid atau kedaluwarsa. Minta siswa scan ulang.');
            }
            if (DB::table('library_kiosk_pilot_checkouts')->where('scan_event_id', $session->validated_event_id)->exists()) {
                throw new DomainException('Scan ini sudah diproses. Reset sesi bila ingin meminjam buku lain.');
            }
            // All checkout paths must serialize on the catalogue row; LoanWorkflow locks the same row.
            $copy = DB::table('library_kiosk_book_copies')->where('id', $session->copy_id)->where('active', 1)->first();
            if (!$copy) throw new DomainException('Eksemplar tidak aktif.');
            $book = DB::table('buku')->where('id_buku', $copy->book_id)->lockForUpdate()->first();
            $member = DB::table('users')->where('id', $session->member_id)
                ->where('role', 'user')->where('status', 'aktif')->first();
            if (!$book || !$member || (int) $book->stock <= 0) {
                throw new DomainException('Anggota atau stok buku tidak tersedia.');
            }
            $active = DB::table('peminjaman')->where('user_id', $member->id)
                ->whereIn('status', ['menunggu', 'dipinjam'])->count();
            if ($active >= (int) config('kiosk_pilot.max_active_loans', 3)) {
                throw new DomainException('Batas maksimal buku aktif telah tercapai.');
            }
            if (DB::table('peminjaman')->where('user_id', $member->id)->where('id_buku', $book->id_buku)
                ->whereIn('status', ['menunggu', 'dipinjam'])->exists()) {
                throw new DomainException('Siswa telah memiliki pinjaman/pengajuan pada judul ini.');
            }
            if (DB::table('library_kiosk_checkout_audit as a')
                ->join('peminjaman as p', 'p.peminjaman_id', '=', 'a.loan_id')
                ->where('a.copy_id', $copy->id)->where('a.mode', 'live')
                ->whereIn('p.status', ['menunggu', 'dipinjam'])->exists()) {
                throw new DomainException('Eksemplar ini masih sedang dipinjam.');
            }
            if (DB::table('peminjaman')->where('user_id', $member->id)->where('status', 'dipinjam')
                ->whereDate('tanggal_pinjam', '<', today()->subDays((int) config('kiosk_pilot.loan_days', 7)))
                ->exists()) {
                throw new DomainException('Siswa memiliki peminjaman yang terlambat.');
            }
            // Use the existing workflow to decrement stock only once, in the same MySQL transaction.
            $workflow = app(LoanWorkflow::class);
            $loan = $workflow->request((int) $member->id, (int) $book->id_buku);
            $loan = $workflow->apply((int) $loan->peminjaman_id, 'dipinjam');
            $due = Carbon::parse($loan->tanggal_pinjam)->addDays((int) config('kiosk_pilot.loan_days', 7))->toDateString();
            DB::table('library_kiosk_checkout_audit')->insert([
                'admin_id' => $adminId, 'member_id' => $member->id, 'copy_id' => $copy->id,
                'loan_id' => $loan->peminjaman_id, 'mode' => 'live', 'created_at' => now(),
            ]);
            DB::table('library_kiosk_pilot_checkouts')->insert([
                'device_id' => $deviceId, 'scan_event_id' => $session->validated_event_id,
                'approved_by' => $adminId, 'member_id' => $member->id, 'copy_id' => $copy->id,
                'loan_id' => $loan->peminjaman_id, 'due_date' => $due, 'created_at' => now(),
            ]);
            DB::table('library_kiosk_device_sessions')->where('device_id', $deviceId)->update([
                'state' => 'borrowed', 'checkout_loan_id' => $loan->peminjaman_id,
                'checkout_due_date' => $due, 'expires_at' => now()->addSeconds(20),
                'updated_at' => now(),
            ]);
            return ['loan_id' => (int) $loan->peminjaman_id, 'already_done' => false, 'due_date' => $due];
        }, 3);
    }
}
