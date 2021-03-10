<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fileupload extends Model
{
    protected $fillable = [
        'original_name',
        'type',
        'filename',
        'path',
        'source',
        'source_id'
    ];
}