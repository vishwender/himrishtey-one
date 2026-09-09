<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberProfileRange extends Model
{
    use HasFactory;
    protected $fillable = [];
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'member_profile_range';
}
