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
        Schema::table('warehouses', function (Blueprint $table) {
            $table->string('code')->nullable(); // Warehouse code
            $table->string('state')->nullable(); // State
            $table->string('postal_code')->nullable(); // Postal code
            $table->json('staffs')->nullable(); // Staffs (assuming this is stored as JSON)
            $table->boolean('display')->default(true); // Display checkbox
            $table->boolean('hide_when_out_of_stock')->default(false); // Hide when out of stock checkbox
            $table->text('note')->nullable(); // Notes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn(['code', 'state', 'postal_code', 'staffs', 'display', 'hide_when_out_of_stock', 'note']);

        });
    }
};
