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
        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('product_code');
            $table->string('warehouse_code');
            $table->string('warehouse_name');
            $table->date('voucher_date');
            $table->integer('opening_stock');
            $table->integer('closing_stock');
            $table->string('lot_number_quantity_sold');
            $table->date('expiry_date')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('note')->nullable();
            $table->string('status')->nullable(); // e.g., active, inactive
            $table->timestamps(); // Created_at and Updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_histories');
    }
};
