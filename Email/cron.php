<?php

// Random delay between 0 and 3600 seconds (0 to 60 minutes)
// $sleepSeconds = rand(0, 300);
// sleep($sleepSeconds);

$baseDir = __DIR__;

// Load dependencies
require $baseDir . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load configurations with absolute path
$emailConfig = require $baseDir . '/emailconfiguration.php';
$trackingFile = $baseDir . '/tracking.json';
$csvFile = $baseDir . '/clients.csv';
$emailTemplate = file_get_contents($baseDir . '/emailbody.html');

// Initialize tracking
if (!file_exists($trackingFile)) {
    file_put_contents($trackingFile, json_encode(['last_sent_index' => 0, 'total_sent' => 0]));
}
$tracking = json_decode(file_get_contents($trackingFile), true);

// Read CSV
$clients = array_map('str_getcsv', file($csvFile));
$headers = array_shift($clients);

// Get next client
$nextIndex = $tracking['last_sent_index'];
if (!isset($clients[$nextIndex])) {
    die("No more emails to send or CSV is empty.\n");
}

$clientData = array_combine($headers, $clients[$nextIndex]);
$email = $clientData['Email'];

// Send email
$mail = new PHPMailer(true);
try {
    // SMTP Config
    $mail->isSMTP();
    $mail->Host = $emailConfig['smtp_host'];
    $mail->Port = $emailConfig['smtp_port'];
    $mail->SMTPAuth = true;
    $mail->Username = $emailConfig['smtp_username'];
    $mail->Password = $emailConfig['smtp_password'];
    $mail->SMTPSecure = $emailConfig['smtp_secure'];

    // Email content
    $mail->setFrom($emailConfig['from_email'], $emailConfig['from_name']);
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = "Custom Websites & Business Solutions";
    $mail->Body = "test email";
    $mail->send();

    echo "Email sent to: $email\n";

    // Update tracking
    $tracking['last_sent_index'] = $nextIndex + 1;
    $tracking['total_sent']++;
    file_put_contents($trackingFile, json_encode($tracking));

} catch (Exception $e) {
    error_log("Failed to send to $email. Error: {$mail->ErrorInfo}");
}