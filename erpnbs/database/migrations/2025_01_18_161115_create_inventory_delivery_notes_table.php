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
        Schema::create('inventory_delivery_notes', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('internal_delivery_name')->nullable(); // Internal Delivery Note Name
            $table->string('internal_delivery_number')->nullable(); // Internal Delivery Note Number
            $table->date('accounting_date')->nullable(); // Accounting Date
            $table->date('voucher_date')->nullable(); // Voucher Date
            $table->string('deliverer')->nullable(); // Deliverer
            $table->text('notes')->nullable(); // Notes
            $table->string('item_description')->nullable(); // Item description
            $table->string('from_stock_name')->nullable(); // Source stock location
            $table->string('to_stock_name')->nullable(); // Destination stock location
            $table->integer('available_quantity')->nullable(); // Available Quantity
            $table->integer('quantity')->nullable(); // Transferred Quantity
            $table->decimal('unit_price', 15, 2)->nullable(); // Unit Price
            $table->decimal('amount', 15, 2)->nullable(); // Amount
            $table->decimal('total_amount', 15, 2)->default(0); // Total Amount
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_delivery_notes');
    }
};
