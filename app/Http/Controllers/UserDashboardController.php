<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\AnalyticsLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $bukuDipinjam = Peminjaman::where('user_id', $user->id)
            ->where('status', 'dipinjam')
            ->count();

        $menungguKonfirmasi = Peminjaman::where('user_id', $user->id)
            ->whereIn('status', ['menunggu', 'pending', 'menunggu_konfirmasi'])
            ->count();

        $riwayatPeminjaman = Peminjaman::where('user_id', $user->id)
            ->count();

        $bukuTersedia = Buku::sum('stock');

        $rekomendasiBuku = Buku::latest('id_buku')
            ->take(4)
            ->get();

        $peminjamanTerbaru = Peminjaman::with('buku')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $this->simpanAnalyticsLog('User Dashboard', 'Membuka Dashboard User', [
            'buku_dipinjam' => $bukuDipinjam,
            'menunggu_konfirmasi' => $menungguKonfirmasi,
            'riwayat_peminjaman' => $riwayatPeminjaman,
            'buku_tersedia' => $bukuTersedia,
        ]);

        return view('user.dashboard.index', compact(
            'user',
            'bukuDipinjam',
            'menungguKonfirmasi',
            'riwayatPeminjaman',
            'bukuTersedia',
            'rekomendasiBuku',
            'peminjamanTerbaru'
        ));
    }

    private function simpanAnalyticsLog(string $halaman, string $aksi, array $data = []): void
    {
        try {
            AnalyticsLog::create([
                'user_id' => Auth::id(),
                'role' => Auth::user()->role ?? 'user',
                'halaman' => $halaman,
                'aksi' => $aksi,
                'data' => $data,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'waktu' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan AnalyticsLog User Dashboard: ' . $e->getMessage());
        }
    }
}