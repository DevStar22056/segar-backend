<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $fillable = [
        'title',
        'nip',
        'company_name',
        'street',
        'address1',
        'address2',
        'postal_code',
        'city',
        'regon',
        'logo',
        'bank_account',
        'currency',
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
    public function logo()
    {
        return $this->hasOne(Fileupload::class, 'source_id')
            ->where('source', '=', 2);
    }
}
