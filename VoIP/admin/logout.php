<?php
session_start();
session_unset();
session_destroy();

$base_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
$base_url = str_replace('/admin', '', $base_url);

header("Location: $base_url/login.php");
exit;
?>
