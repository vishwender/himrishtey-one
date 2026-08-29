<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletOffer extends Model
{
    protected $connection = 'site';

    protected $table = 'wallet_offers';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'amount',
        'add_on_percentage',
        'final_amount',
        'description',
    ];
}
