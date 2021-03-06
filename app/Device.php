<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'device_name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
