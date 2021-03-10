<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'company_name',
        'client_id'
    ];

    public function project_users()
    {
        return $this->hasMany(ProjectUser::class);
    }
}
