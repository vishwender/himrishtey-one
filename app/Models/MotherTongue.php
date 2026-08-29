<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotherTongue extends Model
{
    protected $connection = 'site';

    protected $table = 'mother_tongues';

    protected $fillable = [
        'mother_tongue',
    ];

    public $timestamps = false;
}
