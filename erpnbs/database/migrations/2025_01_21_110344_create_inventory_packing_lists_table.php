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
        Schema::create('inventory_packing_lists', function (Blueprint $table) {
            $table->id();

            // Stock Export Information
            $table->string('stock_export_id')->nullable(); // Assuming stock_exports is another table
            $table->string('customer_id')->nullable(); // Assuming customers is another table
            $table->text('bill_to')->nullable(); // Billing address (could be JSON or text)
            $table->text('ship_to')->nullable(); // Shipping address (could be JSON or text)
            $table->string('packing_list_number')->nullable();

            // Dimensions and Weight
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('volume', 8, 2)->nullable();

            // Client and Admin Notes
            $table->text('client_note')->nullable();
            $table->text('admin_note')->nullable();

            // Item details (Assuming these are stored as rows in a related table)
            $table->json('items')->nullable(); // Storing item details as JSON (item_description, quantity, etc.)

            // Totals
            $table->decimal('subtotal', 10, 2)->default(0)->nullable();
            $table->decimal('additional_discount', 10, 2)->default(0)->nullable();
            $table->decimal('total_discount', 10, 2)->default(0)->nullable();
            $table->decimal('shipping_fee', 10, 2)->default(0)->nullable();
            $table->decimal('total_payment', 10, 2)->default(0)->nullable();
            $table->string('created_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_packing_lists');
    }
};
