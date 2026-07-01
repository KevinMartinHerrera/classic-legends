<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $table = 'sync_logs';

    protected $fillable = [
        'started_at',
        'finished_at',
        'status',
        'pages_requested',
        'pages_scanned',
        'albums_found',
        'products_created',
        'products_updated',
        'images_synced',
        'errors_count',
        'summary',
        'meta',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'meta' => 'array',
    ];
}
