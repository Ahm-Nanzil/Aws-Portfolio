-- Upgrade script: adds email verification support to an EXISTING
-- installation (one that was set up before this feature existed).
--
-- Safe to run once against your current database:
--   mysql -u your_db_user -p your_db_name < upgrade-email-verification.sql
--
-- This does NOT delete or modify any existing universities, programs,
-- or user accounts — it only adds new columns/tables, and marks every
-- existing user as already verified (since they registered before
-- verification existed, they shouldn't be locked out retroactively).
--
-- If you're setting this app up for the very first time, you do NOT
-- need this file — schema.sql already includes everything.

SET NAMES utf8mb4;

-- Add the new columns to users, one at a time, only if missing.
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'email_verified_at'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE users ADD COLUMN email_verified_at DATETIME NULL DEFAULT NULL AFTER status',
  'SELECT "email_verified_at already exists, skipping"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'verification_token'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE users ADD COLUMN verification_token VARCHAR(64) NULL DEFAULT NULL AFTER email_verified_at',
  'SELECT "verification_token already exists, skipping"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'verification_token_expires'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE users ADD COLUMN verification_token_expires DATETIME NULL DEFAULT NULL AFTER verification_token',
  'SELECT "verification_token_expires already exists, skipping"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'verification_last_sent_at'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE users ADD COLUMN verification_last_sent_at DATETIME NULL DEFAULT NULL AFTER verification_token_expires',
  'SELECT "verification_last_sent_at already exists, skipping"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_exists := (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND INDEX_NAME = 'idx_users_verification_token'
);
SET @sql := IF(@idx_exists = 0,
  'ALTER TABLE users ADD INDEX idx_users_verification_token (verification_token)',
  'SELECT "index already exists, skipping"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Grandfather in every existing account as already verified, so nobody
-- who registered before this feature existed gets locked out.
UPDATE users SET email_verified_at = created_at WHERE email_verified_at IS NULL;

-- Create the settings table if it doesn't exist yet.
CREATE TABLE IF NOT EXISTS settings (
  name VARCHAR(100) NOT NULL,
  value VARCHAR(255) NOT NULL DEFAULT '',
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default to OFF, so behavior is unchanged until you turn it on in the
-- Admin Panel.
INSERT INTO settings (name, value, updated_at)
VALUES ('require_email_verification', '0', NOW())
ON DUPLICATE KEY UPDATE name = name;

SELECT 'Upgrade complete.' AS result;
