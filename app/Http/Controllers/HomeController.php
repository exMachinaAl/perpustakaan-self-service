<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\User;
use App\Models\Peminjaman;
use App\Models\AnalyticsLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        $totalBuku = Buku::count();

        $anggotaAktif = User::where('role', 'user')->count();

        $bukuDipinjam = Peminjaman::whereIn('status', ['dipinjam', 'menunggu'])->count();

        $bukuTersedia = Buku::where('stock', '>', 0)->sum('stock');

        $this->simpanAnalyticsLog('Home', 'Membuka halaman home', [
            'total_buku' => $totalBuku,
            'anggota_aktif' => $anggotaAktif,
            'buku_dipinjam' => $bukuDipinjam,
            'buku_tersedia' => $bukuTersedia,
        ]);

        return view('Home', compact(
            'totalBuku',
            'anggotaAktif',
            'bukuDipinjam',
            'bukuTersedia'
        ));
    }

    private function simpanAnalyticsLog(string $halaman, string $aksi, array $data = []): void
    {
        try {
            AnalyticsLog::create([
                'user_id' => Auth::id(),
                'role' => Auth::user()->role ?? 'guest',
                'halaman' => $halaman,
                'aksi' => $aksi,
                'data' => $data,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'waktu' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan AnalyticsLog Home: ' . $e->getMessage());
        }
    }
}
