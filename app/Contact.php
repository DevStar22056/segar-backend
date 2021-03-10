<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'position',
        'phone',
        'mail',
        'resource_id'
    ];
}
