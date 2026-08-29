<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $connection = 'site';

    protected $table = 'educations';

    protected $fillable = [
        'education',
    ];

    public $timestamps = false;
}
