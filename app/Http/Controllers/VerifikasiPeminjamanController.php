<?php

namespace App\Http\Controllers;

use App\Models\VerifikasiPeminjaman;
use App\Models\Peminjaman;
use App\Models\Buku;
use App\Models\ActivityLog;
use App\Models\AnalyticsLog;
use App\Models\SearchHistory;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VerifikasiPeminjamanController extends Controller
{
    /**
     * USER - Ajukan Pinjaman
     */
    public function ajukan($id_buku)
    {
        $buku = Buku::where('id_buku', $id_buku)->firstOrFail();

        if ($buku->stock <= 0) {
            return back()->with('error', 'Stok buku tidak tersedia.');
        }

        $sudahMengajukan = VerifikasiPeminjaman::where('user_id', Auth::id())
            ->where('id_buku', $id_buku)
            ->where('status', 'menunggu')
            ->exists();

        if ($sudahMengajukan) {
            return back()->with('error', 'Kamu sudah mengajukan peminjaman buku ini.');
        }

        $pengajuan = VerifikasiPeminjaman::create([
            'user_id' => Auth::id(),
            'id_buku' => $id_buku,
            'tanggal_pengajuan' => Carbon::now(),
            'status' => 'menunggu',
        ]);

        $this->simpanActivityLog('Ajukan Verifikasi Peminjaman', 'User mengajukan peminjaman buku', [
            'verifikasi_id' => $pengajuan->id,
            'id_buku' => $buku->id_buku,
            'judul_buku' => $buku->judul,
            'status' => $pengajuan->status,
        ]);

        $this->simpanNotificationLog(
            Auth::id(),
            'Pengajuan Peminjaman Dikirim',
            'Pengajuan peminjaman buku "' . $buku->judul . '" berhasil dikirim dan menunggu persetujuan admin.',
            'peminjaman',
            [
                'verifikasi_id' => $pengajuan->id,
                'id_buku' => $buku->id_buku,
                'judul_buku' => $buku->judul,
                'status' => $pengajuan->status,
            ]
        );

        return redirect()
            ->back()
            ->with('success', 'Pengajuan peminjaman telah dikirim dan menunggu persetujuan admin.');
    }

    /**
     * ADMIN - Lihat Semua Pengajuan
     */
    public function index(Request $request)
    {
        $search = $request->search;

        if ($search) {
            $this->simpanSearchHistory(
                'Verifikasi Peminjaman',
                $search,
                'Admin mencari data verifikasi peminjaman',
                [
                    'search' => $search,
                ]
            );
        }

        $data = VerifikasiPeminjaman::with(['user', 'buku'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($user) use ($search) {
                        $user->where('nama', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('nim', 'like', "%{$search}%");
                    })
                    ->orWhereHas('buku', function ($buku) use ($search) {
                        $buku->where('judul', 'like', "%{$search}%")
                             ->orWhere('kode', 'like', "%{$search}%")
                             ->orWhere('pengarang', 'like', "%{$search}%");
                    });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $totalMenunggu = VerifikasiPeminjaman::where('status', 'menunggu')->count();
        $totalDisetujui = VerifikasiPeminjaman::where('status', 'disetujui')->count();
        $totalDitolak = VerifikasiPeminjaman::where('status', 'ditolak')->count();

        $this->simpanAnalyticsLog('Verifikasi Peminjaman', 'Admin membuka halaman verifikasi peminjaman', [
            'total_menunggu' => $totalMenunggu,
            'total_disetujui' => $totalDisetujui,
            'total_ditolak' => $totalDitolak,
        ]);

        return view('admin.peminjaman.index', compact(
            'data',
            'search',
            'totalMenunggu',
            'totalDisetujui',
            'totalDitolak'
        ));
    }

    /**
     * ADMIN - Setujui Pengajuan
     */
    public function setujui($id)
    {
        $verif = VerifikasiPeminjaman::with(['user', 'buku'])->findOrFail($id);
        $buku = Buku::where('id_buku', $verif->id_buku)->firstOrFail();

        if ($verif->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        if ($buku->stock <= 0) {
            return back()->with('error', 'Stok buku tidak tersedia.');
        }

        $buku->decrement('stock');

        $peminjaman = Peminjaman::create([
            'user_id' => $verif->user_id,
            'id_buku' => $verif->id_buku,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => null,
            'status' => 'dipinjam',
            'denda' => 0,
            'denda_dibayar' => 0,
            'status_denda' => 'tidak_ada',
            'tanggal_bayar_denda' => null,
        ]);

        $verif->update([
            'status' => 'disetujui',
        ]);

        $this->simpanActivityLog('Setujui Verifikasi Peminjaman', 'Admin menyetujui pengajuan peminjaman', [
            'verifikasi_id' => $verif->id,
            'peminjaman_id' => $peminjaman->peminjaman_id,
            'user_id_peminjam' => $verif->user_id,
            'nama_peminjam' => $verif->user->nama ?? '-',
            'id_buku' => $buku->id_buku,
            'judul_buku' => $buku->judul,
            'status_verifikasi' => $verif->status,
            'status_peminjaman' => $peminjaman->status,
        ]);

        $this->simpanNotificationLog(
            $verif->user_id,
            'Peminjaman Disetujui',
            'Peminjaman buku "' . $buku->judul . '" telah disetujui admin.',
            'peminjaman',
            [
                'verifikasi_id' => $verif->id,
                'peminjaman_id' => $peminjaman->peminjaman_id,
                'id_buku' => $buku->id_buku,
                'judul_buku' => $buku->judul,
                'status' => 'dipinjam',
                'tanggal_pinjam' => $peminjaman->tanggal_pinjam,
            ]
        );

        return back()->with('success', 'Peminjaman berhasil disetujui.');
    }

    /**
     * ADMIN - Tolak Pengajuan
     */
    public function tolak($id)
    {
        $verif = VerifikasiPeminjaman::with(['user', 'buku'])->findOrFail($id);

        if ($verif->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $verif->update([
            'status' => 'ditolak',
        ]);

        $this->simpanActivityLog('Tolak Verifikasi Peminjaman', 'Admin menolak pengajuan peminjaman', [
            'verifikasi_id' => $verif->id,
            'user_id_peminjam' => $verif->user_id,
            'nama_peminjam' => $verif->user->nama ?? '-',
            'id_buku' => $verif->id_buku,
            'judul_buku' => $verif->buku->judul ?? '-',
            'status' => $verif->status,
        ]);

        $this->simpanNotificationLog(
            $verif->user_id,
            'Peminjaman Ditolak',
            'Pengajuan peminjaman buku "' . ($verif->buku->judul ?? '-') . '" ditolak admin.',
            'peminjaman',
            [
                'verifikasi_id' => $verif->id,
                'id_buku' => $verif->id_buku,
                'judul_buku' => $verif->buku->judul ?? '-',
                'status' => $verif->status,
            ]
        );

        return back()->with('error', 'Pengajuan peminjaman ditolak.');
    }

    /**
     * USER - Status Pengajuan
     */
    public function status()
    {
        $userId = Auth::id();

        $menunggu = VerifikasiPeminjaman::where('user_id', $userId)
            ->where('status', 'menunggu')
            ->with('buku')
            ->orderBy('created_at', 'desc')
            ->get();

        $riwayat = VerifikasiPeminjaman::where('user_id', $userId)
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->with('buku')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->simpanAnalyticsLog('Status Verifikasi Peminjaman', 'User membuka status verifikasi peminjaman', [
            'total_menunggu' => $menunggu->count(),
            'total_riwayat' => $riwayat->count(),
        ]);

        return view('user.status.index', compact('menunggu', 'riwayat'));
    }

    private function simpanActivityLog(string $aktivitas, string $keterangan, array $data = []): void
    {
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'nama_user' => Auth::user()->nama ?? Auth::user()->name ?? 'User',
                'role' => Auth::user()->role ?? 'guest',
                'aktivitas' => $aktivitas,
                'keterangan' => $keterangan,
                'data' => array_merge($data, [
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]),
                'waktu' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan ActivityLog VerifikasiPeminjaman: ' . $e->getMessage());
        }
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
            Log::error('Gagal menyimpan AnalyticsLog VerifikasiPeminjaman: ' . $e->getMessage());
        }
    }

    private function simpanSearchHistory(string $halaman, string $keyword, string $keterangan, array $data = []): void
    {
        try {
            SearchHistory::create([
                'user_id' => Auth::id(),
                'nama_user' => Auth::user()->nama ?? Auth::user()->name ?? 'Guest',
                'role' => Auth::user()->role ?? 'guest',
                'keyword' => $keyword,
                'halaman' => $halaman,
                'keterangan' => $keterangan,
                'data' => $data,
                'waktu' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan SearchHistory VerifikasiPeminjaman: ' . $e->getMessage());
        }
    }

    private function simpanNotificationLog($userId, string $judul, string $pesan, string $tipe = 'peminjaman', array $data = []): void
    {
        try {
            NotificationLog::create([
                'user_id' => $userId,
                'judul' => $judul,
                'pesan' => $pesan,
                'tipe' => $tipe,
                'status' => 'belum_dibaca',
                'data' => $data,
                'waktu' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan NotificationLog VerifikasiPeminjaman: ' . $e->getMessage());
        }
    }
}