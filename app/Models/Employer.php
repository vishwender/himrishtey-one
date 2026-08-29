<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
    protected $connection = 'site';

    protected $table = 'employers';

    protected $fillable = [
        'employer',
    ];

    public $timestamps = false;
}
