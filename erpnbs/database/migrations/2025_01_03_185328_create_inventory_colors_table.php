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
        Schema::create('inventory_colors', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('color_code')->unique(); // Color Code
            $table->string('color_name'); // Color Name
            $table->string('color_hex'); // Color Hex
            $table->integer('order')->default(0); // Order
            $table->text('note')->nullable(); // Note
            $table->boolean('display')->default(true); // Display (Checklist)
            $table->timestamps(); // Created_at and Updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_colors');
    }
};
