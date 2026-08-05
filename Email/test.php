<?php

// Load PHPMailer
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {

    // // SMTP Configuration (Office 365)
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ahmnanzil33@gmail.com';          // Authentication Email
    $mail->Password   = 'odqj over qjse phld';                // Use App Password if MFA is enabled
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;    // IMPORTANT for port 587
    $mail->Port       = 587;

    // SMTP Configuration (Hostinger Titan)
// $mail->isSMTP();
// $mail->Host       = 'smtp.titan.email';
// $mail->SMTPAuth   = true;
// $mail->Username   = 'support@khudebarta.com';   // Your Titan email
// $mail->Password   = 'SoftKad@#8179';            // Your Titan password

$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL for port 465
$mail->Port       = 465;

    // Optional but recommended for localhost
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ],
    ];

    
$mail->SMTPDebug = 2;
$mail->Debugoutput = 'html';

    // Sender
$mail->setFrom('support@khudebarta.com', 'Khudebarta');
    // Recipient (test email)
    $mail->addAddress('ahmnanzil33@gmail.com'); // change if needed

    // Email Content
    $mail->isHTML(true);
    $mail->Subject = 'Office 365 SMTP Test';
    $mail->Body    = '<h3>SMTP Test Successful</h3><p>This email was sent using Office 365 SMTP.</p>';
    $mail->AltBody = 'SMTP Test Successful - Office 365';

    // Send Email
    $mail->send();
    echo "✅ Email sent successfully!";

} catch (Exception $e) {
    echo "❌ Email sending failed. Error: {$mail->ErrorInfo}";
}
