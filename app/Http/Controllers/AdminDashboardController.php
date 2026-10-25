<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\User;
use App\Models\Peminjaman;
use App\Models\AnalyticsLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalBuku = Buku::count();
        $anggotaAktif = User::where('role', 'user')->count();
        $sedangDipinjam = Peminjaman::where('status', 'dipinjam')->count();
        $bukuTersedia = Buku::sum('stock');

        $peminjamanTerbaru = Peminjaman::with(['user', 'buku'])
            ->latest('peminjaman_id')
            ->take(5)
            ->get();

        $aktivitasUser = collect();

        // User baru daftar
        $userBaru = User::where('role', 'user')
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'register',
                    'nama' => $user->nama ?? $user->name ?? 'User',
                    'aktivitas' => 'mendaftar sebagai anggota perpustakaan.',
                    'detail' => 'Email: ' . ($user->email ?? '-'),
                    'created_at' => $user->created_at,
                    'waktu' => $user->created_at ? Carbon::parse($user->created_at)->diffForHumans() : '-',
                ];
            });

        // Peminjaman user
        $pinjamAktivitas = Peminjaman::with(['user', 'buku'])
            ->latest('tanggal_pinjam')
            ->take(6)
            ->get()
            ->map(function ($pinjam) {
                return [
                    'type' => 'pinjam',
                    'nama' => $pinjam->user->nama ?? $pinjam->user->name ?? 'User',
                    'aktivitas' => 'meminjam buku.',
                    'detail' => 'Judul: ' . ($pinjam->buku->judul ?? '-'),
                    'created_at' => $pinjam->tanggal_pinjam ?? $pinjam->created_at,
                    'waktu' => ($pinjam->tanggal_pinjam || $pinjam->created_at)
                        ? Carbon::parse($pinjam->tanggal_pinjam ?? $pinjam->created_at)->diffForHumans()
                        : '-',
                ];
            });

        // Pengembalian user
        $kembaliAktivitas = Peminjaman::with(['user', 'buku'])
            ->whereNotNull('tanggal_kembali')
            ->latest('tanggal_kembali')
            ->take(4)
            ->get()
            ->map(function ($pinjam) {
                return [
                    'type' => 'kembali',
                    'nama' => $pinjam->user->nama ?? $pinjam->user->name ?? 'User',
                    'aktivitas' => 'mengembalikan buku.',
                    'detail' => 'Judul: ' . ($pinjam->buku->judul ?? '-'),
                    'created_at' => $pinjam->tanggal_kembali,
                    'waktu' => $pinjam->tanggal_kembali
                        ? Carbon::parse($pinjam->tanggal_kembali)->diffForHumans()
                        : '-',
                ];
            });

        $aktivitasUser = $aktivitasUser
            ->merge($userBaru)
            ->merge($pinjamAktivitas)
            ->merge($kembaliAktivitas)
            ->sortByDesc('created_at')
            ->take(8)
            ->values();

        $this->simpanAnalyticsLog('Admin Dashboard', 'Membuka Dashboard Admin', [
            'total_buku' => $totalBuku,
            'anggota_aktif' => $anggotaAktif,
            'sedang_dipinjam' => $sedangDipinjam,
            'buku_tersedia' => $bukuTersedia,
        ]);

        return view('admin.dashboard.index', compact(
            'totalBuku',
            'anggotaAktif',
            'sedangDipinjam',
            'bukuTersedia',
            'peminjamanTerbaru',
            'aktivitasUser'
        ));
    }

    private function simpanAnalyticsLog(string $halaman, string $aksi, array $data = []): void
    {
        try {
            AnalyticsLog::create([
                'user_id' => Auth::id(),
                'role' => Auth::user()->role ?? 'admin',
                'halaman' => $halaman,
                'aksi' => $aksi,
                'data' => $data,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'waktu' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan AnalyticsLog Admin Dashboard: ' . $e->getMessage());
        }
    }
}