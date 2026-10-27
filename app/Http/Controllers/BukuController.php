<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AnalyticsLog;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\SearchHistory;
use App\Services\BookInventory;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $kategori = $request->kategori;
        $stok = $request->stok;

        if ($search || $kategori || $stok) {
            $this->simpanSearchHistory(
                'Admin Data Buku',
                $search ?? '-',
                'Admin mencari/filter data buku',
                [
                    'search' => $search,
                    'kategori' => $kategori,
                    'stok' => $stok,
                ]
            );
        }

        $buku = Buku::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', '%'.$search.'%')
                        ->orWhere('kode', 'like', '%'.$search.'%')
                        ->orWhere('pengarang', 'like', '%'.$search.'%')
                        ->orWhere('penerbit', 'like', '%'.$search.'%');
                });
            })
            ->when($kategori, function ($query) use ($kategori) {
                $query->where('kategori', $kategori);
            })
            ->when($stok == 'rendah', function ($query) {
                $query->where('stock', '<=', 3);
            })
            ->when($stok == 'aman', function ($query) {
                $query->where('stock', '>', 3);
            })
            ->latest('id_buku')
            ->paginate(5)
            ->appends($request->query());

        $totalBuku = Buku::count();

        $totalKategori = Buku::whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct('kategori')
            ->count('kategori');

        $stokRendah = Buku::where('stock', '<=', 3)->count();

        $totalEksemplar = Buku::sum('stock');

        $daftarKategori = Buku::whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        $this->simpanAnalyticsLog('Admin Data Buku', 'Membuka halaman data buku admin', [
            'total_buku' => $totalBuku,
            'total_kategori' => $totalKategori,
            'stok_rendah' => $stokRendah,
            'total_eksemplar' => $totalEksemplar,
        ]);

        return view('admin.databuku.index', compact(
            'buku',
            'search',
            'kategori',
            'stok',
            'totalBuku',
            'totalKategori',
            'stokRendah',
            'totalEksemplar',
            'daftarKategori'
        ));
    }

    public function create()
    {
        return view('admin.databuku.create');
    }

    public function store(Request $request, BookInventory $inventory)
    {
        $request->validate([
            'kode' => 'required|string|max:100|unique:buku,kode',
            'isbn' => 'nullable|string|max:20',
            'barcodes' => 'nullable|string|max:5000',
            'judul' => 'required',
            'pengarang' => 'required',
            'penerbit' => 'required',
            'tahun_terbit' => 'required',
            'kategori' => 'required',
            'stock' => 'required|integer|min:0',
            'lokasi' => 'required',
            'deskripsi' => 'nullable',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $coverPath = null;

        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('cover-buku', 'public');
        }

        try {
            $buku = DB::transaction(function () use ($request, $coverPath, $inventory) {
                $book = Buku::create([
                    'kode' => $request->kode,
                    'isbn' => $request->filled('isbn') ? $inventory->normalizeIsbn($request->isbn) : null,
                    'judul' => $request->judul,
                    'pengarang' => $request->pengarang,
                    'penerbit' => $request->penerbit,
                    'tahun_terbit' => $request->tahun_terbit,
                    'kategori' => $request->kategori,
                    'stock' => $request->stock,
                    'lokasi' => $request->lokasi,
                    'deskripsi' => $request->deskripsi,
                    'cover' => $coverPath,
                ]);
                $inventory->assignBarcodes($book, preg_split('/\R+/', (string) $request->barcodes));

                return $book;
            }, 3);
        } catch (DomainException $error) {
            if ($coverPath && Storage::disk('public')->exists($coverPath)) {
                Storage::disk('public')->delete($coverPath);
            }

            return back()->withErrors(['barcodes' => $error->getMessage()])->withInput();
        }

        $this->simpanActivityLog('Tambah Buku', 'Admin menambahkan buku baru', [
            'id_buku' => $buku->id_buku,
            'kode' => $buku->kode,
            'judul' => $buku->judul,
            'pengarang' => $buku->pengarang,
            'penerbit' => $buku->penerbit,
            'tahun_terbit' => $buku->tahun_terbit,
            'kategori' => $buku->kategori,
            'stock' => $buku->stock,
            'lokasi' => $buku->lokasi,
            'cover' => $buku->cover,
        ]);

        return redirect()
            ->route('admin.databuku.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show($id_buku)
    {
        $buku = Buku::findOrFail($id_buku);

        $this->simpanAnalyticsLog('Detail Buku Admin', 'Admin melihat detail buku', [
            'id_buku' => $buku->id_buku,
            'kode' => $buku->kode,
            'judul' => $buku->judul,
            'kategori' => $buku->kategori,
        ]);

        return view('admin.databuku.show', compact('buku'));
    }

    public function edit($id_buku)
    {
        $buku = Buku::findOrFail($id_buku);

        return view('admin.databuku.edit', compact('buku'));
    }

    public function update(Request $request, $id_buku, BookInventory $inventory)
    {
        $buku = Buku::findOrFail($id_buku);

        $request->validate([
            'kode' => 'required|string|max:100|unique:buku,kode,'.$buku->id_buku.',id_buku',
            'isbn' => 'nullable|string|max:20',
            'judul' => 'required',
            'pengarang' => 'required',
            'penerbit' => 'required',
            'tahun_terbit' => 'required',
            'kategori' => 'required',
            'stock' => 'required|integer|min:0',
            'lokasi' => 'required',
            'deskripsi' => 'nullable',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $dataLama = $buku->only([
            'kode',
            'judul',
            'pengarang',
            'penerbit',
            'tahun_terbit',
            'kategori',
            'stock',
            'lokasi',
            'deskripsi',
            'cover',
        ]);

        $coverPath = $buku->cover;

        if ($request->hasFile('cover')) {
            if ($buku->cover && Storage::disk('public')->exists($buku->cover)) {
                Storage::disk('public')->delete($buku->cover);
            }

            $coverPath = $request->file('cover')->store('cover-buku', 'public');
        }

        $buku->update([
            'kode' => $request->kode,
            'isbn' => $request->filled('isbn') ? $inventory->normalizeIsbn($request->isbn) : null,
            'judul' => $request->judul,
            'pengarang' => $request->pengarang,
            'penerbit' => $request->penerbit,
            'tahun_terbit' => $request->tahun_terbit,
            'kategori' => $request->kategori,
            'stock' => $buku->stock,
            'lokasi' => $request->lokasi,
            'deskripsi' => $request->deskripsi,
            'cover' => $coverPath,
        ]);
        $inventory->syncAvailable($buku, (int) $request->stock);

        $this->simpanActivityLog('Update Buku', 'Admin memperbarui data buku', [
            'id_buku' => $buku->id_buku,
            'data_lama' => $dataLama,
            'data_baru' => [
                'kode' => $buku->kode,
                'judul' => $buku->judul,
                'pengarang' => $buku->pengarang,
                'penerbit' => $buku->penerbit,
                'tahun_terbit' => $buku->tahun_terbit,
                'kategori' => $buku->kategori,
                'stock' => $buku->stock,
                'lokasi' => $buku->lokasi,
                'deskripsi' => $buku->deskripsi,
                'cover' => $buku->cover,
            ],
        ]);

        return redirect()
            ->route('admin.databuku.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy($id_buku)
    {
        $buku = Buku::findOrFail($id_buku);
        if (Peminjaman::where('id_buku', $buku->id_buku)->exists()) {
            return back()->with('error', 'Buku memiliki riwayat transaksi. Tidak dapat dihapus; arsipkan melalui prosedur sekolah.');
        }

        $dataBuku = [
            'id_buku' => $buku->id_buku,
            'kode' => $buku->kode,
            'judul' => $buku->judul,
            'pengarang' => $buku->pengarang,
            'penerbit' => $buku->penerbit,
            'tahun_terbit' => $buku->tahun_terbit,
            'kategori' => $buku->kategori,
            'stock' => $buku->stock,
            'lokasi' => $buku->lokasi,
            'cover' => $buku->cover,
        ];

        if ($buku->cover && Storage::disk('public')->exists($buku->cover)) {
            Storage::disk('public')->delete($buku->cover);
        }

        $buku->delete();

        $this->simpanActivityLog('Hapus Buku', 'Admin menghapus data buku', $dataBuku);

        return redirect()
            ->route('admin.databuku.index')
            ->with('success', 'Buku berhasil dihapus.');
    }

    public function userIndex(Request $request)
    {
        $search = $request->search;
        $kategori = $request->kategori;
        $tahun = $request->tahun;
        $stok = $request->stok;
        $sort = $request->sort ?? 'terbaru';

        if ($search || $kategori || $tahun || $stok || $sort !== 'terbaru') {
            $this->simpanSearchHistory(
                'User Data Buku',
                $search ?? '-',
                'User mencari/filter buku',
                [
                    'search' => $search,
                    'kategori' => $kategori,
                    'tahun' => $tahun,
                    'stok' => $stok,
                    'sort' => $sort,
                ]
            );
        }

        $query = Buku::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', '%'.$search.'%')
                        ->orWhere('kode', 'like', '%'.$search.'%')
                        ->orWhere('pengarang', 'like', '%'.$search.'%')
                        ->orWhere('penerbit', 'like', '%'.$search.'%');
                });
            })
            ->when($kategori && $kategori != 'semua', function ($query) use ($kategori) {
                $query->where('kategori', $kategori);
            })
            ->when($tahun, function ($query) use ($tahun) {
                $query->where('tahun_terbit', $tahun);
            })
            ->when($stok == 'tersedia', function ($query) {
                $query->where('stock', '>', 0);
            });

        if ($sort == 'terlama') {
            $query->oldest('id_buku');
        } elseif ($sort == 'judul_az') {
            $query->orderBy('judul', 'asc');
        } elseif ($sort == 'judul_za') {
            $query->orderBy('judul', 'desc');
        } elseif ($sort == 'tahun_terbaru') {
            $query->orderBy('tahun_terbit', 'desc');
        } elseif ($sort == 'tahun_terlama') {
            $query->orderBy('tahun_terbit', 'asc');
        } elseif ($sort == 'stok_terbanyak') {
            $query->orderBy('stock', 'desc');
        } else {
            $query->latest('id_buku');
        }

        $buku = $query->paginate(4)->appends($request->query());

        $daftarKategori = Buku::whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        $daftarTahun = Buku::query()
            ->when($kategori && $kategori != 'semua', function ($query) use ($kategori) {
                $query->where('kategori', $kategori);
            })
            ->whereNotNull('tahun_terbit')
            ->where('tahun_terbit', '!=', '')
            ->distinct()
            ->orderBy('tahun_terbit', 'desc')
            ->pluck('tahun_terbit');

        $this->simpanAnalyticsLog('User Data Buku', 'User membuka katalog buku', [
            'jumlah_data_tampil' => $buku->count(),
            'kategori' => $kategori,
            'tahun' => $tahun,
            'stok' => $stok,
            'sort' => $sort,
        ]);

        return view('user.databuku.index', compact(
            'buku',
            'search',
            'kategori',
            'tahun',
            'stok',
            'daftarKategori',
            'daftarTahun'
        ));
    }

    public function userShow($id_buku)
    {
        $buku = Buku::findOrFail($id_buku);

        $bukuTerkait = Buku::where('id_buku', '!=', $id_buku)
            ->where('kategori', $buku->kategori)
            ->latest('id_buku')
            ->limit(4)
            ->get();

        $this->simpanAnalyticsLog('Detail Buku User', 'User melihat detail buku', [
            'id_buku' => $buku->id_buku,
            'kode' => $buku->kode,
            'judul' => $buku->judul,
            'kategori' => $buku->kategori,
            'stock' => $buku->stock,
        ]);

        return view('user.databuku.show', compact('buku', 'bukuTerkait'));
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
            Log::error('Gagal menyimpan ActivityLog Buku: '.$e->getMessage());
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
            Log::error('Gagal menyimpan AnalyticsLog Buku: '.$e->getMessage());
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
            Log::error('Gagal menyimpan SearchHistory Buku: '.$e->getMessage());
        }
    }
}
