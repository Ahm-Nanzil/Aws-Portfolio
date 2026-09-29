<?php
/**
 * Database connection settings.
 *
 * Edit these four values to match your MySQL/MariaDB server, then run
 * schema.sql once against an empty database to create the tables:
 *
 *   mysql -u root -p -e "CREATE DATABASE german_uni_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
 *   mysql -u root -p german_uni_manager < schema.sql
 *
 * On most shared hosting (cPanel etc.) you'll create the database and a
 * database user through the hosting control panel, then paste those
 * credentials in here.
 */

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'uni_manager');
define('DB_USER', 'root');
define('DB_PASS', '');

// Set to true while diagnosing a connection problem to see the raw PDO
// error on screen. Turn this back off (false) for normal / production use.
define('DB_DEBUG', false);

/**
 * Outgoing mail (SMTP) settings — used to send account-verification
 * emails when that feature is turned on in the Admin Panel.
 *
 * Any standard SMTP provider works: your own mail server, a hosting
 * provider's SMTP relay, or a transactional service like Gmail SMTP,
 * SendGrid, Mailgun, Postmark, Brevo, etc. Fill these in with the
 * credentials your provider gives you.
 *
 * You can leave these as placeholders if you don't plan to use email
 * verification — the feature defaults to OFF, so the app works fine
 * without a working mail server until an admin turns it on.
 */
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_ENCRYPTION', 'tls');           // 'tls', 'ssl', or '' for none
define('SMTP_USERNAME', 'your-smtp-username');
define('SMTP_PASSWORD', 'your-smtp-password');
define('SMTP_FROM_EMAIL', 'no-reply@example.com');
define('SMTP_FROM_NAME', 'German University Research Manager');

// Set to true to see detailed SMTP conversation output in the server's
// PHP error log when a send fails — helpful while setting this up for
// the first time. Turn back off for normal use.
define('SMTP_DEBUG', false);
