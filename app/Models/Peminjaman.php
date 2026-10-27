<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $primaryKey = 'peminjaman_id';

    protected $fillable = [
        'user_id',
        'id_buku',
        'copy_id',
        'kiosk_device_id',
        'source',
        'tanggal_pinjam',
        'due_date',
        'tanggal_kembali',
        'status',
        'denda',
        'denda_dibayar',
        'status_denda',
        'tanggal_bayar_denda',
    ];

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
