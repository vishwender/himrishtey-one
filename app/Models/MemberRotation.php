<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberRotation extends Model
{
    protected $connection = 'site';

    protected $table = 'member_rotations';

    protected $fillable = [
        'member_id',
        'user_id',
        'days',
        'time',
        'next_rotation_at',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'next_rotation_at' => 'datetime',
        'completed_at' => 'datetime',
        'time' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(
            Member::class,
            'member_id'
        );
    }
}
