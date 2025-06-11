<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = [
        'filename',
        'db',
        'file_path',
        'file_size',
        'mime_type',
        'upload_at',
        'processed_at',
        'progress',
        'status',
        'lead_processed',
        'movements_created'
    ];

    protected $casts = [
        'upload_at' => 'datetime',
        'processed_at' => 'datetime'
    ];
} 