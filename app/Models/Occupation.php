<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    protected $connection = 'site';

    protected $table = 'occupations';

    protected $fillable = [
        'occupation',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
        'adding_date' => 'datetime',
    ];

    const CREATED_AT = 'adding_date';

    const UPDATED_AT = null;
}
