<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceContractor extends Model
{
    protected $fillable = [
        'invoice_id',
        'user_id',
        'hours_value',
        'netto',
        'vat',
        'gross',
    ];
}
