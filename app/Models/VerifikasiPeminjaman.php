<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifikasiPeminjaman extends Model
{
    protected $table = 'verifikasi_peminjaman';

    protected $fillable = [
        'user_id',
        'id_buku',
        'tanggal_pengajuan',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku');
    }
}
