<?php
/**
 * Database connection settings.
 *
 * Edit these four values to match your MySQL/MariaDB server, then run
 * schema.sql once against an empty database to create the tables:
 *
 *   mysql -u root -p -e "CREATE DATABASE german_uni_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
 *   mysql -u root -p german_uni_manager < schema.sql
 *
 * On most shared hosting (cPanel etc.) you'll create the database and a
 * database user through the hosting control panel, then paste those
 * credentials in here.
 */

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'uni_manager');
define('DB_USER', 'root');
define('DB_PASS', '');

// Set to true while diagnosing a connection problem to see the raw PDO
// error on screen. Turn this back off (false) for normal / production use.
define('DB_DEBUG', false);
