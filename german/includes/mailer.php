<?php
/**
 * Outgoing mail (SMTP), used for account-verification emails.
 *
 * Uses PHPMailer (vendored directly under includes/PHPMailer/ — no
 * Composer required) configured from the SMTP_* constants in
 * config.php.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**
 * Sends one email via the configured SMTP server.
 *
 * @return array{0: bool, 1: string} [success, error message if any]
 */
function send_mail(string $toEmail, string $toName, string $subject, string $htmlBody, string $altBody = ''): array {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->Port = SMTP_PORT;
        $mail->SMTPAuth = (SMTP_USERNAME !== "");
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        if (SMTP_ENCRYPTION === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } elseif (SMTP_ENCRYPTION === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = false;
            $mail->SMTPAutoTLS = false;
        }

        if (defined('SMTP_DEBUG') && SMTP_DEBUG) {
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = 'error_log';
        }

        $mail->CharSet = 'UTF-8';
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = $altBody !== '' ? $altBody : strip_tags($htmlBody);

        $mail->send();
        return [true, ''];
    } catch (PHPMailerException $e) {
        return [false, $mail->ErrorInfo ?: $e->getMessage()];
    } catch (\Throwable $e) {
        return [false, $e->getMessage()];
    }
}

function verification_email_html(string $name, string $verifyUrl): string {
    $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $safeUrl = htmlspecialchars($verifyUrl, ENT_QUOTES, 'UTF-8');
    return <<<HTML
    <div style="font-family: -apple-system, Segoe UI, Roboto, sans-serif; max-width: 480px; margin: 0 auto; color: #1B2430;">
      <p style="font-size: 20px; margin-bottom: 4px;">🇩🇪 German University Research Manager</p>
      <h2 style="font-weight: 600;">Confirm your email address</h2>
      <p>Hi {$safeName},</p>
      <p>Thanks for registering. Please confirm this is your email address to activate your account:</p>
      <p style="margin: 28px 0;">
        <a href="{$safeUrl}" style="background: #1F3D5C; color: #ffffff; padding: 12px 22px; border-radius: 6px; text-decoration: none; display: inline-block; font-weight: 600;">Verify my email</a>
      </p>
      <p style="color: #62697A; font-size: 13px;">Or copy and paste this link into your browser:<br>{$safeUrl}</p>
      <p style="color: #62697A; font-size: 13px;">This link expires in 24 hours. If you didn't create this account, you can safely ignore this email.</p>
    </div>
    HTML;
}

/**
 * @return array{0: bool, 1: string}
 */
function send_verification_email(array $user, string $token): array {
    $verifyUrl = app_base_url() . '/verify.php?token=' . urlencode($token);
    $html = verification_email_html($user['name'], $verifyUrl);
    return send_mail($user['email'], $user['name'], 'Confirm your email address', $html);
}

/**
 * @return array{0: bool, 1: string}
 */
function send_test_email(string $toEmail): array {
    $html = '<div style="font-family: sans-serif;"><p>This is a test email from your German University Research Manager installation.</p><p>If you received this, your SMTP settings in <code>config.php</code> are working correctly.</p></div>';
    return send_mail($toEmail, $toEmail, 'Test email — German University Research Manager', $html);
}

/**
 * Best-effort absolute base URL of this app (scheme + host + base path),
 * used to build a clickable link inside emails (relative links don't
 * work there).
 */
function app_base_url(): string {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . base_path();
}
