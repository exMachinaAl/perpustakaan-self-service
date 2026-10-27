<?php

namespace App\Services;

use App\Models\Peminjaman;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

final class SelfServiceCheckout
{
    public function checkout(int $deviceId, string $requestId): array
    {
        return DB::transaction(function () use ($deviceId, $requestId) {
            $existing = DB::table('library_kiosk_self_checkouts')->where('request_id', $requestId)->first();
            if ($existing) {
                return ['loan_id' => (int) $existing->loan_id, 'due_date' => $existing->due_date, 'already_done' => true];
            }

            $device = DB::table('library_kiosk_devices')->where('id', $deviceId)->lockForUpdate()->first();
            if (! $device || ! $device->active) {
                throw new DomainException('Perangkat kiosk tidak aktif.');
            }

            $session = DB::table('library_kiosk_device_sessions')->where('device_id', $deviceId)
                ->lockForUpdate()->first();
            if (! $session || $session->state !== 'ready_checkout' || ! $session->member_id || ! $session->copy_id
                || ! $session->member_authenticated_at || ! $session->expires_at
                || Carbon::parse($session->expires_at)->isPast()) {
                throw new DomainException('Sesi belum lengkap atau sudah kedaluwarsa. Scan ulang kartu anggota dan buku.');
            }

            $copyLookup = DB::table('library_kiosk_book_copies')->where('id', $session->copy_id)->first();
            if (! $copyLookup) {
                throw new DomainException('Eksemplar tidak tersedia.');
            }
            DB::table('users')->where('id', $session->member_id)->lockForUpdate()->first();
            $book = DB::table('buku')->where('id_buku', $copyLookup->book_id)->lockForUpdate()->first();
            $copy = DB::table('library_kiosk_book_copies')->where('id', $session->copy_id)
                ->where('active', true)->lockForUpdate()->first();
            if (! $copy || $copy->status !== 'available' || (int) $copy->book_id !== (int) $book?->id_buku) {
                throw new DomainException('Eksemplar tidak tersedia.');
            }
            if (! $book || (int) $book->stock < 1) {
                throw new DomainException('Stok buku tidak tersedia.');
            }

            app(LoanPolicy::class)->assertEligible((int) $session->member_id, (int) $book->id_buku, (int) $copy->id);

            $dueDate = today()->addDays((int) config('library.loan_days', 7))->toDateString();
            $loan = Peminjaman::create([
                'user_id' => $session->member_id,
                'id_buku' => $book->id_buku,
                'copy_id' => $copy->id,
                'kiosk_device_id' => $deviceId,
                'source' => 'self_service',
                'tanggal_pinjam' => today(),
                'due_date' => $dueDate,
                'tanggal_kembali' => null,
                'status' => 'dipinjam',
                'denda' => 0,
                'denda_dibayar' => 0,
                'status_denda' => 'tidak_ada',
            ]);

            DB::table('buku')->where('id_buku', $book->id_buku)->decrement('stock');
            DB::table('library_kiosk_book_copies')->where('id', $copy->id)
                ->update(['status' => 'borrowed', 'updated_at' => now()]);
            DB::table('library_kiosk_self_checkouts')->insert([
                'request_id' => $requestId,
                'device_id' => $deviceId,
                'member_id' => $session->member_id,
                'copy_id' => $copy->id,
                'loan_id' => $loan->peminjaman_id,
                'due_date' => $dueDate,
                'created_at' => now(),
            ]);
            DB::table('library_kiosk_device_sessions')->where('device_id', $deviceId)->update([
                'state' => 'borrowed',
                'checkout_loan_id' => $loan->peminjaman_id,
                'checkout_due_date' => $dueDate,
                'expires_at' => now()->addSeconds(20),
                'updated_at' => now(),
            ]);

            return ['loan_id' => (int) $loan->peminjaman_id, 'due_date' => $dueDate, 'already_done' => false];
        }, 3);
    }
}
