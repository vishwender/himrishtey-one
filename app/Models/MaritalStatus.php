<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaritalStatus extends Model
{
    protected $connection = 'site';

    protected $table = 'marital_status';

    protected $fillable = [
        'marital_status',
    ];

    public $timestamps = false;
}
