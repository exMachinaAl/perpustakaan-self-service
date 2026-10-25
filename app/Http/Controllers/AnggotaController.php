<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Prodi;
use App\Models\ActivityLog;
use App\Models\AnalyticsLog;
use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $prodiId = $request->prodi_id;
        $status = $request->status;

        if ($search || $prodiId || $status) {
            $this->simpanSearchHistory(
                'Data Anggota',
                $search ?? '-',
                'Admin mencari/filter data anggota',
                [
                    'search' => $search,
                    'prodi_id' => $prodiId,
                    'status' => $status,
                ]
            );
        }

        $anggota = User::with('prodi')
            ->withCount('peminjaman')
            ->where('role', 'user')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nim', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($prodiId, function ($query) use ($prodiId) {
                $query->where('prodi_id', $prodiId);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        $totalAnggota = User::where('role', 'user')->count();

        $anggotaAktif = User::where('role', 'user')
            ->where('status', 'aktif')
            ->count();

        $anggotaTidakAktif = User::where('role', 'user')
            ->where('status', 'tidak_aktif')
            ->count();

        $anggotaDiblokir = User::where('role', 'user')
            ->where('status', 'diblokir')
            ->count();

        $anggotaBaru = User::where('role', 'user')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $prodiList = Prodi::orderBy('nama', 'asc')->get();

        $this->simpanAnalyticsLog('Data Anggota', 'Membuka halaman data anggota', [
            'total_anggota' => $totalAnggota,
            'anggota_aktif' => $anggotaAktif,
            'anggota_tidak_aktif' => $anggotaTidakAktif,
            'anggota_diblokir' => $anggotaDiblokir,
            'anggota_baru_bulan_ini' => $anggotaBaru,
        ]);

        return view('admin.anggota.index', compact(
            'anggota',
            'prodiList',
            'search',
            'prodiId',
            'status',
            'totalAnggota',
            'anggotaAktif',
            'anggotaTidakAktif',
            'anggotaDiblokir',
            'anggotaBaru'
        ));
    }

    public function create()
    {
        $prodiList = Prodi::orderBy('nama', 'asc')->get();

        return view('admin.anggota.create', compact('prodiList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|max:50|unique:users,nim',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:10',
            'prodi_id' => 'nullable|exists:prodi,id',
            'status' => 'required|in:aktif,tidak_aktif,diblokir',
        ]);

        $anggota = User::create([
            'nama' => $request->nama,
            'nim' => $request->nim,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => $request->status,
            'prodi_id' => $request->prodi_id,
        ]);

        $this->simpanActivityLog('Tambah Anggota', 'Admin menambahkan anggota baru', [
            'anggota_id' => $anggota->id,
            'nama' => $anggota->nama,
            'nim' => $anggota->nim,
            'email' => $anggota->email,
            'status' => $anggota->status,
            'prodi_id' => $anggota->prodi_id,
        ]);

        return redirect()
            ->route('admin.anggota.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function show($id)
    {
        $anggota = User::with(['prodi', 'peminjaman'])
            ->withCount('peminjaman')
            ->where('role', 'user')
            ->findOrFail($id);

        $this->simpanAnalyticsLog('Detail Anggota', 'Melihat detail anggota', [
            'anggota_id' => $anggota->id,
            'nama' => $anggota->nama,
            'nim' => $anggota->nim,
            'email' => $anggota->email,
            'jumlah_peminjaman' => $anggota->peminjaman_count,
        ]);

        return view('admin.anggota.show', compact('anggota'));
    }

    public function edit($id)
    {
        $anggota = User::where('role', 'user')->findOrFail($id);
        $prodiList = Prodi::orderBy('nama', 'asc')->get();

        return view('admin.anggota.edit', compact('anggota', 'prodiList'));
    }

    public function update(Request $request, $id)
    {
        $anggota = User::where('role', 'user')->findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|max:50|unique:users,nim,' . $anggota->id,
            'email' => 'required|email|unique:users,email,' . $anggota->id,
            'password' => 'nullable|min:10',
            'prodi_id' => 'nullable|exists:prodi,id',
            'status' => 'required|in:aktif,tidak_aktif,diblokir',
        ]);

        $dataLama = $anggota->only([
            'nama',
            'nim',
            'email',
            'prodi_id',
            'status',
        ]);

        $anggota->nama = $request->nama;
        $anggota->nim = $request->nim;
        $anggota->email = $request->email;
        $anggota->prodi_id = $request->prodi_id;
        $anggota->status = $request->status;

        if ($request->filled('password')) {
            $anggota->password = Hash::make($request->password);
        }

        $anggota->save();

        $this->simpanActivityLog('Update Anggota', 'Admin memperbarui data anggota', [
            'anggota_id' => $anggota->id,
            'data_lama' => $dataLama,
            'data_baru' => [
                'nama' => $anggota->nama,
                'nim' => $anggota->nim,
                'email' => $anggota->email,
                'prodi_id' => $anggota->prodi_id,
                'status' => $anggota->status,
                'password_diubah' => $request->filled('password'),
            ],
        ]);

        return redirect()
            ->route('admin.anggota.index')
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        $anggota = User::where('role', 'user')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:aktif,tidak_aktif,diblokir',
        ]);

        $statusLama = $anggota->status;

        $anggota->update([
            'status' => $request->status,
        ]);

        $this->simpanActivityLog('Update Status Anggota', 'Admin mengubah status anggota', [
            'anggota_id' => $anggota->id,
            'nama' => $anggota->nama,
            'nim' => $anggota->nim,
            'status_lama' => $statusLama,
            'status_baru' => $request->status,
        ]);

        return back()->with('success', 'Status anggota berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $anggota = User::withCount('peminjaman')
            ->where('role', 'user')
            ->findOrFail($id);

        if ($anggota->peminjaman_count > 0) {
            return back()->with('error', 'Anggota tidak bisa dihapus karena sudah memiliki riwayat peminjaman.');
        }

        $dataAnggota = [
            'anggota_id' => $anggota->id,
            'nama' => $anggota->nama,
            'nim' => $anggota->nim,
            'email' => $anggota->email,
            'status' => $anggota->status,
            'prodi_id' => $anggota->prodi_id,
        ];

        $anggota->delete();

        $this->simpanActivityLog('Hapus Anggota', 'Admin menghapus data anggota', $dataAnggota);

        return redirect()
            ->route('admin.anggota.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }

    public function export(): StreamedResponse
    {
        $fileName = 'data-anggota.csv';

        $anggota = User::with('prodi')
            ->withCount('peminjaman')
            ->where('role', 'user')
            ->orderBy('nama')
            ->get();

        $this->simpanActivityLog('Export Data Anggota', 'Admin mengekspor data anggota ke CSV', [
            'nama_file' => $fileName,
            'total_data' => $anggota->count(),
        ]);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        return response()->stream(function () use ($anggota) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Nama',
                'NIM',
                'Program Studi',
                'Email',
                'Jumlah Pinjaman',
                'Status',
                'Tanggal Daftar',
            ]);

            foreach ($anggota as $item) {
                fputcsv($file, [
                    $item->nama,
                    $item->nim,
                    $item->prodi->nama ?? '-',
                    $item->email,
                    $item->peminjaman_count,
                    $item->status,
                    $item->created_at ? $item->created_at->format('d-m-Y') : '-',
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }

    private function simpanActivityLog(string $aktivitas, string $keterangan, array $data = []): void
    {
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'nama_user' => Auth::user()->nama ?? Auth::user()->name ?? 'Admin',
                'role' => Auth::user()->role ?? 'admin',
                'aktivitas' => $aktivitas,
                'keterangan' => $keterangan,
                'data' => array_merge($data, [
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]),
                'waktu' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan ActivityLog Anggota: ' . $e->getMessage());
        }
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
            Log::error('Gagal menyimpan AnalyticsLog Anggota: ' . $e->getMessage());
        }
    }

    private function simpanSearchHistory(string $halaman, string $keyword, string $keterangan, array $data = []): void
    {
        try {
            SearchHistory::create([
                'user_id' => Auth::id(),
                'nama_user' => Auth::user()->nama ?? Auth::user()->name ?? 'Admin',
                'role' => Auth::user()->role ?? 'admin',
                'keyword' => $keyword,
                'halaman' => $halaman,
                'keterangan' => $keterangan,
                'data' => $data,
                'waktu' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan SearchHistory Anggota: ' . $e->getMessage());
        }
    }
}