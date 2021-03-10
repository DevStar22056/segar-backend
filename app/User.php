<?php

namespace App;

use App\Http\Controllers\ApiController;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'verified',
        'password',
        'is_active',
        'hrm_candidat_id',
        'address_id',
        'profile',
        'type',
        'country',
        'user_phone',
        'rate_type',
        'invoice_bank_no',
        'user_street',
        'user_postal_code',
        'user_city',
        'user_residency',
        'company_type',
        'invoice_company_name',
        'invoice_payment_currency',
        'invoice_address_id',
        'company_street',
        'company_postal_code',
        'company_city',
        'company_nip',
        'invoice_reference_no',
        'invoice_krs_no',
        'invoice_payment_deadline',
        'invoice_vat_no',
        'vat_value',
        'cash_register',
        'bank_name',
        'bank_account_number',
        'bank_iban',
        'bank_swift_bic',
        'settlement_type',
        'hourly_rate',
        'hourly_currency',
        'overtime_rate',
        'fixed_rate',
        'fixed_currency',
        'oncall_10',
        'oncall_30',
        'first_payment_number',
        'second_payment_number',
        'third_payment_number',
        'other_payment_number',
        'client_id',
        'payment_date',
        'email_verified_at',
        'date_of_signing',
        'date_of_ending',
        'contract_duration',
        'notice_ending',
        'internal_hour_rate',
        'internal_overtime_rate',
        'internal_fixed_rate',
        'internal_rate_type',
        'is_on_fixed_rate',
        'is_on_hourly_rate',
        'order_id',
        'can_login',
        'language',
    ];

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * @param $roles
     */
    public function checkRoles($roles)
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        if (!$this->hasAnyRole($roles)) {
            ApiController::logout();
            auth()->logout();
            abort(404);
        }
    }

    /**
     * @param $roles
     * @return bool
     */
    public function hasAnyRole($roles): bool
    {
        return (bool)$this->roles()->whereIn('name', $roles)->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * @param $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        return (bool)$this->roles()->where('name', $role)->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasInternalRate()
    {
        return $this->hasMany(InternalRate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this
            ->hasMany(Fileupload::class, 'source_id')
            ->where('source', '=', 0);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function changes()
    {
        return $this->hasMany(Change::class, 'user_id');
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class, 'resource_id');
    }
}
