<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRating extends Model
{
    protected $table = 'user_rating';

    protected $connection = 'site';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'profile_id',
        'rating',
        'description',
        'submitted_on',
    ];
}
