<?php
namespace App\Services;

use DomainException;
use Illuminate\Support\Facades\DB;

/** Hanya pengembalian PILOT yang diverifikasi fisik oleh admin; bukan self-return otomatis. */
final class KioskPilotReturn
{
    public function receive(int $loanId, int $adminId, bool $physicallyReceived, ?string $condition): array
    {
        if (app()->environment('production')) {
            throw new DomainException('Pengembalian pilot tidak dapat dioperasikan pada APP_ENV=production.');
        }
        if (!$physicallyReceived) {
            throw new DomainException('Petugas wajib menerima dan memeriksa buku fisik sebelum menyelesaikan pengembalian.');
        }
        $condition = $condition === null ? null : trim($condition);
        if ($condition !== null && mb_strlen($condition) > 255) {
            throw new DomainException('Catatan kondisi terlalu panjang.');
        }
        return DB::transaction(function () use ($loanId, $adminId, $condition) {
            $pilot = DB::table('library_kiosk_pilot_checkouts')->where('loan_id', $loanId)->first();
            if (!$pilot) throw new DomainException('Pinjaman bukan dari pilot kiosk. Gunakan alur pengembalian reguler.');
            $copy = DB::table('library_kiosk_book_copies')->where('id', $pilot->copy_id)->first();
            if (!$copy) throw new DomainException('Identitas eksemplar pinjaman tidak ditemukan.');
            // Kunci katalog terlebih dahulu, sama seperti LoanWorkflow, untuk mencegah race/deadlock.
            $book = DB::table('buku')->where('id_buku', $copy->book_id)->lockForUpdate()->first();
            if (!$book) throw new DomainException('Buku katalog tidak ditemukan.');
            $loan = DB::table('peminjaman')->where('peminjaman_id', $loanId)->lockForUpdate()->first();
            if (!$loan) throw new DomainException('Transaksi pinjaman tidak ditemukan.');
            $existing = DB::table('library_kiosk_pilot_returns')->where('loan_id', $loanId)->first();
            if ($existing) {
                if ($loan->status !== 'dikembalikan') throw new DomainException('Audit pengembalian tidak cocok dengan status pinjaman.');
                return ['already_done' => true, 'loan_id' => $loanId, 'received_at' => $existing->received_at];
            }
            if ((int) $loan->user_id !== (int) $pilot->member_id || (int) $loan->id_buku !== (int) $copy->book_id) {
                throw new DomainException('Data peminjam atau eksemplar tidak cocok.');
            }
            if ($loan->status !== 'dipinjam') throw new DomainException('Buku belum berstatus dipinjam atau sudah dikembalikan melalui jalur lain.');
            app(LoanWorkflow::class)->apply($loanId, 'dikembalikan');
            DB::table('library_kiosk_pilot_returns')->insert([
                'loan_id' => $loanId, 'copy_id' => $copy->id, 'accepted_by' => $adminId,
                'physical_condition' => $condition ?: null, 'received_at' => now(),
            ]);
            return ['already_done' => false, 'loan_id' => $loanId, 'received_at' => now()->toDateTimeString()];
        }, 3);
    }
}
