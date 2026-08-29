<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $connection = 'site';

    protected $table = 'payments';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'float',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(
            Member::class,
            'member_id',
            'id'
        );
    }
}
