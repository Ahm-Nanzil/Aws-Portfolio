<?php
/**
 * config.php
 * Global configuration, paths and constants for the Lead Management System.
 */

declare(strict_types=1);

// Show errors during development. Set to '0' in production.
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Start session (used for flash messages)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ----------------------------------------------------------------------
// Base paths
// ----------------------------------------------------------------------
define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');
define('UPLOADS_PATH', BASE_PATH . '/uploads');

define('LEADS_FILE', STORAGE_PATH . '/leads.json');
define('SETTINGS_FILE', STORAGE_PATH . '/settings.json');
define('BACKUPS_PATH', STORAGE_PATH . '/backups');

// Base URL (relative) - works regardless of subfolder install
define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/'));

// ----------------------------------------------------------------------
// App constants
// ----------------------------------------------------------------------
define('APP_NAME', 'LeadCRM');

// Allowed lead statuses (order matters for display)
define('LEAD_STATUSES', [
    'New'       => 'secondary',
    'Contacted' => 'info',
    'Interested'=> 'primary',
    'Won'       => 'success',
    'Lost'      => 'danger',
]);

// Allowed priorities
define('LEAD_PRIORITIES', [
    'Low'    => 'secondary',
    'Medium' => 'warning',
    'High'   => 'danger',
]);

// Make sure storage directories exist (safety net for fresh uploads)
if (!is_dir(STORAGE_PATH)) {
    @mkdir(STORAGE_PATH, 0775, true);
}
if (!is_dir(UPLOADS_PATH)) {
    @mkdir(UPLOADS_PATH, 0775, true);
}
if (!is_dir(BACKUPS_PATH)) {
    @mkdir(BACKUPS_PATH, 0775, true);
}
