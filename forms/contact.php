<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


$contact = new PHPMailer(true);

$contact->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_CONNECTION;
$contact->Debugoutput = function ($str, $level) {
};

$contact->isSMTP();
$contact->Host = 'smtp.gmail.com';  
$contact->SMTPAuth = true;
$contact->Username = 'ahmnanzil33@gmail.com';  
$contact->Password = 'hpitjdlzhhmnhurc';  
$contact->SMTPSecure = 'tls';
$contact->Port = 587;

$sender = $_POST['email'];
$senderName = $_POST['name'];
$subject = $_POST['subject'];
$message = $_POST['message'];

// Format the date for email
$date = date("F j, Y, g:i a");

$contact->setFrom($sender, $senderName);
$contact->addAddress('ahmnanzilofficial@gmail.com', 'Ahm Nanzil');  
$contact->Subject = $subject;

// Set email to HTML format
$contact->isHTML(true);

// Create HTML email template
$emailTemplate = '
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f9f9f9; color: #333333;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 0 auto;">
        <!-- Header -->
        <tr>
            <td align="center" bgcolor="#2e58ff" style="padding: 20px 24px; border-radius: 8px 8px 0 0;">
                <h1 style="margin: 0; color: white; font-size: 24px; font-weight: 700;">New Inquiry</h1>
            </td>
        </tr>
        
        <!-- Content -->
        <tr>
            <td bgcolor="#ffffff" style="padding: 24px; border-left: 1px solid #dddddd; border-right: 1px solid #dddddd;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    
                    
                    <!-- Sender Details -->
                    <tr>
                        <td style="padding: 0 0 20px 0;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f5f8ff; border-radius: 4px; padding: 12px;">
                                <tr>
                                    <td width="100" style="font-weight: bold; padding: 8px;">Name:</td>
                                    <td style="padding: 8px;">'.htmlspecialchars($senderName).'</td>
                                </tr>
                                <tr>
                                    <td width="100" style="font-weight: bold; padding: 8px;">Email:</td>
                                    <td style="padding: 8px;">'.htmlspecialchars($sender).'</td>
                                </tr>
                                <tr>
                                    <td width="100" style="font-weight: bold; padding: 8px;">Subject:</td>
                                    <td style="padding: 8px;">'.htmlspecialchars($subject).'</td>
                                </tr>
                                <tr>
                                    <td width="100" style="font-weight: bold; padding: 8px;">Date:</td>
                                    <td style="padding: 8px;">'.$date.'</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Message -->
                    <tr>
                        <td style="padding: 0 0 20px 0;">
                            <h2 style="font-size: 18px; margin: 0 0 12px 0; color: #2e58ff;">Message:</h2>
                            <div style="background-color: #f8f8f8; border-left: 4px solid #2e58ff; padding: 12px; border-radius: 4px; font-size: 16px; line-height: 1.6;">
                                '.nl2br(htmlspecialchars($message)).'
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td bgcolor="#f0f2f5" style="padding: 16px 24px; text-align: center; border-radius: 0 0 8px 8px; border: 1px solid #dddddd; border-top: none;">
                <p style="margin: 0; font-size: 14px; color: #666666;">This is an automated email from your website contact form.</p>
            </td>
        </tr>
    </table>
</body>
</html>
';

// Set the HTML content
$contact->Body = $emailTemplate;

// Set plain-text version for non-HTML mail clients
$plainText = "NEW CONTACT FORM SUBMISSION\n\n";
$plainText .= "FROM: $senderName ($sender)\n";
$plainText .= "SUBJECT: $subject\n";
$plainText .= "DATE: $date\n\n";
$plainText .= "MESSAGE:\n$message\n";

$contact->AltBody = $plainText;

$contact->SMTPDebug = SMTP::DEBUG_CONNECTION;
$contact->Debugoutput = function ($str, $level) {
    // Silent debug output
};

try {
    $contact->send();
    echo 'OK'; 
} catch (Exception $e) {
    http_response_code(500);
    echo "Mailer Error: " . $contact->ErrorInfo;
}

?>