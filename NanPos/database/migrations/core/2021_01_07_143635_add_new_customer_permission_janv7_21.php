<?php

use App\Classes\Schema;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Determine whether the migration
     * should execute when we're accessing
     * a multistore instance.
     */
    public function runOnMultiStore()
    {
        return false;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( ! Permission::namespace( 'possoft.customers.manage-account-history' ) instanceof Permission ) {
            $permission = Permission::firstOrNew( [ 'namespace' => 'possoft.customers.manage-account-history' ] );
            $permission->namespace = 'possoft.customers.manage-account-history';
            $permission->name = __( 'Manage Customers Account History' );
            $permission->description = __( 'Allow to manage customer virtual deposit account history.' );
            $permission->save();
        }

        Role::namespace( 'admin' )->addPermissions( 'possoft.customers.manage-account-history' );
        Role::namespace( 'possoft.store.administrator' )->addPermissions( 'possoft.customers.manage-account-history' );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ( Schema::hasTable( 'possoft_permissions' ) ) {
            $permission = Permission::namespace( 'possoft.customers.manage-account-history' );

            if ( $permission instanceof Permission ) {
                RolePermission::where( 'permission_id', $permission->id )->delete();
                $permission->delete();
            }
        }
    }
};
