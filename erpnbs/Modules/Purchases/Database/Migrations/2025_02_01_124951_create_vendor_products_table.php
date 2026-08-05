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
        Schema::create('vendor_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('vendor');
            $table->unsignedInteger('categories')->nullable();
            $table->json('products');  // Changed from unsignedInteger to json to store multiple products
            $table->unsignedInteger('created_by')->nullable();
            $table->date('datecreate');
            $table->timestamps();
            // $table->foreign('vendor')->references('id')->on('venders')->onDelete('cascade');
            // $table->foreign('categories')->references('id')->on('inventory_product_categories_mains')->onDelete('set null');
            // $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_products');
    }
};
