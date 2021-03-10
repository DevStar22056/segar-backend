<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->unique();
            $table->string('name');
            $table->string('password');
            $table->string('rate_type')->default('0');
            $table->boolean('is_active')->default(0);
            $table->boolean('can_login')->default(0);
            $table->string('role')->nullable();
            $table->boolean('verified')->default(0);
            $table->integer('hrm_candidat_id')->nullable();
            $table->integer('address_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->string('surname')->nullable();
            $table->string('profile')->nullable();
            $table->string('type')->nullable();
            $table->string('user_phone')->nullable();
            $table->string('country')->nullable();
            $table->string('user_street')->nullable();
            $table->string('user_postal_code')->nullable();
            $table->string('user_city')->nullable();
            $table->string('user_residency')->nullable();
            $table->string('company_type')->nullable();
            $table->string('invoice_company_name')->nullable();
            $table->string('invoice_payment_currency')->nullable();
            $table->string('invoice_address_id')->nullable();
            $table->string('company_street')->nullable();
            $table->string('company_postal_code')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_nip')->nullable();
            $table->string('invoice_reference_no')->nullable();
            $table->string('invoice_krs_no')->nullable();
            $table->string('invoice_payment_deadline')->nullable();
            $table->string('invoice_vat_no')->nullable();
            $table->string('vat_value')->nullable();
            $table->string('cash_register')->default('0');
            $table->string('is_on_fixed_rate')->default('0');
            $table->string('is_on_hourly_rate')->default('0');
            $table->string('bank_name')->nullable();
            $table->string('invoice_bank_no')->nullable();
            $table->string('bank_iban')->nullable();
            $table->string('bank_swift_bic')->nullable();
            $table->string('settlement_type')->nullable();
            $table->string('hourly_rate')->nullable();
            $table->string('hourly_currency')->nullable();
            $table->string('overtime_rate')->nullable();
            $table->string('fixed_rate')->nullable();
            $table->string('fixed_currency')->nullable();
            $table->boolean('oncall_10')->default(0);
            $table->boolean('oncall_30')->default(0);
            $table->string('first_payment_number')->nullable();
            $table->string('second_payment_number')->nullable();
            $table->string('third_payment_number')->nullable();
            $table->string('other_payment_number')->nullable();
            $table->string('payment_date')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('date_of_signing')->nullable();
            $table->timestamp('date_of_ending')->nullable();
            $table->timestamp('contract_duration')->nullable();
            $table->string('notice_ending')->nullable();
            $table->string('internal_hour_rate')->nullable();
            $table->string('internal_overtime_rate')->nullable();
            $table->string('internal_fixed_rate')->nullable();
            $table->string('internal_rate_type')->nullable();
            $table->string('language')->default('pl_PL');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
