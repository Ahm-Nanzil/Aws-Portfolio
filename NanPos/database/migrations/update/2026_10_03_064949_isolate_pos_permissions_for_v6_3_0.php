<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const PERMISSIONS = [
        'possoft.pos.access' => 'POS: Access',
        'possoft.pos.read-products' => 'POS: Read Products',
        'possoft.pos.read-own-orders' => 'POS: Read Own Orders',
        'possoft.pos.read-all-orders' => 'POS: Read All Orders',
    ];

    public function up(): void
    {
        foreach ( self::PERMISSIONS as $namespace => $name ) {
            $permission = Permission::namespace( $namespace );

            if ( Permission::where( 'name', $name )->where( 'namespace', '!=', $namespace )->exists() ) {
                throw new RuntimeException( "The permission name [{$name}] is already assigned to another namespace." );
            }

            if ( ! $permission instanceof Permission ) {
                Permission::create( [
                    'namespace' => $namespace,
                    'name' => $name,
                    'description' => "Introduced by the PosSOFT 6.3.0 POS isolation migration: {$namespace}",
                ] );
            }
        }

        foreach ( [ Role::ADMIN, Role::STOREADMIN ] as $roleNamespace ) {
            Role::namespace( $roleNamespace )?->addPermissions( array_keys( self::PERMISSIONS ) );
        }

        $cashier = Role::namespace( Role::STORECASHIER );
        if ( $cashier instanceof Role ) {
            $cashier->addPermissions( [
                'possoft.pos.access',
                'possoft.pos.read-products',
                'possoft.pos.read-own-orders',
            ] );

            foreach ( [ 'possoft.read.orders', 'possoft.read.products', 'possoft.read.categories' ] as $permission ) {
                if ( Permission::namespace( $permission ) instanceof Permission ) {
                    $cashier->removePermissions( $permission );
                }
            }
        }
    }

    public function down(): void
    {
        $cashier = Role::namespace( Role::STORECASHIER );
        if ( $cashier instanceof Role ) {
            foreach ( [ 'possoft.read.orders', 'possoft.read.products', 'possoft.read.categories' ] as $permission ) {
                if ( Permission::namespace( $permission ) instanceof Permission ) {
                    $cashier->addPermissions( $permission );
                }
            }
        }

        Permission::whereIn( 'namespace', array_keys( self::PERMISSIONS ) )
            ->where( 'description', 'like', 'Introduced by the PosSOFT 6.3.0 POS isolation migration:%' )
            ->get()
            ->each( function ( Permission $permission ): void {
                $permission->removeFromRoles();
                $permission->delete();
            } );
    }
};
