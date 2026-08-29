<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteMember extends Model
{
    protected $table = 'members';

    protected $connection = 'site';

    public $timestamps = false;

    protected $guarded = [];

    /**
     * Format height.
     *
     * Examples:
     *
     * 5.7  => 5ft 7in
     * 5.10 => 5ft 10in
     * 5.11 => 5ft 11in
     * 6    => 6ft 0in
     */
    public function formatHeight($height): string
    {
        if ($height === null || $height === '') {
            return '-';
        }

        $height = trim((string) $height);

        if ($height === '') {
            return '-';
        }

        $parts = explode('.', $height, 2);

        $feet = (int) $parts[0];

        $inches = isset($parts[1])
            ? (int) $parts[1]
            : 0;

        return "{$feet}ft {$inches}in";
    }

    /**
     * Main profile photo.
     *
     * This comes from members.photo.
     */
    public function getProfilePhotoAttribute()
    {
        return $this->photo;
    }

    /**
     * Gallery photos.
     *
     * These come from member_photos.
     */
    public function photos()
    {
        return $this->hasMany(
            MemberPhoto::class,
            'member_id',
            'id'
        );
    }
}
