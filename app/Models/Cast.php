<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cast extends Model
{
    protected $connection = 'site';

    protected $table = 'casts';

    protected $fillable = [
        'cast',
        'religion',
    ];

    public $timestamps = false;
}
