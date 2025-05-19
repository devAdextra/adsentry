<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = [
        'filename',
        'original_filename',
        'path',
        'status',
        'filters',
        'user',
        'total_records',
        'expires_at',
        'selectedDb',
        'user_id'
    ];

    protected $casts = [
        'filters' => 'array',
        'expires_at' => 'datetime',
    ];

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'processing' => 'bg-warning',
            'completed' => 'bg-success',
            'failed' => 'bg-danger',
            default => 'bg-secondary'
        };
    }
} 