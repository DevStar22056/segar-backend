<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'resource_id', 'resource_type', 'bank_name', 'invoice_bank_no', 'bank_iban', 'bank_swift_bic'
    ];
}
