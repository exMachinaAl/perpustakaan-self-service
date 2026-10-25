<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SheetPengembalian implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $pengembalian;

    public function __construct($pengembalian)
    {
        $this->pengembalian = $pengembalian;
    }

    public function collection()
    {
        return $this->pengembalian->map(function($p) {
            return [
                'User' => $p->user->nama ?? '-',
                'Buku' => $p->buku->judul ?? '-',
                'Tanggal Kembali' => $p->tanggal_kembali ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['User', 'Buku', 'Tanggal Kembali'];
    }
}
