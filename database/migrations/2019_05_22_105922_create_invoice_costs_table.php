<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_costs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('cost_value');
            $table->decimal('cost_vat');
            $table->decimal('cost_vat_only');
            $table->decimal('cost_vat_value');
            $table->string('cost_description')->nullable();
            $table->string('cost_files')->nullable();
            $table->unsignedInteger('invoice_id');
            $table->unsignedInteger('user_id');
            $table->string('cost_type');
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
        Schema::dropIfExists('invoice_costs');
    }
}
