<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyStatus extends Model
{
    protected $connection = 'site';

    protected $table = 'family_status';

    protected $fillable = [
        'value',
    ];

    public $timestamps = false;
}
