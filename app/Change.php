<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Change extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'field_name',
        'old_value',
        'new_value',
        'accepted',
        'created_at'
    ];
}
