<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExternalPersona extends Model
{
    protected $fillable = [
        'email',
        'name',
        'surname',
        'user_phone',
        'country',
        'user_street',
        'user_postal_code',
        'user_city',
        'invoice_company_name',
        'company_nip',
        'description',
    ];
}
