<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeleteProfileRequest extends Model
{
    protected $connection = 'site';

    protected $table = 'delete_profile_request';

    public $timestamps = false;

    protected $guarded = [];

    /**
     * Legacy member submissions use zero; older API submissions used the member
     * ID and a full timestamp. Staff writers use an admin ID and a date only.
     */
    public function scopeFromSource(Builder $query, string $source): Builder
    {
        $memberSubmission = function (Builder $query) {
            $query->whereNull('request_by')->orWhere('request_by', 0)
                ->orWhere(function (Builder $query) {
                    $query->whereColumn('request_by', 'user_id')
                        ->whereRaw("COALESCE(date, '') LIKE ?", ['____-__-__ __:__:__']);
                });
        };

        return $source === 'member'
            ? $query->where($memberSubmission)
            : $query->whereNot($memberSubmission);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(
            Member::class,
            'user_id',
            'id'
        );
    }
}
