<?php

use App\Models\Permission;

if ( defined( 'NEXO_CREATE_PERMISSIONS' ) ) {
    $customers = Permission::firstOrNew( [ 'namespace' => 'possoft.create.customers' ] );
    $customers->name = __( 'Create Customers' );
    $customers->namespace = 'possoft.create.customers';
    $customers->description = __( 'Let the user create customers.' );
    $customers->save();

    $customers = Permission::firstOrNew( [ 'namespace' => 'possoft.delete.customers' ] );
    $customers->name = __( 'Delete Customers' );
    $customers->namespace = 'possoft.delete.customers';
    $customers->description = __( 'Let the user delete customers.' );
    $customers->save();

    $customers = Permission::firstOrNew( [ 'namespace' => 'possoft.update.customers' ] );
    $customers->name = __( 'Update Customers' );
    $customers->namespace = 'possoft.update.customers';
    $customers->description = __( 'Let the user update customers.' );
    $customers->save();

    $customers = Permission::firstOrNew( [ 'namespace' => 'possoft.read.customers' ] );
    $customers->name = __( 'Read Customers' );
    $customers->namespace = 'possoft.read.customers';
    $customers->description = __( 'Let the user read customers.' );
    $customers->save();

    $customers = Permission::firstOrNew( [ 'namespace' => 'possoft.import.customers' ] );
    $customers->name = __( 'Import Customers' );
    $customers->namespace = 'possoft.import.customers';
    $customers->description = __( 'Let the user import customers.' );
    $customers->save();

    $permission = Permission::firstOrNew( [ 'namespace' => 'possoft.customers.manage-account-history' ] );
    $permission->namespace = 'possoft.customers.manage-account-history';
    $permission->name = __( 'Manage Customer Account History' );
    $permission->description = __( 'Can add, deduct amount from each customers account.' );
    $permission->save();
}
