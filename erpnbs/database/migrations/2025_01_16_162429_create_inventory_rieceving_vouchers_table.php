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
        Schema::create('inventory_rieceving_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_docket_number')->nullable();
            $table->date('accounting_date')->nullable();
            $table->date('voucher_date')->nullable();
            $table->string('purchase_order')->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('buyer')->nullable();
            $table->string('project')->nullable();
            $table->string('type')->nullable();
            $table->string('department')->nullable();
            $table->string('requester')->nullable();
            $table->string('deliverer')->nullable();
            $table->string('warehouse_name')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('invoice_no')->nullable();
            $table->text('notes')->nullable();

            // Item details
            $table->text('item_description')->nullable();
            $table->string('item_warehouse_name')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->decimal('tax', 5, 2);
            $table->string('lot_number')->nullable();
            $table->date('date_manufacture')->nullable();
            $table->date('item_expiry_date')->nullable();
            $table->decimal('amount', 10, 2);

            // Totals
            $table->decimal('total_goods_value', 10, 2);
            $table->decimal('value_of_inventory', 10, 2);
            $table->decimal('total_tax_amount', 10, 2);
            $table->decimal('total_payment', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_rieceving_vouchers');
    }
};
