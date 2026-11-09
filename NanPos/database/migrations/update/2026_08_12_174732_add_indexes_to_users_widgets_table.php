<?php

use App\Classes\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        if ( ! Schema::hasTable( 'possoft_users_widgets' ) ) {
            return;
        }

        if ( Schema::hasColumns( 'possoft_users_widgets', [ 'user_id', 'identifier' ] ) && ! Schema::hasIndex( 'possoft_users_widgets', 'users_widgets_user_identifier_unique' ) ) {
            Schema::table( 'possoft_users_widgets', function ( Blueprint $table ): void {
                $table->unique( [ 'user_id', 'identifier' ], 'users_widgets_user_identifier_unique' );
            } );
        }

        if ( Schema::hasColumns( 'possoft_users_widgets', [ 'user_id', 'position' ] ) && ! Schema::hasIndex( 'possoft_users_widgets', 'users_widgets_user_position_index' ) ) {
            Schema::table( 'possoft_users_widgets', function ( Blueprint $table ): void {
                $table->index( [ 'user_id', 'position' ], 'users_widgets_user_position_index' );
            } );
        }
    }

    public function down(): void
    {
        if ( ! Schema::hasTable( 'possoft_users_widgets' ) ) {
            return;
        }

        if ( Schema::hasIndex( 'possoft_users_widgets', 'users_widgets_user_identifier_unique' ) ) {
            Schema::table( 'possoft_users_widgets', function ( Blueprint $table ): void {
                $table->dropUnique( 'users_widgets_user_identifier_unique' );
            } );
        }

        if ( Schema::hasIndex( 'possoft_users_widgets', 'users_widgets_user_position_index' ) ) {
            Schema::table( 'possoft_users_widgets', function ( Blueprint $table ): void {
                $table->dropIndex( 'users_widgets_user_position_index' );
            } );
        }
    }
};
