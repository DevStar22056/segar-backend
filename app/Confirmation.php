<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Confirmation extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'value',
    ];

    public function user()
    {
        return $this->hasOne(ProjectUser::class);
    }
}
