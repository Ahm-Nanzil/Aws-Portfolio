<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_product_categories_children', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('main_category_id')->nullable(); // Foreign key column
            $table->unsignedBigInteger('sub_category_id')->nullable(); // Foreign key column
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->integer('order')->default(1);
            $table->boolean('display')->default(true);
            $table->text('note')->nullable();
            $table->timestamps();

            // Define the foreign key constraint
            $table->foreign('main_category_id')
                  ->references('id')
                  ->on('inventory_product_categories_mains')
                  ->onDelete('cascade');
            $table->foreign('sub_category_id')
                  ->references('id')
                  ->on('inventory_product_categories_subs')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_product_categories_children');
    }
};
