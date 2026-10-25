<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SheetPeminjaman implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $peminjaman;

    public function __construct($peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    public function collection()
    {
        return $this->peminjaman->map(function($p){
            return [
                'User' => $p->user->nama ?? '-',
                'Buku' => $p->buku->judul ?? '-',
                'Tanggal Peminjaman' => $p->tanggal_peminjaman,
                'Tanggal Kembali' => $p->tanggal_kembali ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['User', 'Buku', 'Tanggal Peminjaman', 'Tanggal Kembali'];
    }
}
