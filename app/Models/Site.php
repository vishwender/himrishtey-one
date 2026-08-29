<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'name',
        'code',
        'domain',
        'database_host',
        'database_port',
        'database_name',
        'database_username',
        'database_password',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $hidden = [
        'database_password',
    ];

    public function admins()
    {
        return $this->belongsToMany(
            Admin::class,
            'admin_sites'
        );
    }

    public function activityLogs()
    {
        return $this->hasMany(
            AdminActivityLog::class
        );
    }
}
