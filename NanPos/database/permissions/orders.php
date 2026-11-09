<?php

use App\Models\Permission;

if ( defined( 'NEXO_CREATE_PERMISSIONS' ) ) {
    $orders = Permission::firstOrNew( [ 'namespace' => 'possoft.create.orders' ] );
    $orders->name = __( 'Create Orders' );
    $orders->namespace = 'possoft.create.orders';
    $orders->description = __( 'Let the user create orders' );
    $orders->save();

    $orders = Permission::firstOrNew( [ 'namespace' => 'possoft.delete.orders' ] );
    $orders->name = __( 'Delete Orders' );
    $orders->namespace = 'possoft.delete.orders';
    $orders->description = __( 'Let the user delete orders' );
    $orders->save();

    $orders = Permission::firstOrNew( [ 'namespace' => 'possoft.update.orders' ] );
    $orders->name = __( 'Update Orders' );
    $orders->namespace = 'possoft.update.orders';
    $orders->description = __( 'Let the user update orders' );
    $orders->save();

    $orders = Permission::firstOrNew( [ 'namespace' => 'possoft.read.orders' ] );
    $orders->name = __( 'Read Orders' );
    $orders->namespace = 'possoft.read.orders';
    $orders->description = __( 'Let the user read orders' );
    $orders->save();

    $orders = Permission::firstOrNew( [ 'namespace' => 'possoft.void.orders' ] );
    $orders->name = __( 'Void Order' );
    $orders->namespace = 'possoft.void.orders';
    $orders->description = __( 'Let the user void orders' );
    $orders->save();

    $orders = Permission::firstOrNew( [ 'namespace' => 'possoft.refund.orders' ] );
    $orders->name = __( 'Refund Order' );
    $orders->namespace = 'possoft.refund.orders';
    $orders->description = __( 'Let the user refund orders' );
    $orders->save();

    $orders = Permission::firstOrNew( [ 'namespace' => 'possoft.make-payment.orders' ] );
    $orders->name = __( 'Make Payment To orders' );
    $orders->namespace = 'possoft.make-payment.orders';
    $orders->description = __( 'Allow the user to make payments to orders.' );
    $orders->save();
}
