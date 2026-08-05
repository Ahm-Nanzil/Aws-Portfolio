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
        Schema::create('inventory_minmaxes', function (Blueprint $table) {
            $table->id();
            $table->string('commodity_code')->nullable();
            $table->string('commodity_name')->nullable();
            $table->string('sku_code')->nullable();
            $table->string('min_inventory_value')->nullable();
            $table->string('max_inventory_qty')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_minmaxes');
    }
};
