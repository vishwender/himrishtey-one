<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
    protected $connection = 'site';

    protected $table = 'religions';

    protected $fillable = [
        'religion',
    ];

    public $timestamps = false;
}
