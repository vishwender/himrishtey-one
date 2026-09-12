<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $connection = 'site';

    protected $fillable = ['site_key', 'name', 'email', 'phone', 'profile_id', 'subject', 'message'];

    protected $casts = ['read_at' => 'datetime'];
}
