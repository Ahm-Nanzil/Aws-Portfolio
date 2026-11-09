<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that have an `author` column which should be renamed to `author_id`.
     */
    protected array $tables = [
        'possoft_coupons',
        'possoft_customers_addresses',
        'possoft_customers_coupons',
        'possoft_customers_groups',
        'possoft_orders',
        'possoft_orders_addresses',
        'possoft_orders_coupons',
        'possoft_orders_metas',
        'possoft_orders_payments',
        'possoft_orders_products',
        'possoft_orders_products_refunds',
        'possoft_payments_types',
        'possoft_procurements',
        'possoft_procurements_products',
        'possoft_products',
        'possoft_products_categories',
        'possoft_products_galleries',
        'possoft_products_histories',
        'possoft_products_metas',
        'possoft_products_subitems',
        'possoft_products_taxes',
        'possoft_providers',
        'possoft_registers',
        'possoft_registers_history',
        'possoft_rewards_system',
        'possoft_rewards_system_rules',
        'possoft_roles',
        'possoft_scale_ranges',
        'possoft_taxes',
        'possoft_taxes_groups',
        'possoft_transactions',
        'possoft_transactions_accounts',
        'possoft_transactions_histories',
        'possoft_units',
        'possoft_units_groups',
        'possoft_users',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ( $this->tables as $table ) {
            if ( Schema::hasTable( $table ) && Schema::hasColumn( $table, 'author' ) && ! Schema::hasColumn( $table, 'author_id' ) ) {
                Schema::table( $table, function ( Blueprint $table ) {
                    $table->renameColumn( 'author', 'author_id' );
                } );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ( $this->tables as $table ) {
            if ( Schema::hasTable( $table ) && Schema::hasColumn( $table, 'author_id' ) && ! Schema::hasColumn( $table, 'author' ) ) {
                Schema::table( $table, function ( Blueprint $table ) {
                    $table->renameColumn( 'author_id', 'author' );
                } );
            }
        }
    }
};
