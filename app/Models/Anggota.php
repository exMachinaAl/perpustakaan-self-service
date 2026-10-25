<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $table = 'anggota';
    protected $primaryKey = 'id_anggota';

    protected $fillable = [
        'user_id',
        'nim',
        'nama',
        'prodi',
        'email',
        'status',
        'tanggal_daftar',
        'jumlah_pinjaman',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prodi()
{
    return $this->belongsTo(Prodi::class, 'prodi_id');
}

}
