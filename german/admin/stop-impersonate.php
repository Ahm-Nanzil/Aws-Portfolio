<?php
require __DIR__ . '/../includes/auth.php';
require_admin();

stop_impersonation();
flash('success', 'Returned to your own admin account.');
redirect('index.php');
