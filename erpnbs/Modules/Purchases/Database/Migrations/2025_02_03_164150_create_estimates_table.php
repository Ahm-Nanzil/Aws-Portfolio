<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('purchase_request_id')->nullable();
            $table->string('estimate_number')->nullable();
            $table->unsignedBigInteger('buyer_id')->nullable();
            $table->string('currency')->nullable();
            $table->date('estimate_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('discount_type', ['before_tax', 'after_tax'])->nullable();
            $table->decimal('subtotal', 15, 2)->default(0)->nullable();
            $table->decimal('total_discount', 15, 2)->default(0)->nullable();
            $table->decimal('shipping_fee', 15, 2)->default(0)->nullable();
            $table->decimal('grand_total', 15, 2)->default(0)->nullable();
            $table->text('vendor_note')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->unsignedInteger('created_by')->nullable();

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
        Schema::dropIfExists('estimates');
    }
};
