<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceCost extends Model
{
    protected $fillable = [
        'cost_value',
        'cost_vat',
        'cost_vat_value',
        'cost_description',
        'cost_files',
        'invoice_id',
        'user_id',
        'cost_type'
    ];
}
