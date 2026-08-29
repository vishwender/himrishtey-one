<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuccessStory extends Model
{
    protected $table = 'success_stories';

    protected $connection = 'site';

    public $timestamps = false;

    protected $fillable = [
        'groom_name',
        'bride_name',
        'detail',
        'photo',
        'user_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'user_id' => 'integer',
    ];
}
