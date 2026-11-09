<?php

use App\Models\Permission;

if ( defined( 'NEXO_CREATE_PERMISSIONS' ) ) {
    $transaction = Permission::firstOrNew( [ 'namespace' => 'possoft.create.transactions' ] );
    $transaction->name = __( 'Create Transaction' );
    $transaction->namespace = 'possoft.create.transactions';
    $transaction->description = __( 'Let the user create transactions' );
    $transaction->save();

    $transaction = Permission::firstOrNew( [ 'namespace' => 'possoft.delete.transactions' ] );
    $transaction->name = __( 'Delete Transaction' );
    $transaction->namespace = 'possoft.delete.transactions';
    $transaction->description = __( 'Let the user delete transactions' );
    $transaction->save();

    $transaction = Permission::firstOrNew( [ 'namespace' => 'possoft.update.transactions' ] );
    $transaction->name = __( 'Update Transaction' );
    $transaction->namespace = 'possoft.update.transactions';
    $transaction->description = __( 'Let the user update transactions' );
    $transaction->save();

    $transaction = Permission::firstOrNew( [ 'namespace' => 'possoft.read.transactions' ] );
    $transaction->name = __( 'Read Transaction' );
    $transaction->namespace = 'possoft.read.transactions';
    $transaction->description = __( 'Let the user read transactions' );
    $transaction->save();

    $readCashFlowHistory = Permission::firstOrNew( [ 'namespace' => 'possoft.read.transactions-history' ] );
    $readCashFlowHistory->name = __( 'Read Transactions History' );
    $readCashFlowHistory->namespace = 'possoft.read.transactions-history';
    $readCashFlowHistory->description = __( 'Give access to the transactions history.' );
    $readCashFlowHistory->save();

    $deleteCashFlowHistory = Permission::firstOrNew( [ 'namespace' => 'possoft.delete.transactions-history' ] );
    $deleteCashFlowHistory->name = __( 'Delete Transactions History' );
    $deleteCashFlowHistory->namespace = 'possoft.delete.transactions-history';
    $deleteCashFlowHistory->description = __( 'Allow to delete an Transactions History.' );
    $deleteCashFlowHistory->save();

    $readCashFlowHistory = Permission::withNamespaceOrNew( 'possoft.update.transactions-history' );
    $readCashFlowHistory->name = __( 'Update Transactions History' );
    $readCashFlowHistory->namespace = 'possoft.update.transactions-history';
    $readCashFlowHistory->description = __( 'Allow to the Transactions History.' );
    $readCashFlowHistory->save();

    $createCashFlowHistory = Permission::withNamespaceOrNew( 'possoft.create.transactions-history' );
    $createCashFlowHistory->name = __( 'Create Transactions History' );
    $createCashFlowHistory->namespace = 'possoft.create.transactions-history';
    $createCashFlowHistory->description = __( 'Allow to create a Transactions History.' );
    $createCashFlowHistory->save();
}
