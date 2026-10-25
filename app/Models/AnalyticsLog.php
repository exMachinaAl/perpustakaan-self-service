<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Log stored in the primary MySQL database (no MongoDB driver required).
 * Compatible with existing Controller::AnalyticsLog::create() calls.
 */
class AnalyticsLog extends Model
{
    protected $table = 'analytics_logs';

    protected $fillable = [
        'user_id',
        'nama_user',
        'role',
        'event',
        'halaman',
        'aksi',
        'ip_address',
        'user_agent',
        'data',
        'waktu',
    ];

    protected $casts = [
        'data' => 'array',
        'waktu' => 'datetime',
    ];
}
