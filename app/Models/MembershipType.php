<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipType extends Model
{
    protected $connection = 'site';

    protected $table = 'membership_type';

    protected $fillable = [
        'plan_name',
        'plan_guide',
        'plan_description',
        'terms_and_conditions',
    ];

    public $timestamps = false;

    public function plans()
    {
        return $this->hasMany(
            MembershipPlan::class,
            'membership_type',
            'id'
        );
    }
}
