<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Height extends Model
{
    protected $connection = 'site';

    protected $table = 'heights';

    protected $fillable = [
        'height',
        'height_value',
    ];

    public $timestamps = false;
}
