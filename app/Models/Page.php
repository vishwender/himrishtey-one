<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $connection = 'site';

    protected $table = 'pages';

    protected $fillable = [
        'refund_policy',
        'privacy_policy',
        'terms_and_conditions',
        'about_us',
        'updated_at',
    ];

    public $timestamps = false;
}
