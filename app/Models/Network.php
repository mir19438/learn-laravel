<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    protected $fillable = [
        'user_id',
        'parent_id',
        'referral_code'
    ];
}
