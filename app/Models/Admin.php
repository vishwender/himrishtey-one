<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'profile_id',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'status' => 'boolean',
        'password' => 'hashed',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'admin_roles'
        );
    }

    public function sites()
    {
        return $this->belongsToMany(
            Site::class,
            'admin_sites'
        );
    }

    public function activityLogs()
    {
        return $this->hasMany(
            AdminActivityLog::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Role / Permission Helpers
    |--------------------------------------------------------------------------
    */

    public function hasRole(string $role): bool
    {
        return $this->roles()
            ->where('slug', $role)
            ->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()
            ->whereIn('slug', $roles)
            ->exists();
    }

    public function hasPermission(string $permission): bool
    {
        // Super Admin has every permission.
        if ($this->hasRole('super-admin')) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('slug', $permission);
            })
            ->exists();
    }

    public function hasPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (! $this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function hasSiteAccess(int $siteId): bool
    {
        // Super Admin can access every active site.
        if ($this->hasRole('super-admin')) {
            return Site::where('id', $siteId)
                ->where('status', true)
                ->exists();
        }

        return $this->sites()
            ->where('sites.id', $siteId)
            ->where('sites.status', true)
            ->exists();
    }
}
