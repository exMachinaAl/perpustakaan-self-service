<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\ActivityLog;
use App\Models\AnalyticsLog;
use App\Models\SearchHistory;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    /**
     * Halaman admin: daftar semua peminjaman
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;
        $denda = $request->denda;

        if ($search || $status || $denda) {
            $this->simpanSearchHistory(
                'Admin Peminjaman',
                $search ?? '-',
                'Admin mencari/filter data peminjaman',
                [
                    'search' => $search,
                    'status' => $status,
                    'denda' => $denda,
                ]
            );
        }

        $query = Peminjaman::with(['user', 'buku'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('buku', function ($buku) use ($search) {
                        $buku->where('judul', 'like', "%{$search}%")
                            ->orWhere('kode', 'like', "%{$search}%")
                            ->orWhere('pengarang', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('nama', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($denda, function ($query) use ($denda) {
                if ($denda == 'tidak_ada') {
                    $query->where(function ($q) {
                        $q->whereNull('denda')
                          ->orWhere('denda', 0);
                    });
                }

                if ($denda == 'belum_lunas') {
                    $query->where('status_denda', 'belum_lunas');
                }

                if ($denda == 'lunas') {
                    $query->where('status_denda', 'lunas');
                }
            });

        $peminjaman = $query->latest('peminjaman_id')
            ->paginate(5)
            ->appends($request->query());

        $totalPinjam = Peminjaman::count();
        $sedangDipinjam = Peminjaman::where('status', 'dipinjam')->count();
        $dikembalikan = Peminjaman::where('status', 'dikembalikan')->count();
        $menunggu = Peminjaman::where('status', 'menunggu')->count();
        $ditolak = Peminjaman::where('status', 'ditolak')->count();

        $complianceScore = $totalPinjam > 0
            ? round(($dikembalikan / $totalPinjam) * 100)
            : 0;

        $this->simpanAnalyticsLog('Admin Peminjaman', 'Membuka halaman daftar peminjaman admin', [
            'total_pinjam' => $totalPinjam,
            'sedang_dipinjam' => $sedangDipinjam,
            'dikembalikan' => $dikembalikan,
            'menunggu' => $menunggu,
            'ditolak' => $ditolak,
            'compliance_score' => $complianceScore,
        ]);

        return view('admin.peminjaman.index', compact(
            'peminjaman',
            'search',
            'status',
            'denda',
            'totalPinjam',
            'sedangDipinjam',
            'dikembalikan',
            'menunggu',
            'ditolak',
            'complianceScore'
        ));
    }

    /**
     * User: proses ajukan pinjam buku
     */
