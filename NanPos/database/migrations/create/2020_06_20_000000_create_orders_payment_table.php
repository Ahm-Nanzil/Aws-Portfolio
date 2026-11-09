<?php

/**
 * Table Migration
 **/

use App\Classes\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( ! Schema::hasTable( 'possoft_orders_payments' ) ) {
            Schema::createIfMissing( 'possoft_orders_payments', function ( Blueprint $table ) {
                $table->bigIncrements( 'id' );
                $table->integer( 'order_id' );
                $table->decimal( 'value', 18, 5 )->default( 0 );
                $table->integer( 'author_id' );
                $table->string( 'identifier' );
                $table->string( 'uuid' )->nullable();
                $table->timestamps();
            } );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ( Schema::hasTable( 'possoft_orders_payments' ) ) {
            Schema::dropIfExists( 'possoft_orders_payments' );
        }
    }
};
