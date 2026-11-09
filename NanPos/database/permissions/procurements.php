<?php

use App\Models\Permission;

if ( defined( 'NEXO_CREATE_PERMISSIONS' ) ) {
    $procurements = Permission::firstOrNew( [ 'namespace' => 'possoft.create.procurements' ] );
    $procurements->name = __( 'Create Procurements' );
    $procurements->namespace = 'possoft.create.procurements';
    $procurements->description = __( 'Let the user create procurements' );
    $procurements->save();

    $procurements = Permission::firstOrNew( [ 'namespace' => 'possoft.delete.procurements' ] );
    $procurements->name = __( 'Delete Procurements' );
    $procurements->namespace = 'possoft.delete.procurements';
    $procurements->description = __( 'Let the user delete procurements' );
    $procurements->save();

    $procurements = Permission::firstOrNew( [ 'namespace' => 'possoft.update.procurements' ] );
    $procurements->name = __( 'Update Procurements' );
    $procurements->namespace = 'possoft.update.procurements';
    $procurements->description = __( 'Let the user update procurements' );
    $procurements->save();

    $procurements = Permission::firstOrNew( [ 'namespace' => 'possoft.read.procurements' ] );
    $procurements->name = __( 'Read Procurements' );
    $procurements->namespace = 'possoft.read.procurements';
    $procurements->description = __( 'Let the user read procurements' );
    $procurements->save();
}
