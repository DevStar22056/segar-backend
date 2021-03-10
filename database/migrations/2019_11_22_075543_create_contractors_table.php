<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nip')->nullable();
            $table->string('company_name')->nullable();
            $table->string('street')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('regon')->nullable();
            $table->integer('bank_account')->nullable();
            $table->string('account_manager')->nullable();
            $table->string('currency')->nullable();
            $table->string('shipping_type')->nullable();
            $table->string('shipping_email')->nullable();
            $table->text('shipping_post')->nullable();
            $table->boolean('is_b2b')->default(0);
            $table->boolean('is_uop')->default(0);
            $table->boolean('is_margin')->default(0);
            $table->boolean('is_inne')->default(0);
            $table->integer('terms_uop')->nullable();
            $table->string('terms_currency_type')->nullable();
            $table->string('terms_payment_deadline')->nullable();
            $table->string('invoicing_type')->nullable();
            $table->string('invoicing_invoice')->nullable();
            $table->text('invoicing_process')->nullable();
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
        Schema::dropIfExists('contractors');
    }
}
