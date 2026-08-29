<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeleteProfileRequest extends Model
{
    protected $connection = 'site';

    protected $table = 'delete_profile_request';

    public $timestamps = false;

    protected $guarded = [];

    public function member(): BelongsTo
    {
        return $this->belongsTo(
            Member::class,
            'user_id',
            'id'
        );
    }
}
