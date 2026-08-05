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
        Schema::create('estimate_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimate_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name')->nullable();
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('subtotal_before_tax', 15, 2)->nullable();
            $table->string('tax')->default('No Tax')->nullable();
            $table->decimal('tax_value', 15, 2)->default(0)->nullable();
            $table->decimal('subtotal_after_tax', 15, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0)->nullable();
            $table->decimal('discount_money', 15, 2)->default(0)->nullable();
            $table->decimal('total', 15, 2)->nullable();
            $table->unsignedInteger('created_by')->nullable();

            $table->timestamps();

            $table->foreign('estimate_id')->references('id')->on('estimates')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('product_services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estimate_items');
    }
};
