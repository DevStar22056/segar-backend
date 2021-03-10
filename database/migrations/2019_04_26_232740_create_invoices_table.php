<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('creator')->nullable();
            $table->integer('invoice_type')->nullable();
            $table->integer('invoice_type_id')->nullable();
            $table->integer('correction_id')->nullable();
            $table->text('correction_description')->nullable();
            $table->integer('user_id');

            $table->integer('vendor')->nullable();
            $table->integer('purchaser')->default(1);

            $table->integer('approval')->nullable();

            $table->string('purchaser_name')->nullable();
            $table->string('purchaser_nip')->nullable();
            $table->string('purchaser_address')->nullable();

            $table->string('vendor_name')->nullable();
            $table->string('vendor_nip')->nullable();
            $table->string('vendor_address')->nullable();

            $table->timestamp('issue_date')->nullable();
            $table->timestamp('completion_date')->nullable();
            $table->text('description')->nullable();
            $table->string('language')->nullable();
            $table->string('files')->nullable();
            $table->string('file')->nullable();
            $table->string('remarks')->nullable();
            $table->string('invoice_number')->nullable();
            $table->decimal('hours_value')->nullable();
            $table->decimal('hours_value_netto')->nullable();
            $table->decimal('hours_value_gross')->nullable();
            $table->decimal('hours_value_vat')->nullable();
            $table->decimal('fixed_price')->nullable();
            $table->decimal('fixed_price_gross')->nullable();
            $table->decimal('fixed_price_vat')->nullable();
            $table->decimal('overtime_value')->nullable();
            $table->decimal('overtime_value_netto')->nullable();
            $table->decimal('overtime_value_gross')->nullable();
            $table->decimal('overtime_value_vat')->nullable();
            $table->decimal('oncall_value_10')->nullable();
            $table->decimal('oncall_value_netto_10')->nullable();
            $table->decimal('oncall_value_gross_10')->nullable();
            $table->decimal('oncall_value_vat_10')->nullable();
            $table->decimal('oncall_value_30')->nullable();
            $table->decimal('oncall_value_netto_30')->nullable();
            $table->decimal('oncall_value_gross_30')->nullable();
            $table->decimal('oncall_value_vat_30')->nullable();
            $table->string('internal_invoice_number')->nullable();
            $table->text('rejection_type')->nullable();
            $table->text('rejection_description')->nullable();
            $table->boolean('is_correction')->nullable();
            $table->boolean('is_accepted')->default(0);
            $table->boolean('status')->nullable();
            $table->boolean('eu_vat')->default(0);
            $table->decimal('discount')->nullable();
            $table->text('discount_description')->nullable();
            $table->decimal('discount_gross')->nullable();
            $table->decimal('discount_vat')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('currency_value')->nullable();
            $table->timestamp('payment_date')->nullable();
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
        Schema::dropIfExists('invoices');
    }
}
