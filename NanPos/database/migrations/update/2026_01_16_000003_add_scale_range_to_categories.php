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
        Schema::table( 'possoft_products_categories', function ( Blueprint $table ) {
            if ( ! Schema::hasColumn( 'possoft_products_categories', 'scale_range_id' ) ) {
                $table->unsignedBigInteger( 'scale_range_id' )->nullable()->after( 'parent_id' );
                $table->foreign( 'scale_range_id' )
                    ->references( 'id' )
                    ->on( 'possoft_scale_ranges' )
                    ->onDelete( 'set null' );
            }
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table( 'possoft_products_categories', function ( Blueprint $table ) {
            if ( Schema::hasColumn( 'possoft_products_categories', 'scale_range_id' ) ) {
                $table->dropForeign( [ 'scale_range_id' ] );
                $table->dropColumn( 'scale_range_id' );
            }
        } );
    }
};
