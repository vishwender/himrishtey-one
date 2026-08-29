<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $connection = 'site';

    protected $table = 'countries';

    protected $fillable = [
        'name',
    ];

    public $timestamps = false;
}
