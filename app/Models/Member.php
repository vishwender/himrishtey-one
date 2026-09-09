<?php

namespace App\Models;

use App\Services\RelationshipManagerAccess;
use App\Support\HeightFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class Member extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'members';

    protected $connection = 'site';

    public $timestamps = false;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'google_token',
        'photo_password',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(
            'relationship_manager_access',
            fn (Builder $query) => app(RelationshipManagerAccess::class)->scope($query)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Format Height
    |--------------------------------------------------------------------------
    */

    public function formatHeight($height): string
    {
        return HeightFormatter::format($height);
    }

    /*
    |--------------------------------------------------------------------------
    | Partner Height
    |--------------------------------------------------------------------------
    */

    public function getPartnerHeightFromFormattedAttribute(): string
    {
        return $this->formatHeight(
            $this->partner_height_from
        );
    }

    public function getPartnerHeightToFormattedAttribute(): string
    {
        return $this->formatHeight(
            $this->partner_height_to
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Profile Completion
    |--------------------------------------------------------------------------
    */

    public function getProfileCompletionAttribute(): int
    {
        $excludedFields = [
            'id',
            'password',
            'google_token',
            'photo_password',
        ];

        $attributes = $this->getAttributes();

        $totalFields = 0;
        $completedFields = 0;

        foreach ($attributes as $field => $value) {

            if (in_array($field, $excludedFields)) {
                continue;
            }

            $totalFields++;

            if (
                $value !== null &&
                trim((string) $value) !== ''
            ) {
                $completedFields++;
            }
        }

        if ($totalFields === 0) {
            return 0;
        }

        return (int) round(
            ($completedFields / $totalFields) * 100
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Completed Profile Fields
    |--------------------------------------------------------------------------
    */

    public function getCompletedFieldsAttribute(): int
    {
        $excludedFields = [
            'id',
            'password',
            'google_token',
            'photo_password',
        ];

        $completed = 0;

        foreach ($this->getAttributes() as $field => $value) {

            if (in_array($field, $excludedFields)) {
                continue;
            }

            if (
                $value !== null &&
                trim((string) $value) !== ''
            ) {
                $completed++;
            }
        }

        return $completed;
    }

    public function getProfileCompletion(): int
    {
        return $this->profile_completion;
    }

    public function updateProfileCompletion(): int
    {
        $this->profile_completed = $this->getProfileCompletion();
        $this->saveQuietly();

        return (int) $this->profile_completed;
    }

    /*
    |--------------------------------------------------------------------------
    | Total Profile Fields
    |--------------------------------------------------------------------------
    */

    public function getTotalFieldsAttribute(): int
    {
        $excludedFields = [
            'id',
            'password',
            'google_token',
            'photo_password',
        ];

        return count(
            array_diff(
                array_keys($this->getAttributes()),
                $excludedFields
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Active Status
    |--------------------------------------------------------------------------
    */

    public function getIsActiveAttribute(): bool
    {
        return strtolower(
            trim((string) $this->active)
        ) === 'yes';
    }

    /*
    |--------------------------------------------------------------------------
    | Inactive Status
    |--------------------------------------------------------------------------
    */

    public function getIsInactiveAttribute(): bool
    {
        return ! $this->is_active;
    }

    /*
    |--------------------------------------------------------------------------
    | Member Photos
    |--------------------------------------------------------------------------
    */

    public function photos(): HasMany
    {
        return $this->hasMany(
            MemberPhoto::class,
            'member_id',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Member Rotations
    |--------------------------------------------------------------------------
    */

    public function rotations(): HasMany
    {
        return $this->hasMany(
            MemberRotation::class,
            'member_id',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Latest Rotation
    |--------------------------------------------------------------------------
    */

    public function latestRotation(): HasOne
    {
        return $this->hasOne(
            MemberRotation::class,
            'member_id',
            'id'
        )->latestOfMany();
    }

    public function generateProfileId(int $memberId): string
    {
        $database = DB::connection('site')
            ->getDatabaseName();

        $prefixes = [
            'himrishteymain_base' => 'HIM',
            'himrishteymain_gallpakki' => 'PB',
            'himrishteymain_devbhoomi' => 'DB',
            'himrishteymain_dogririshtey' => 'DR',
        ];

        $prefix = $prefixes[$database] ?? null;

        if (! $prefix) {
            throw new \RuntimeException(
                "No profile ID prefix configured for database: {$database}"
            );
        }

        return $prefix.$memberId;
    }
}
