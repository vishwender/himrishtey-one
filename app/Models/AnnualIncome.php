<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnualIncome extends Model
{
    protected $connection = 'site';

    protected $table = 'annual_incomes';

    protected $fillable = [
        'annual_income',
        'display_order',
    ];

    public $timestamps = false;

    protected $casts = [
        'display_order' => 'integer',
    ];
}
