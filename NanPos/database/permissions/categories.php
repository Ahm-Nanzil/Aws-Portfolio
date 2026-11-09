<?php

use App\Models\Permission;

if ( defined( 'NEXO_CREATE_PERMISSIONS' ) ) {
    $category = Permission::firstOrNew( [ 'namespace' => 'possoft.create.categories' ] );
    $category->name = __( 'Create Categories' );
    $category->namespace = 'possoft.create.categories';
    $category->description = __( 'Let the user create products categories.' );
    $category->save();

    $category = Permission::firstOrNew( [ 'namespace' => 'possoft.delete.categories' ] );
    $category->name = __( 'Delete Categories' );
    $category->namespace = 'possoft.delete.categories';
    $category->description = __( 'Let the user delete products categories.' );
    $category->save();

    $category = Permission::firstOrNew( [ 'namespace' => 'possoft.update.categories' ] );
    $category->name = __( 'Update Categories' );
    $category->namespace = 'possoft.update.categories';
    $category->description = __( 'Let the user update products categories.' );
    $category->save();

    $category = Permission::firstOrNew( [ 'namespace' => 'possoft.read.categories' ] );
    $category->name = __( 'Read Categories' );
    $category->namespace = 'possoft.read.categories';
    $category->description = __( 'Let the user read products categories.' );
    $category->save();
}
