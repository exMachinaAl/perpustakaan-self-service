<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Log stored in the primary MySQL database (no MongoDB driver required).
 * Compatible with existing Controller::ActivityLog::create() calls.
 */
class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'nama_user',
        'role',
        'aktivitas',
        'keterangan',
        'data',
        'ip_address',
        'user_agent',
        'waktu',
    ];

    protected $casts = [
        'data' => 'array',
        'waktu' => 'datetime',
    ];
}
