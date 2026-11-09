<?php

use App\Models\Permission;

if ( defined( 'NEXO_CREATE_PERMISSIONS' ) ) {
    $taxes = Permission::firstOrNew( [ 'namespace' => 'possoft.create.taxes' ] );
    $taxes->name = __( 'Create Taxes' );
    $taxes->namespace = 'possoft.create.taxes';
    $taxes->description = __( 'Let the user create taxes' );
    $taxes->save();

    $taxes = Permission::firstOrNew( [ 'namespace' => 'possoft.delete.taxes' ] );
    $taxes->name = __( 'Delete Taxes' );
    $taxes->namespace = 'possoft.delete.taxes';
    $taxes->description = __( 'Let the user delete taxes' );
    $taxes->save();

    $taxes = Permission::firstOrNew( [ 'namespace' => 'possoft.update.taxes' ] );
    $taxes->name = __( 'Update Taxes' );
    $taxes->namespace = 'possoft.update.taxes';
    $taxes->description = __( 'Let the user update taxes' );
    $taxes->save();

    $taxes = Permission::firstOrNew( [ 'namespace' => 'possoft.read.taxes' ] );
    $taxes->name = __( 'Read Taxes' );
    $taxes->namespace = 'possoft.read.taxes';
    $taxes->description = __( 'Let the user read taxes' );
    $taxes->save();
}
