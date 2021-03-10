<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    protected $fillable = [
        'agree_from',
        'agree_to',
        'period',
        'penalties',
        'resource_id'
    ];
}
