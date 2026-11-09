<?php

namespace App\Services;

use App\Classes\Hook;
use App\Classes\Schema;
use App\Events\AfterHardResetEvent;
use App\Events\BeforeHardResetEvent;
use App\Models\Customer;
use App\Models\Option;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ResetService
{
    public function softReset()
    {
        $tables = Hook::filter( 'ns-wipeable-tables', [
            'possoft_coupons',
            'possoft_coupons_products',
            'possoft_coupons_categories',
            'possoft_coupons_customers',
            'possoft_coupons_customers_groups',

            'possoft_customers_account_history',
            'possoft_customers_addresses',
            'possoft_customers_coupons',
            'possoft_customers_groups',
            'possoft_customers_rewards',

            'possoft_dashboard_days',
            'possoft_dashboard_weeks',
            'possoft_dashboard_months',

            'possoft_transactions_histories',
            'possoft_accounting_journals',
            'possoft_transactions_actions_rule_lines',
            'possoft_transactions_actions_rules',
            'possoft_transactions',
            'possoft_transactions_accounts',
            'possoft_transactions_balance_days',
            'possoft_transactions_balance_months',

            'possoft_medias',
            'possoft_notifications',

            'possoft_orders',
            'possoft_orders_addresses',
            'possoft_orders_count',
            'possoft_orders_coupons',
            'possoft_orders_metas',
            'possoft_orders_payments',
            'possoft_orders_products',
            'possoft_orders_products_refunds',
            'possoft_orders_refunds',
            'possoft_orders_storage',
            'possoft_orders_taxes',

            'possoft_procurements',
            'possoft_procurements_products',

            'possoft_products',
            'possoft_products_categories',
            'possoft_products_histories',
            'possoft_products_histories_combined',
            'possoft_products_galleries',
            'possoft_products_metas',
            'possoft_products_taxes',
            'possoft_products_unit_quantities',

            'possoft_payments_types',

            'possoft_providers',

            'possoft_registers',
            'possoft_registers_history',

            'possoft_rewards_system',
            'possoft_rewards_system_rules',

            'possoft_taxes',
            'possoft_taxes_groups',

            'possoft_units',
            'possoft_units_groups',
        ] );

        foreach ( $tables as $table ) {
            if ( Hook::filter( 'ns-reset-table', $table ) !== false && Schema::hasTable( Hook::filter( 'ns-reset-table', $table ) ) ) {
                DB::table( Hook::filter( 'ns-table-name', $table ) )->truncate();
            }
        }

        /**
         * @var CustomerService $customerService
         */
        $customerService = app()->make( CustomerService::class );

        /**
         * Customers stills needs to be cleared
         * so we'll remove them manually.
         */
        Customer::get()->each( fn( $customer ) => $customerService->delete( $customer ) );

        /**
         * We'll delete all options where key starts with "ns_"
         * as this is a reserved key for the system, we can safely delete it
         * but excluding some options provided in an array
         */
        Option::where( 'key', 'LIKE', 'ns\_%' )
            ->where( 'key', 'NOT LIKE', 'ns\_pa_%' )
            ->where( 'key', 'NOT LIKE', 'ns\_gastro_%' )
            ->where( 'key', 'NOT LIKE', 'ns\_sms_%' )
            ->where( 'key', 'NOT LIKE', 'ns\_email_%' )
            ->where( 'key', 'NOT LIKE', 'ns-stocktransfers%' )
            ->whereNotIn( 'key', [
                'ns_store_name',
                'ns_store_email',
                'ns_date_format',
                'ns_datetime_format',
                'ns_currency_precision',
                'ns_currency_iso',
                'ns_currency_symbol',
                'enabled_modules',
                'ns_pos_order_types',
            ] )->delete();

        return [
            'status' => 'success',
            'message' => __( 'The table has been truncated.' ),
        ];
    }

    /**
     * Will completely wipe the database
     * forcing a new installation to be made
     */
    public function hardReset(): array
    {
        BeforeHardResetEvent::dispatch();

        /**
         * this will only apply clearing all tables
         * when we're not using sqlite.
         */
        if ( env( 'DB_CONNECTION' ) !== 'sqlite' ) {
            $tables = DB::select( 'SHOW TABLES' );

            foreach ( $tables as $table ) {
                $table_name = array_values( (array) $table )[0];
                DB::statement( 'SET FOREIGN_KEY_CHECKS = 0' );
                DB::statement( "DROP TABLE `$table_name`" );
                DB::statement( 'SET FOREIGN_KEY_CHECKS = 1' );
            }
        } else {
            DB::statement( 'PRAGMA writable_schema = 1' );

            try {
                DB::statement( "DELETE FROM sqlite_master WHERE type IN ('table', 'index', 'trigger', 'view')" );
            } finally {
                DB::statement( 'PRAGMA writable_schema = 0' );
            }

            DB::statement( 'VACUUM' );
        }

        Artisan::call( 'key:generate', [ '--force' => true ] );
        Artisan::call( 'ns:cookie generate' );

        exec( 'rm -rf public/storage' );

        AfterHardResetEvent::dispatch();

        return [
            'status' => 'success',
            'message' => __( 'The database has been wiped out.' ),
        ];
    }

    public function handleCustom( $data )
    {
        /**
         * @var string $mode
         * @var bool   $create_sales
         * @var bool   $create_procurements
         */
        extract( $data );

        return Hook::filter( 'ns-handle-custom-reset', [
            'status' => 'error',
            'message' => __( 'No custom handler for the reset "' . $mode . '"' ),
        ], $data );
    }
}
