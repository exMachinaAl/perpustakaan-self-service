<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Log stored in the primary MySQL database (no MongoDB driver required).
 * Compatible with existing Controller::SearchHistory::create() calls.
 */
class SearchHistory extends Model
{
    protected $table = 'search_history';

    protected $fillable = [
        'user_id',
        'nama_user',
        'role',
        'keyword',
        'kategori',
        'sort',
        'halaman',
        'keterangan',
        'data',
        'waktu',
    ];

    protected $casts = [
        'data' => 'array',
        'waktu' => 'datetime',
    ];
}
