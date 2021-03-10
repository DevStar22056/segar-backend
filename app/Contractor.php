<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    protected $fillable = [
        'nip',
        'company_name',
        'street',
        'address1',
        'address2',
        'postal_code',
        'city',
        'country',
        'regon',
        'bank_account',
        'account_manager',
        'currency',
        'shipping_type',
        'shipping_email',
        'shipping_post',
        'is_b2b',
        'is_uop',
        'is_margin',
        'is_inne',
        'terms_uop',
        'terms_currency_type',
        'terms_payment_deadline',
        'invoicing_type',
        'invoicing_invoice',
        'invoicing_process'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bankAccounts()
    {
        return $this->hasOne(BankAccount::class, 'resource_id')
            ->where('resource_type', '=', 2);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function contacts()
    {
        return $this->hasOne(Contact::class, 'resource_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function agreements()
    {
        return $this->hasOne(Agreement::class, 'resource_id');
    }
}
