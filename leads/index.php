<?php
/**
 * index.php
 * Application entry point. Opens directly into the dashboard
 * (no login/auth — to be added later).
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

redirect('dashboard.php');
