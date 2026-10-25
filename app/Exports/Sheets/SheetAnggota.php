<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SheetAnggota implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $anggota;

    public function __construct($anggota)
    {
        $this->anggota = $anggota;
    }

    public function collection()
    {
        return $this->anggota->map(function($a) {
            return [
                'Nama' => $a->nama,
                'Prodi' => $a->prodi->nama ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama', 'Prodi'];
    }
}
