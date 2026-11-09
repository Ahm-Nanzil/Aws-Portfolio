<?php

use App\Models\Permission;
use App\Models\Role;
use App\Widgets\ProfileWidget;

$storeCashier = Role::firstOrNew( [ 'namespace' => 'possoft.store.cashier' ] );
$storeCashier->name = __( 'Store Cashier' );
$storeCashier->namespace = 'possoft.store.cashier';
$storeCashier->locked = true;
$storeCashier->description = __( 'Has a control over the sale process.' );
$storeCashier->save();
$storeCashier->addPermissions( [ 'read.dashboard' ] );
$storeCashier->addPermissions( Permission::includes( '.profile' )->get()->map( fn( $permission ) => $permission->namespace ) );
$storeCashier->addPermissions( Permission::whereIn( 'namespace', [
    'possoft.create.orders',
    'possoft.update.orders',
    'possoft.void.orders',
    'possoft.refund.orders',
    'possoft.make-payment.orders',
    'possoft.read.registers',
    'possoft.read.customers',
    'possoft.create.orders-instaments',
    'possoft.update.orders-instaments',
    'possoft.read.orders-instaments',
    'possoft.customers.manage-account-history',
] )->get()->map( fn( $permission ) => $permission->namespace ) );

$storeCashier->addPermissions( Permission::whereIn( 'namespace', [
    'possoft.pos.access',
    'possoft.pos.read-products',
    'possoft.pos.read-own-orders',
    'possoft.pos.edit-purchase-price',
    'possoft.pos.edit-settings',
    'possoft.pos.products-discount',
    'possoft.pos.cart-discount',
    'possoft.pos.delete-order-product',
] )->get()->map( fn( $permission ) => $permission->namespace ) );

$storeCashier->addPermissions( Permission::whereIn( 'namespace', [
    ( new ProfileWidget )->getPermission(),
] )->get()->map( fn( $permission ) => $permission->namespace ) );
