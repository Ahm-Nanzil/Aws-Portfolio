<?php
require __DIR__ . '/includes/auth.php';

if (db_count_users() === 0) {
    redirect(base_path() . '/setup.php');
}
if (!is_logged_in()) {
    redirect(base_path() . '/login.php');
}
redirect(base_path() . '/dashboard.php');
