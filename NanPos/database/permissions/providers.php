<?php

use App\Models\Permission;

if ( defined( 'NEXO_CREATE_PERMISSIONS' ) ) {
    $providers = Permission::firstOrNew( [ 'namespace' => 'possoft.create.providers' ] );
    $providers->name = __( 'Create Providers' );
    $providers->namespace = 'possoft.create.providers';
    $providers->description = __( 'Let the user create providers' );
    $providers->save();

    $providers = Permission::firstOrNew( [ 'namespace' => 'possoft.delete.providers' ] );
    $providers->name = __( 'Delete Providers' );
    $providers->namespace = 'possoft.delete.providers';
    $providers->description = __( 'Let the user delete providers' );
    $providers->save();

    $providers = Permission::firstOrNew( [ 'namespace' => 'possoft.update.providers' ] );
    $providers->name = __( 'Update Providers' );
    $providers->namespace = 'possoft.update.providers';
    $providers->description = __( 'Let the user update providers' );
    $providers->save();

    $providers = Permission::firstOrNew( [ 'namespace' => 'possoft.read.providers' ] );
    $providers->name = __( 'Read Providers' );
    $providers->namespace = 'possoft.read.providers';
    $providers->description = __( 'Let the user read providers' );
    $providers->save();
}
