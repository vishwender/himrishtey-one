<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $connection = 'site';

    protected $table = 'offers';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
        'image',
        'offer_date',
        'status',
        'offer_time',
    ];
}
