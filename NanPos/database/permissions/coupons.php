<?php

use App\Models\Permission;

if ( defined( 'NEXO_CREATE_PERMISSIONS' ) ) {
    $coupons = Permission::firstOrNew( [ 'namespace' => 'possoft.create.coupons' ] );
    $coupons->name = __( 'Create Coupons' );
    $coupons->namespace = 'possoft.create.coupons';
    $coupons->description = __( 'Let the user create coupons' );
    $coupons->save();

    $coupons = Permission::firstOrNew( [ 'namespace' => 'possoft.delete.coupons' ] );
    $coupons->name = __( 'Delete Coupons' );
    $coupons->namespace = 'possoft.delete.coupons';
    $coupons->description = __( 'Let the user delete coupons' );
    $coupons->save();

    $coupons = Permission::firstOrNew( [ 'namespace' => 'possoft.update.coupons' ] );
    $coupons->name = __( 'Update Coupons' );
    $coupons->namespace = 'possoft.update.coupons';
    $coupons->description = __( 'Let the user update coupons' );
    $coupons->save();

    $coupons = Permission::firstOrNew( [ 'namespace' => 'possoft.read.coupons' ] );
    $coupons->name = __( 'Read Coupons' );
    $coupons->namespace = 'possoft.read.coupons';
    $coupons->description = __( 'Let the user read coupons' );
    $coupons->save();
}
