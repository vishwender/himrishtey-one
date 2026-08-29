<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileRange extends Model
{
    protected $connection = 'site';

    protected $table = 'profile_ranges';

    public $timestamps = false;

    protected $fillable = [
        'range_from',
        'range_to',
        'rate',
    ];
}
