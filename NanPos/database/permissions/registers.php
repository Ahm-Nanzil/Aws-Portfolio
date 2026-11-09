<?php

use App\Models\Permission;

if ( defined( 'NEXO_CREATE_PERMISSIONS' ) ) {
    $registers = Permission::firstOrNew( [ 'namespace' => 'possoft.create.registers' ] );
    $registers->name = __( 'Create Registers' );
    $registers->namespace = 'possoft.create.registers';
    $registers->description = __( 'Let the user create registers' );
    $registers->save();

    $registers = Permission::firstOrNew( [ 'namespace' => 'possoft.delete.registers' ] );
    $registers->name = __( 'Delete Registers' );
    $registers->namespace = 'possoft.delete.registers';
    $registers->description = __( 'Let the user delete registers' );
    $registers->save();

    $registers = Permission::firstOrNew( [ 'namespace' => 'possoft.update.registers' ] );
    $registers->name = __( 'Update Registers' );
    $registers->namespace = 'possoft.update.registers';
    $registers->description = __( 'Let the user update registers' );
    $registers->save();

    $registers = Permission::firstOrNew( [ 'namespace' => 'possoft.read.registers' ] );
    $registers->name = __( 'Read Registers' );
    $registers->namespace = 'possoft.read.registers';
    $registers->description = __( 'Let the user read registers' );
    $registers->save();

    $registers = Permission::firstOrNew( [ 'namespace' => 'possoft.read.registers-history' ] );
    $registers->name = __( 'Read Registers History' );
    $registers->namespace = 'possoft.read.registers-history';
    $registers->description = __( 'Let the user read registers history' );
    $registers->save();

    $registers = Permission::firstOrNew( [ 'namespace' => 'possoft.use.registers' ] );
    $registers->name = __( 'Read Use Registers' );
    $registers->namespace = 'possoft.use.registers';
    $registers->description = __( 'Let the user use registers' );
    $registers->save();
}
