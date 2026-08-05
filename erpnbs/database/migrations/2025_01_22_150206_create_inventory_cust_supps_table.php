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
        Schema::create('inventory_cust_supps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('related_type_id')->nullable();
            $table->unsignedBigInteger('related_data_id')->nullable();
            $table->string('order_number')->nullable();
            $table->date('order_date')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->date('date_created')->nullable();
            $table->unsignedBigInteger('return_type_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('order_return_number')->nullable();
            $table->text('reason')->nullable();
            $table->text('admin_note')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0)->nullable();
            $table->decimal('additional_discount', 15, 2)->default(0)->nullable();
            $table->decimal('total_discount', 15, 2)->default(0)->nullable();
            $table->decimal('total_payment', 15, 2)->default(0)->nullable();
            $table->json('items')->nullable(); // Store item details in JSON format
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_cust_supps');
    }
};
