<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminActivityLog extends Model
{
    protected $table = 'admin_activity_logs';

    protected $fillable = [
        'admin_id',
        'site_id',
        'action',
        'module',
        'subject_type',
        'subject_id',
        'member_id',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(
            Admin::class,
            'admin_id',
            'id'
        );
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(
            Site::class,
            'site_id',
            'id'
        );
    }
}
