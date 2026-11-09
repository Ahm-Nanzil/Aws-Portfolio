<?php

use App\Classes\Schema;
use App\Models\Permission;
use App\Models\Role;
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
        $createInstalment = Permission::firstOrNew( [ 'namespace' => 'possoft.create.orders-instalments' ] );
        $createInstalment->namespace = 'possoft.create.orders-instalments';
        $createInstalment->name = __( 'Create Instalment' );
        $createInstalment->description = __( 'Allow the user to create instalments.' );
        $createInstalment->save();

        $updateInstalment = Permission::firstOrNew( [ 'namespace' => 'possoft.update.orders-instalments' ] );
        $updateInstalment->namespace = 'possoft.update.orders-instalments';
        $updateInstalment->name = __( 'Update Instalment' );
        $updateInstalment->description = __( 'Allow the user to update instalments.' );
        $updateInstalment->save();

        $readInstalment = Permission::firstOrNew( [ 'namespace' => 'possoft.read.orders-instalments' ] );
        $readInstalment->namespace = 'possoft.read.orders-instalments';
        $readInstalment->name = __( 'Read Instalment' );
        $readInstalment->description = __( 'Allow the user to read instalments.' );
        $readInstalment->save();

        $deleteInstalments = Permission::firstOrNew( [ 'namespace' => 'possoft.delete.orders-instalments' ] );
        $deleteInstalments->namespace = 'possoft.delete.orders-instalments';
        $deleteInstalments->name = __( 'Delete Instalment' );
        $deleteInstalments->description = __( 'Allow the user to delete instalments.' );
        $deleteInstalments->save();

        Role::namespace( 'admin' )->addPermissions( [
            $createInstalment,
            $updateInstalment,
            $readInstalment,
            $deleteInstalments,
        ] );

        Role::namespace( 'possoft.store.administrator' )->addPermissions( [
            $createInstalment,
            $updateInstalment,
            $readInstalment,
            $deleteInstalments,
        ] );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ( Schema::hasTable( 'possoft_permissions' ) && Schema::hasTable( 'possoft_roles' ) ) {
            collect( [
                'possoft.create.orders-instalments',
                'possoft.update.orders-instalments',
                'possoft.read.orders-instalments',
                'possoft.delete.orders-instalments',
            ] )->each( function ( $identifier ) {
                $permission = Permission::where( 'namespace', $identifier
                )->first();

                $permission->removeFromRoles();
                $permission->delete();
            } );
        }
    }
};
