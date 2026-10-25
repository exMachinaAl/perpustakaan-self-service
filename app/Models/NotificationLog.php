<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Log stored in the primary MySQL database (no MongoDB driver required).
 * Compatible with existing Controller::NotificationLog::create() calls.
 */
class NotificationLog extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'nama_user',
        'judul',
        'pesan',
        'tipe',
        'status',
        'dibaca',
        'data',
        'waktu',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
        'data' => 'array',
        'waktu' => 'datetime',
    ];
}
