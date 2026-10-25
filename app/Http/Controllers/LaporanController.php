<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Buku;
use App\Models\User;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;



class LaporanController extends Controller
{
    public function index()
    {
        $peminjaman = Peminjaman::with('buku','user')->get();
        $pengembalian = Peminjaman::where('status', 'dikembalikan')->with('buku','user')->get();
        $buku = Buku::all();
        $anggota = User::where('role','user')->get();

        return view('admin.laporan.index', compact(
            'peminjaman',
            'pengembalian',
            'buku',
            'anggota'
        ));
    }

    public function export()
{
     // Ambil semua peminjaman beserta relasi user & buku
    $peminjaman = Peminjaman::with(['user', 'buku'])->get();

    // Buat "pengembalian" dari peminjaman yang sudah dikembalikan
    $pengembalian = $peminjaman->whereNotNull('tanggal_kembali');

    // Ambil semua buku dan anggota
    $buku = Buku::all();
    $anggota = User::with('prodi')->where('role','user')->get();

    // Export Excel
    return Excel::download(
        new LaporanExport($peminjaman, $pengembalian, $buku, $anggota),
        'laporan_perpustakaan.xlsx'
    );
}

   
}
