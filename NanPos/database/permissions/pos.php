<?php

use App\Models\Permission;
use App\Models\Role;

$posReadPermissions = [
    'possoft.pos.access' => [ __( 'POS: Access' ), __( 'Access the point of sale workspace.' ) ],
    'possoft.pos.read-products' => [ __( 'POS: Read Products' ), __( 'Browse the sale catalog through POS workflows.' ) ],
    'possoft.pos.read-own-orders' => [ __( 'POS: Read Own Orders' ), __( 'Read orders created by the current user through POS workflows.' ) ],
    'possoft.pos.read-all-orders' => [ __( 'POS: Read All Orders' ), __( 'Read every order through POS workflows.' ) ],
];

foreach ( $posReadPermissions as $namespace => [$name, $description] ) {
    if ( Permission::where( 'name', $name )->where( 'namespace', '!=', $namespace )->exists() ) {
        throw new RuntimeException( sprintf( 'The permission name "%s" is already assigned to another namespace.', $name ) );
    }

    $permission = Permission::firstOrNew( [ 'namespace' => $namespace ] );
    $permission->name = $name;
    $permission->namespace = $namespace;
    $permission->description = $description;
    $permission->save();
}

if ( ! Permission::namespace( 'possoft.pos.edit-purchase-price' ) instanceof Permission ) {
    $pos = Permission::firstOrNew( [ 'namespace' => 'possoft.pos.edit-purchase-price' ] );
    $pos->name = __( 'Edit Purchase Price' );
    $pos->namespace = 'possoft.pos.edit-purchase-price';
    $pos->description = __( 'Let the user edit the purchase price of products.' );
    $pos->save();
}

if ( ! Permission::namespace( 'possoft.pos.edit-settings' ) instanceof Permission ) {
    $pos = Permission::firstOrNew( [ 'namespace' => 'possoft.pos.edit-settings' ] );
    $pos->name = __( 'Edit Order Settings' );
    $pos->namespace = 'possoft.pos.edit-settings';
    $pos->description = __( 'Let the user edit the order settings.' );
    $pos->save();
}

if ( ! Permission::namespace( 'possoft.pos.products-discount' ) instanceof Permission ) {
    $pos = Permission::firstOrNew( [ 'namespace' => 'possoft.pos.products-discount' ] );
    $pos->name = __( 'Edit Product Discounts' );
    $pos->namespace = 'possoft.pos.products-discount';
    $pos->description = __( 'Let the user add discount on products.' );
    $pos->save();
}

if ( ! Permission::namespace( 'possoft.pos.cart-discount' ) instanceof Permission ) {
    $pos = Permission::firstOrNew( [ 'namespace' => 'possoft.pos.cart-discount' ] );
    $pos->name = __( 'Edit Cart Discounts' );
    $pos->namespace = 'possoft.pos.cart-discount';
    $pos->description = __( 'Let the user add discount on cart.' );
    $pos->save();
}

if ( ! Permission::namespace( 'possoft.pos.delete-order-product' ) instanceof Permission ) {
    $pos = Permission::firstOrNew( [ 'namespace' => 'possoft.pos.delete-order-product' ] );
    $pos->name = __( 'POS: Delete Order Products' );
    $pos->namespace = 'possoft.pos.delete-order-product';
    $pos->description = __( 'Let the user delete order products on POS.' );
    $pos->save();
}

// Create POS cart action permissions
$posCartPermissions = [
    'possoft.cart.product-discount' => __( 'Cart: Change Product Discount' ),
    'possoft.cart.product-price' => __( 'Cart: Edit Product Price' ),
    'possoft.cart.product-wholesale-price' => __( 'Cart: Use Wholesale Price' ),
    'possoft.cart.product-delete' => __( 'Cart: Product Delete' ),
    'possoft.cart.settings' => __( 'Cart: Change Settings' ),
    'possoft.cart.taxes' => __( 'Cart: Set Taxes' ),
    'possoft.cart.comments' => __( 'Cart: Add Comments' ),
    'possoft.cart.order-type' => __( 'Cart: Change Order Type' ),
    'possoft.cart.coupons' => __( 'Cart: Apply Coupons' ),
    'possoft.cart.products' => __( 'Cart: Create Quick Product' ),
    'possoft.cart.void' => __( 'Cart: Void Order' ),
    'possoft.cart.discount' => __( 'Cart: Apply Discount' ),
    'possoft.cart.hold' => __( 'Cart: Hold Order' ),
];

foreach ( $posCartPermissions as $namespace => $name ) {
    $permission = Permission::firstOrNew( [ 'namespace' => $namespace ] );
    $permission->name = $name;
    $permission->namespace = $namespace;
    $permission->description = sprintf( __( 'Allow access to %s feature in POS cart.' ), strtolower( $name ) );
    $permission->save();
}

/**
 * Assign new permissions to admin role
 */
$admin = Role::namespace( 'admin' );
if ( $admin instanceof Role ) {
    $admin->addPermissions( array_merge( array_keys( $posReadPermissions ), array_keys( $posCartPermissions ) ) );
}
