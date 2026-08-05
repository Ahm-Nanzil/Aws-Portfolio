<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

require_once __DIR__ . '/config.php';

// Fetch SMTP settings from DB
$stmt = $pdo->query("SELECT `key`, `value` FROM settings WHERE `key` IN ('smtp_host','smtp_port','smtp_user','smtp_pass','smtp_from_name','smtp_from_email')");
$smtpSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Retrieve form data
$name    = htmlspecialchars(trim($_POST['your-name'] ?? ''));
$email   = htmlspecialchars(trim($_POST['your-email'] ?? ''));
$subject = htmlspecialchars(trim($_POST['your-subject'] ?? 'No Subject'));
$phone   = htmlspecialchars(trim($_POST['your-phone'] ?? ''));
$message = htmlspecialchars(trim($_POST['your-message'] ?? ''));

$test_smtp = $_POST['test_smtp'] ?? 0;

if($test_smtp) {

    $name = 'Test User';
    $email ='test@example.com';
    $subject = 'SMTP Test Email';
    $phone = '123456789';
    $message = 'This is a test email to check your SMTP settings.';
    
}


$mail = new PHPMailer(true);

try {
    // Server settings using DB values
    $mail->isSMTP();
    $mail->Host       = $smtpSettings['smtp_host'] ?? 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpSettings['smtp_user'] ?? 'ahmnanzil33@gmail.com';
    $mail->Password   = $smtpSettings['smtp_pass'] ?? 'pmjxqjwcjyaqkdra';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $smtpSettings['smtp_port'] ?? 587;

    // Sender & recipient
    $fromEmail = $smtpSettings['smtp_from_email'] ?? 'ahmnanzil33@gmail.com';
    $fromName  = $smtpSettings['smtp_from_name'] ?? 'Audiophone Website';

    $mail->setFrom($fromEmail, $fromName);
    $mail->addAddress($fromEmail, 'Admin'); // receiving email
    if (!empty($email)) {
        $mail->addReplyTo($email, $name);
    }

    // Email content
    $mail->isHTML(true);
    $mail->Subject = "📩 Contact Form Submission: $subject";

    $mail->Body = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
    <meta charset='UTF-8'>
    <title>New Contact Form Submission</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin:0; padding:0; }
        .email-container { max-width:600px; margin:20px auto; background-color:#fff; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1); padding:20px; }
        h2 { color:#333; text-align:center; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { text-align:left; padding:12px; border-bottom:1px solid #ddd; }
        th { background-color:#2c3e50; color:#fff; }
        p.note { font-size:12px; color:#777; text-align:center; margin-top:20px; }
    </style>
    </head>
    <body>
    <div class='email-container'>
        <h2>New Contact Form Message</h2>
        <table>
            <tr><th>Name</th><td>$name</td></tr>
            <tr><th>Email</th><td>$email</td></tr>
            <tr><th>Phone</th><td>$phone</td></tr>
            <tr><th>Subject</th><td>$subject</td></tr>
            <tr><th>Message</th><td>".nl2br($message)."</td></tr>
        </table>
        <p class='note'>📬 Sent from your website contact form.</p>
    </div>
    </body>
    </html>
    ";

    $mail->send();
    echo "success";

} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}
?>
