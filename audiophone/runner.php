<?php
$migrationFiles = glob(__DIR__ . '/migrations/*.php');

foreach ($migrationFiles as $file) {
    echo "Running migration: " . basename($file) . "\n";
    require $file;
}
echo "All migrations completed.\n";

$seederFiles = glob(__DIR__ . '/seeders/*.php');

foreach ($seederFiles as $file) {
    echo "Running seeder: " . basename($file) . "\n";
    require $file;
}
