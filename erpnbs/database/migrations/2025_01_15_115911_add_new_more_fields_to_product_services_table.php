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
        Schema::table('product_services', function (Blueprint $table) {
            $table->string('images')->nullable();
            $table->string('product_type_code')->nullable();
            $table->string('product_type_name')->nullable();
            $table->string('group_name')->nullable();
            $table->string('warehouse_name')->nullable();
            $table->string('tags')->nullable();
            $table->float('inventory')->default(0)->nullable();
            $table->string('main_category')->nullable();
            $table->string('sub_category')->nullable();
            $table->string('child_category')->nullable();
            $table->string('tax_2')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('minimum_stock')->default(0)->nullable();
            $table->integer('maximum_stock')->default(0)->nullable();
            $table->decimal('price_after_tax', 16, 2)->default(0.0)->nullable();
            $table->string('mother')->nullable();
            $table->date('date')->nullable();
            $table->string('inventory_vendor')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('uom')->nullable();
            $table->decimal('temperature_inwards_delivery', 8, 2)->nullable();
            $table->decimal('target_penjualan', 16, 2)->default(0.0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_services', function (Blueprint $table) {
            $table->dropColumn([
                'images',
                'product_type_code',
                'product_type_name',
                'group_name',
                'warehouse_name',
                'tags',
                'inventory',
                'main_category',
                'sub_category',
                'child_category',
                'tax_2',
                'status',
                'minimum_stock',
                'maximum_stock',
                'price_after_tax',
                'mother',
                'date',
                'inventory_vendor',
                'expiration_date',
                'uom',
                'temperature_inwards_delivery',
                'target_penjualan',
            ]);
        });
    }
};
