<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SheetBuku implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $buku;

    public function __construct($buku)
    {
        $this->buku = $buku;
    }

    public function collection()
    {
        return $this->buku->map(function($b) {
            return [
                'Judul' => $b->judul,
                'Tahun Terbit' => $b->tahun_terbit ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['Judul', 'Tahun Terbit'];
    }
}
