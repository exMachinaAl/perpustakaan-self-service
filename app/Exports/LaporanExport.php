<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\SheetPeminjaman;
use App\Exports\Sheets\SheetPengembalian;
use App\Exports\Sheets\SheetBuku;
use App\Exports\Sheets\SheetAnggota;

class LaporanExport implements WithMultipleSheets
{
    protected $peminjaman;
    protected $pengembalian;
    protected $buku;
    protected $anggota;

    public function __construct($peminjaman, $pengembalian, $buku, $anggota)
    {
        $this->peminjaman = $peminjaman;
        $this->pengembalian = $pengembalian;
        $this->buku = $buku;
        $this->anggota = $anggota;
    }

    public function sheets(): array
    {
        return [
            new SheetPeminjaman($this->peminjaman),
            new SheetPengembalian($this->pengembalian),
            new SheetBuku($this->buku),
            new SheetAnggota($this->anggota),
        ];
    }
}
