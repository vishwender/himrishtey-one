<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberPhoto extends Model
{
    protected $table = 'member_photos';

    protected $connection = 'site';

    public $timestamps = false;

    protected $guarded = [];

    /**
     * Member who owns this photo.
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }
}
