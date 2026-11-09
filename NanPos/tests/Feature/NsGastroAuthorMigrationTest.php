<?php

namespace Tests\Feature;

use Illuminate\Database\Migrations\Migration;
use ReflectionClass;
use Tests\TestCase;

class NsGastroAuthorMigrationTest extends TestCase
{
    public function test_author_column_rename_migration_targets_expected_tables(): void
    {
        $migration = require base_path( 'modules/NsGastro/Migrations/2026_06_30_000000_rename_author_to_author_id_on_nsgastro_tables.php' );
        $reflection = new ReflectionClass( $migration );
        $tables = $reflection->getMethod( 'tables' )->invoke( $migration );

        $this->assertInstanceOf( Migration::class, $migration );
        $this->assertTrue( $reflection->hasMethod( 'up' ) );
        $this->assertTrue( $reflection->hasMethod( 'down' ) );
        $this->assertSame( [
            'possoft_gastro_areas',
            'possoft_gastro_kitchens',
            'possoft_gastro_modifiers_groups',
            'possoft_gastro_tables',
            'possoft_gastro_tables_history',
            'possoft_gastro_tables_booking_history',
        ], $tables );
    }
}
