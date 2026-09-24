<?php
require __DIR__ . '/../includes/auth.php';
require_login();
$userId = effective_user_id();

$data = export_user_data($userId);
$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$filename = 'german-university-research-' . date('Y-m-d') . '.json';

header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($json));
header('Cache-Control: no-store, no-cache, must-revalidate');
echo $json;
exit;
