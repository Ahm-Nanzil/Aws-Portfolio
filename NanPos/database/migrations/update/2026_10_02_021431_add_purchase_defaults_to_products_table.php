<?php

use App\Classes\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ( ! Schema::hasTable( 'possoft_products' ) ) {
            return;
        }

        if ( ! Schema::hasColumn( 'possoft_products', 'default_purchase_unit_id' ) ) {
            Schema::table( 'possoft_products', function ( Blueprint $table ) {
                $table->integer( 'default_purchase_unit_id' )->nullable()->after( 'auto_cogs' );
            } );
        }

        if ( ! Schema::hasColumn( 'possoft_products', 'scheduled_reorder_quantity' ) ) {
            Schema::table( 'possoft_products', function ( Blueprint $table ) {
                $table->float( 'scheduled_reorder_quantity' )->nullable()->default( 0 )->after( 'default_purchase_unit_id' );
            } );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ( ! Schema::hasTable( 'possoft_products' ) ) {
            return;
        }

        if ( Schema::hasColumn( 'possoft_products', 'scheduled_reorder_quantity' ) ) {
            Schema::table( 'possoft_products', function ( Blueprint $table ) {
                $table->dropColumn( 'scheduled_reorder_quantity' );
            } );
        }

        if ( Schema::hasColumn( 'possoft_products', 'default_purchase_unit_id' ) ) {
            Schema::table( 'possoft_products', function ( Blueprint $table ) {
                $table->dropColumn( 'default_purchase_unit_id' );
            } );
        }
    }
};
