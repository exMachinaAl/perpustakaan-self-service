<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Prodi extends Model
{
    use HasFactory;

    protected $table = 'prodi';

    protected $fillable = [
        'nama',
        'kode_prodi',
        'fakultas',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'prodi_id', 'id');
    }
}