<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\AnalyticsLog;
use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProdiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $fakultas = $request->fakultas;
        $status = $request->status;

        if ($search || $fakultas || $status) {
            $this->simpanSearchHistory(
                'Program Studi',
                $search ?? '-',
                'Admin mencari/filter data program studi',
                [
                    'search' => $search,
                    'fakultas' => $fakultas,
                    'status' => $status,
                ]
            );
        }

        $prodi = Prodi::query()
            ->withCount([
                'users as jumlah_anggota' => function ($query) {
                    $query->where('role', 'user');
                }
            ])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('kode_prodi', 'LIKE', "%{$search}%")
                      ->orWhere('fakultas', 'LIKE', "%{$search}%");
                });
            })
            ->when($fakultas, function ($query, $fakultas) {
                $query->where('fakultas', $fakultas);
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        $totalProdi = Prodi::count();

        $totalFakultas = Prodi::whereNotNull('fakultas')
            ->where('fakultas', '!=', '')
            ->distinct('fakultas')
            ->count('fakultas');

        $prodiAktif = Prodi::where('status', 'aktif')->count();
        $prodiNonaktif = Prodi::where('status', 'nonaktif')->count();

        $this->simpanAnalyticsLog('Program Studi', 'Membuka halaman program studi', [
            'total_prodi' => $totalProdi,
            'total_fakultas' => $totalFakultas,
            'prodi_aktif' => $prodiAktif,
            'prodi_nonaktif' => $prodiNonaktif,
        ]);

        return view('admin.programstudi.index', compact(
            'prodi',
            'search',
            'fakultas',
            'status',
            'totalProdi',
            'totalFakultas',
            'prodiAktif',
            'prodiNonaktif'
        ));
    }

    public function create()
    {
        return view('admin.programstudi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_prodi' => 'nullable|string|max:50|unique:prodi,kode_prodi',
            'nama' => 'required|string|max:255|unique:prodi,nama',
            'fakultas' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $prodi = Prodi::create([
            'kode_prodi' => $request->kode_prodi,
            'nama' => $request->nama,
            'fakultas' => $request->fakultas,
            'status' => $request->status,
        ]);

        $this->simpanActivityLog('Tambah Program Studi', 'Admin menambahkan program studi baru', [
            'prodi_id' => $prodi->id,
            'kode_prodi' => $prodi->kode_prodi,
            'nama' => $prodi->nama,
            'fakultas' => $prodi->fakultas,
            'status' => $prodi->status,
        ]);

        return redirect()
            ->route('admin.program-studi.index')
            ->with('success', 'Prodi berhasil ditambahkan');
    }

    public function show($id)
    {
        $prodi = Prodi::withCount('users')->findOrFail($id);

        $this->simpanAnalyticsLog('Detail Program Studi', 'Admin melihat detail program studi', [
            'prodi_id' => $prodi->id,
            'kode_prodi' => $prodi->kode_prodi,
            'nama' => $prodi->nama,
            'fakultas' => $prodi->fakultas,
            'status' => $prodi->status,
            'jumlah_anggota' => $prodi->users_count,
        ]);

        return view('admin.programstudi.show', compact('prodi'));
    }

    public function edit($id)
    {
        $prodi = Prodi::findOrFail($id);

        return view('admin.programstudi.edit', compact('prodi'));
    }

    public function update(Request $request, $id)
    {
        $prodi = Prodi::findOrFail($id);

        $request->validate([
            'kode_prodi' => 'nullable|string|max:50|unique:prodi,kode_prodi,' . $prodi->id,
            'nama' => 'required|string|max:255|unique:prodi,nama,' . $prodi->id,
            'fakultas' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $dataLama = $prodi->only([
            'kode_prodi',
            'nama',
            'fakultas',
            'status',
        ]);

        $prodi->update([
            'kode_prodi' => $request->kode_prodi,
            'nama' => $request->nama,
            'fakultas' => $request->fakultas,
            'status' => $request->status,
        ]);

        $this->simpanActivityLog('Update Program Studi', 'Admin memperbarui data program studi', [
            'prodi_id' => $prodi->id,
            'data_lama' => $dataLama,
            'data_baru' => [
                'kode_prodi' => $prodi->kode_prodi,
                'nama' => $prodi->nama,
                'fakultas' => $prodi->fakultas,
                'status' => $prodi->status,
            ],
        ]);

        return redirect()
            ->route('admin.program-studi.index')
            ->with('success', 'Prodi berhasil diupdate');
    }

    public function destroy($id)
    {
        $prodi = Prodi::withCount('users')->findOrFail($id);

        if ($prodi->users_count > 0) {
            return back()->with('error', 'Prodi tidak bisa dihapus karena masih digunakan oleh anggota.');
        }

        $dataProdi = [
            'prodi_id' => $prodi->id,
            'kode_prodi' => $prodi->kode_prodi,
            'nama' => $prodi->nama,
            'fakultas' => $prodi->fakultas,
            'status' => $prodi->status,
        ];

        $prodi->delete();

        $this->simpanActivityLog('Hapus Program Studi', 'Admin menghapus data program studi', $dataProdi);

        return redirect()
            ->route('admin.program-studi.index')
            ->with('success', 'Prodi berhasil dihapus');
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
            Log::error('Gagal menyimpan ActivityLog Prodi: ' . $e->getMessage());
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
            Log::error('Gagal menyimpan AnalyticsLog Prodi: ' . $e->getMessage());
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
            Log::error('Gagal menyimpan SearchHistory Prodi: ' . $e->getMessage());
        }
    }
}