<?php

use App\Models\Permission;

if ( defined( 'NEXO_CREATE_PERMISSIONS' ) ) {
    $reward = Permission::firstOrNew( [ 'namespace' => 'possoft.create.rewards' ] );
    $reward->name = __( 'Create Rewards' );
    $reward->namespace = 'possoft.create.rewards';
    $reward->description = __( 'Let the user create Rewards' );
    $reward->save();

    $reward = Permission::firstOrNew( [ 'namespace' => 'possoft.delete.rewards' ] );
    $reward->name = __( 'Delete Rewards' );
    $reward->namespace = 'possoft.delete.rewards';
    $reward->description = __( 'Let the user delete Rewards' );
    $reward->save();

    $reward = Permission::firstOrNew( [ 'namespace' => 'possoft.update.rewards' ] );
    $reward->name = __( 'Update Rewards' );
    $reward->namespace = 'possoft.update.rewards';
    $reward->description = __( 'Let the user update Rewards' );
    $reward->save();

    $reward = Permission::firstOrNew( [ 'namespace' => 'possoft.read.rewards' ] );
    $reward->name = __( 'Read Rewards' );
    $reward->namespace = 'possoft.read.rewards';
    $reward->description = __( 'Let the user read Rewards' );
    $reward->save();
}
