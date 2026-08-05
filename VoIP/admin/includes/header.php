<?php
$base_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
require __DIR__ . '/../../config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); // redirect to login if not authenticated
    exit;
}
$settings = [];
try {
    $stmt = $pdo->query("SELECT `key`, `value`, created_at, updated_at FROM settings ORDER BY `key`");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $settings[$row['key']] = [
            'value' => $row['value'],
            '_meta' => [
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
            ],
        ];
    }
} catch (\PDOException $e) {
    error_log("Error fetching settings table: " . $e->getMessage());
    $settings = [];
}

/**
 * Helper: get setting value by key
 * Usage:
 *   setting('address')          => returns the value of 'address' or null if not exists
 *   setting('facebook', true)   => returns array with value and meta if second param true
 */
function setting(string $key, bool $withMeta = false) {
    global $settings;
    if (!isset($settings[$key])) return null;

    return $withMeta ? $settings[$key] : $settings[$key]['value'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="<?= setting('favicon') 
        ? setting('favicon') 
        : 'assets/img/cropped-asiantelecom-favicon.png'; ?>">
  <title>
    <?= setting('tab_title') 
        ?setting('tab_title') 
        : 'Ahm Nanzil'; ?>
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet" />
  <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
  <!-- Nucleo Icons -->
  <link href="assets/css/nucleo-icons.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link href="assets/css/black-dashboard.css?v=1.0.0" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="assets/demo/demo.css" rel="stylesheet" />
  <script src="https://cdn.tiny.cloud/1/vnhuic5iqy4orc3rog0gx9egr8u3p6rfdnumt40i0dfwxdtx/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

</head>
<style>
    .modal .form-control {
    color: #000 !important;
    background-color: #fff !important;
}
</style>
<body class="">
  <div class="wrapper">