<?php

namespace App\Services;

use DomainException;
use Illuminate\Support\Facades\DB;

final class LoanPolicy
{
    public function assertEligible(int $userId, int $bookId, ?int $copyId = null, ?int $excludeLoanId = null): void
    {
        $member = DB::table('users')->where('id', $userId)
            ->where('role', 'user')->where('status', 'aktif')->first();
        if (! $member) {
            throw new DomainException('Akun anggota tidak aktif.');
        }

        $activeQuery = DB::table('peminjaman')->where('user_id', $userId)
            ->whereIn('status', ['menunggu', 'dipinjam']);
        if ($excludeLoanId) {
            $activeQuery->where('peminjaman_id', '!=', $excludeLoanId);
        }
        $active = $activeQuery->count();
        if ($active >= (int) config('library.max_active_loans', 3)) {
            throw new DomainException('Batas maksimal pinjaman aktif telah tercapai.');
        }

        $sameTitle = DB::table('peminjaman')->where('user_id', $userId)->where('id_buku', $bookId)
            ->whereIn('status', ['menunggu', 'dipinjam']);
        if ($excludeLoanId) {
            $sameTitle->where('peminjaman_id', '!=', $excludeLoanId);
        }
        if ($sameTitle->exists()) {
            throw new DomainException('Anggota sudah mengajukan atau meminjam judul ini.');
        }

        $sameCopy = $copyId ? DB::table('peminjaman')->where('copy_id', $copyId)
            ->whereIn('status', ['menunggu', 'dipinjam']) : null;
        if ($sameCopy && $excludeLoanId) {
            $sameCopy->where('peminjaman_id', '!=', $excludeLoanId);
        }
        if ($sameCopy && $sameCopy->exists()) {
            throw new DomainException('Eksemplar ini sedang dipinjam.');
        }

        if (DB::table('peminjaman')->where('user_id', $userId)->where('status', 'dipinjam')
            ->where(function ($query) {
                $query->whereDate('due_date', '<', today())
                    ->orWhere(function ($legacy) {
                        $legacy->whereNull('due_date')->whereDate('tanggal_pinjam', '<', today()->subDays((int) config('library.loan_days', 7)));
                    });
            })->exists()) {
            throw new DomainException('Ada peminjaman terlambat yang belum diselesaikan.');
        }
    }
}
