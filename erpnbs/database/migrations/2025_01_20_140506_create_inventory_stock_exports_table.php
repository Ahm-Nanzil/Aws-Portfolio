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
        Schema::create('inventory_stock_exports', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->nullable();
            $table->date('accounting_date')->nullable();
            $table->date('voucher_date')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('receiver')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('requester_id')->nullable();
            $table->unsignedBigInteger('sales_person_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->json('items')->nullable(); // Store items as a JSON to handle multiple rows
            $table->decimal('summary_subtotal', 10, 2)->default(0.00);
            $table->decimal('summary_discount', 10, 2)->default(0.00);
            $table->decimal('summary_shipping_fee', 10, 2)->default(0.00);
            $table->decimal('summary_total_payment', 10, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_exports');
    }
};