public function store($id_buku) {
  try {$loan=app(\App\Services\LoanWorkflow::class)->request((int)Auth::id(),(int)$id_buku);}
  catch(\DomainException $e){return back()->with('error',$e->getMessage());}
  $this->simpanActivityLog('Ajukan Peminjaman','Anggota mengajukan peminjaman',['peminjaman_id'=>$loan->peminjaman_id]);
  $this->simpanNotificationLog((int)Auth::id(),'Pengajuan Peminjaman','Pengajuan diterima, menunggu verifikasi petugas.','peminjaman',['peminjaman_id'=>$loan->peminjaman_id]);
  return redirect()->route('user.peminjaman.cek')->with('success','Pengajuan berhasil dikirim.');
}

    /**
     * User: halaman status peminjaman
     */
    public function cek(Request $request)
    {
        $search = $request->search;
        $status = $request->status;

        if ($search || $status) {
            $this->simpanSearchHistory(
                'User Status Peminjaman',
                $search ?? '-',
                'User mencari/filter status peminjaman',
                [
                    'search' => $search,
                    'status' => $status,
                ]
            );
        }

        $query = Peminjaman::with('buku')
            ->where('user_id', Auth::id())
            ->when($search, function ($query) use ($search) {
                $query->whereHas('buku', function ($buku) use ($search) {
                    $buku->where('judul', 'like', "%{$search}%")
                        ->orWhere('kode', 'like', "%{$search}%")
                        ->orWhere('pengarang', 'like', "%{$search}%")
                        ->orWhere('penerbit', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            });

        $peminjaman = $query->latest('peminjaman_id')
            ->paginate(5)
            ->appends($request->query());

        $totalMenunggu = Peminjaman::where('user_id', Auth::id())
            ->where('status', 'menunggu')
            ->count();

        $totalDipinjam = Peminjaman::where('user_id', Auth::id())
            ->where('status', 'dipinjam')
            ->count();

        $totalDikembalikan = Peminjaman::where('user_id', Auth::id())
            ->where('status', 'dikembalikan')
            ->count();

        $totalDibatalkan = Peminjaman::where('user_id', Auth::id())
            ->whereIn('status', ['dibatalkan', 'ditolak'])
            ->count();

        $this->simpanAnalyticsLog('User Status Peminjaman', 'User membuka halaman status peminjaman', [
            'total_menunggu' => $totalMenunggu,
            'total_dipinjam' => $totalDipinjam,
            'total_dikembalikan' => $totalDikembalikan,
            'total_dibatalkan' => $totalDibatalkan,
        ]);

        return view('user.status.index', compact(
            'peminjaman',
            'search',
            'status',
            'totalMenunggu',
            'totalDipinjam',
            'totalDikembalikan',
            'totalDibatalkan'
        ));
    }

    /**
     * User: halaman riwayat peminjaman
     */
    public function riwayat(Request $request)
    {
        $status = $request->status;
        $dendaPerHari = 2000;

        if ($status) {
            $this->simpanSearchHistory(
                'User Riwayat Peminjaman',
                $status,
                'User memfilter riwayat peminjaman',
                [
                    'status' => $status,
                ]
            );
        }

        $query = Peminjaman::with('buku')
            ->where('user_id', Auth::id());

        if ($status == 'dikembalikan') {
            $query->where('status', 'dikembalikan');
        }

        if ($status == 'terlambat') {
            $query->whereIn('status', ['dipinjam', 'dikembalikan'])
                ->whereNotNull('tanggal_pinjam')
                ->whereRaw('COALESCE(tanggal_kembali, NOW()) > DATE_ADD(tanggal_pinjam, INTERVAL 7 DAY)');
        }

        $riwayat = $query->latest('peminjaman_id')
            ->paginate(5)
            ->appends($request->query());

        $totalPeminjaman = Peminjaman::where('user_id', Auth::id())->count();

        $dendaTertunggak = Peminjaman::where('user_id', Auth::id())
            ->where('status_denda', 'belum_lunas')
            ->sum(DB::raw('GREATEST(denda - denda_dibayar, 0)'));

        $this->simpanAnalyticsLog('User Riwayat Peminjaman', 'User membuka halaman riwayat peminjaman', [
            'total_peminjaman' => $totalPeminjaman,
            'denda_tertunggak' => $dendaTertunggak,
            'denda_per_hari' => $dendaPerHari,
            'filter_status' => $status,
        ]);

        return view('user.riwayat.index', compact(
            'riwayat',
            'status',
            'totalPeminjaman',
            'dendaTertunggak',
            'dendaPerHari'
        ));
    }

    /**
     * User: detail peminjaman
     */
    public function show($peminjaman_id)
    {
        $peminjaman = Peminjaman::with(['buku', 'user'])
            ->where('peminjaman_id', $peminjaman_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->simpanAnalyticsLog('Detail Peminjaman User', 'User melihat detail peminjaman', [
            'peminjaman_id' => $peminjaman->peminjaman_id,
            'id_buku' => $peminjaman->id_buku,
            'judul_buku' => $peminjaman->buku->judul ?? '-',
            'status' => $peminjaman->status,
        ]);

        return view('user.peminjaman.show', compact('peminjaman'));
    }

    /**
     * User: batalkan pengajuan sebelum disetujui admin
     */
public function tidakJadi($peminjaman_id) {
  $loan=Peminjaman::whereKey($peminjaman_id)->where('user_id',Auth::id())->firstOrFail();
  try {$loan=app(\App\Services\LoanWorkflow::class)->apply((int)$loan->peminjaman_id,'dibatalkan');}
  catch(\DomainException $e){return back()->with('error',$e->getMessage());}
  $this->simpanActivityLog('Batalkan Peminjaman','Anggota membatalkan pengajuan',['peminjaman_id'=>$loan->peminjaman_id]);
  return redirect()->route('user.peminjaman.cek')->with('success','Pengajuan dibatalkan.');
}

    /**
     * Admin: halaman verifikasi peminjaman
     */
    public function verifikasi(Request $request)
    {
        $search = $request->search;

        if ($search) {
            $this->simpanSearchHistory(
                'Admin Verifikasi Peminjaman',
                $search,
                'Admin mencari data verifikasi peminjaman',
                [
                    'search' => $search,
                ]
            );
        }

        $query = Peminjaman::with(['user', 'buku'])
            ->where('status', 'menunggu')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('buku', function ($buku) use ($search) {
                        $buku->where('judul', 'like', "%{$search}%")
                            ->orWhere('kode', 'like', "%{$search}%")
                            ->orWhere('pengarang', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('nama', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
                });
            });

        $peminjaman = $query->latest('peminjaman_id')
            ->paginate(10)
            ->appends($request->query());

        $totalMenunggu = Peminjaman::where('status', 'menunggu')->count();

        $this->simpanAnalyticsLog('Admin Verifikasi Peminjaman', 'Admin membuka halaman verifikasi peminjaman', [
            'total_menunggu' => $totalMenunggu,
        ]);

        return view('admin.verifikasi.index', compact(
            'peminjaman',
            'search',
            'totalMenunggu'
        ));
    }

    /**
     * Admin: ubah status peminjaman manual
     */
public function updateStatus(Request $request,$peminjaman_id) {
  $data=$request->validate(['status'=>'required|in:dipinjam,dikembalikan,ditolak,dibatalkan']);
  try {$loan=app(\App\Services\LoanWorkflow::class)->apply((int)$peminjaman_id,$data['status']);}
  catch(\DomainException $e){return back()->with('error',$e->getMessage());}
  $this->simpanActivityLog('Ubah Status Peminjaman','Admin mengubah status',['peminjaman_id'=>$loan->peminjaman_id,'status'=>$loan->status]);
  $this->simpanNotificationLog($loan->user_id,'Status Peminjaman','Status berubah menjadi '.$loan->status,'peminjaman',['peminjaman_id'=>$loan->peminjaman_id]);
  return back()->with('success','Status peminjaman diperbarui.');
}

    /**
     * Admin: tombol khusus setujui
     */
public function setujui($peminjaman_id) {
  try {$loan=app(\App\Services\LoanWorkflow::class)->apply((int)$peminjaman_id,'dipinjam');}
  catch(\DomainException $e){return back()->with('error',$e->getMessage());}
  $this->simpanActivityLog('Setujui Peminjaman','Admin menyetujui pinjaman',['peminjaman_id'=>$loan->peminjaman_id]);
  $this->simpanNotificationLog($loan->user_id,'Peminjaman Disetujui','Bukti peminjaman sudah tersedia.','peminjaman',['peminjaman_id'=>$loan->peminjaman_id]);
  return back()->with('success','Peminjaman disetujui.');
}

    /**
     * Admin: kembalikan buku + hitung denda
     */
public function tolak($peminjaman_id) {
  try {$loan=app(\App\Services\LoanWorkflow::class)->apply((int)$peminjaman_id,'ditolak');}
  catch(\DomainException $e){return back()->with('error',$e->getMessage());}
  $this->simpanActivityLog('Tolak Peminjaman','Admin menolak pengajuan',['peminjaman_id'=>$loan->peminjaman_id]);
  $this->simpanNotificationLog($loan->user_id,'Peminjaman Ditolak','Pengajuan ditolak petugas.','peminjaman',['peminjaman_id'=>$loan->peminjaman_id]);
  return back()->with('success','Pengajuan ditolak.');
}

public function adminKembalikan($peminjaman_id) {
  try {$loan=app(\App\Services\LoanWorkflow::class)->apply((int)$peminjaman_id,'dikembalikan');}
  catch(\DomainException $e){return back()->with('error',$e->getMessage());}
  $this->simpanActivityLog('Kembalikan Buku','Admin mencatat pengembalian',['peminjaman_id'=>$loan->peminjaman_id,'denda'=>$loan->denda]);
  $this->simpanNotificationLog($loan->user_id,'Buku Dikembalikan','Pengembalian dicatat; denda Rp '.number_format($loan->denda,0,',','.'),'peminjaman',['peminjaman_id'=>$loan->peminjaman_id]);
  return redirect()->route('admin.peminjaman.index')->with('success','Buku berhasil dikembalikan.');
}

    /**
     * Admin: bayar denda sebagian / penuh
     */
public function bayarDenda(Request $request,$peminjaman_id) {
  $data=$request->validate(['jumlah_bayar'=>'required|integer|min:1']);
  try {$loan=app(\App\Services\LoanWorkflow::class)->payFine((int)$peminjaman_id,(int)$data['jumlah_bayar']);}
  catch(\DomainException $e){return back()->with('error',$e->getMessage());}
  $this->simpanActivityLog('Bayar Denda','Admin mencatat pembayaran',['peminjaman_id'=>$loan->peminjaman_id,'jumlah_bayar'=>$data['jumlah_bayar'],'status_denda'=>$loan->status_denda]);
  return back()->with('success','Pembayaran denda dicatat.');
}

    /**
     * Admin: hapus data peminjaman
     */
public function destroy($peminjaman_id) {
  try {app(\App\Services\LoanWorkflow::class)->deletePending((int)$peminjaman_id);}
  catch(\DomainException $e){return back()->with('error',$e->getMessage());}
  $this->simpanActivityLog('Hapus Pengajuan','Admin menghapus pengajuan tidak aktif',['peminjaman_id'=>$peminjaman_id]);
  return back()->with('success','Pengajuan tidak aktif dihapus.');
}

    /**
     * User: bukti peminjaman
     */
    public function bukti($id)
    {
        $peminjaman = Peminjaman::with(['user', 'buku'])
            ->where('peminjaman_id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($peminjaman->status !== 'dipinjam') {
            return redirect()
                ->route('user.peminjaman.cek')
                ->with('error', 'Bukti peminjaman hanya tersedia setelah disetujui admin.');
        }

        $this->simpanAnalyticsLog('Bukti Peminjaman', 'User membuka bukti peminjaman', [
            'peminjaman_id' => $peminjaman->peminjaman_id,
            'id_buku' => $peminjaman->id_buku,
            'judul_buku' => $peminjaman->buku->judul ?? '-',
            'status' => $peminjaman->status,
        ]);

        return view('user.status.bukti', ['peminjaman'=>$peminjaman, 'qrPayload'=>app(\App\Services\SecureLoanQr::class)->issue($peminjaman)]);
    }

    /**
     * Admin: detail peminjaman
     */
    public function adminShow($peminjaman_id)
    {
        $peminjaman = Peminjaman::with(['user', 'buku'])
            ->where('peminjaman_id', $peminjaman_id)
            ->firstOrFail();

        $this->simpanAnalyticsLog('Detail Peminjaman Admin', 'Admin melihat detail peminjaman', [
            'peminjaman_id' => $peminjaman->peminjaman_id,
            'user_id_peminjam' => $peminjaman->user_id,
            'nama_peminjam' => $peminjaman->user->nama ?? '-',
            'id_buku' => $peminjaman->id_buku,
            'judul_buku' => $peminjaman->buku->judul ?? '-',
            'status' => $peminjaman->status,
        ]);

        return view('admin.peminjaman.show', compact('peminjaman'));
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
            Log::error('Gagal menyimpan ActivityLog Peminjaman: ' . $e->getMessage());
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
            Log::error('Gagal menyimpan AnalyticsLog Peminjaman: ' . $e->getMessage());
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
            Log::error('Gagal menyimpan SearchHistory Peminjaman: ' . $e->getMessage());
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
            Log::error('Gagal menyimpan NotificationLog Peminjaman: ' . $e->getMessage());
        }
    }
}