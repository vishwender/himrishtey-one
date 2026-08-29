<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipPlan extends Model
{
    protected $connection = 'site';

    protected $table = 'membership_plans';

    protected $fillable = [
        'membership_type',
        'plan_name',
        'duration_days',
        'view_contact',
        'view_profile',
        'plan_cost',
        'discount_percentage',
        'final_cost',
    ];

    public $timestamps = false;

    public function membershipType()
    {
        return $this->belongsTo(
            MembershipType::class,
            'membership_type',
            'id'
        );
    }
}
