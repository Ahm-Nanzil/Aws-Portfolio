-- Upgrade script: adds Smart Merge import support to an EXISTING
-- installation (one that was set up before this feature existed).
--
-- Safe to run once against your current database:
--   mysql -u your_db_user -p your_db_name < upgrade-smart-merge-import.sql
--
-- This only ADDS two new tables. It does not modify, delete, or touch
-- any existing universities, programs, or user accounts in any way.
--
-- If you're setting this app up for the very first time, you do NOT
-- need this file — schema.sql already includes everything.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS import_sessions (
  token VARCHAR(64) NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  filename VARCHAR(255) NOT NULL DEFAULT '',
  mode ENUM('smart_merge','replace_all') NOT NULL DEFAULT 'smart_merge',
  payload LONGTEXT NOT NULL,
  plan LONGTEXT NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (token),
  KEY idx_import_sessions_user (user_id),
  CONSTRAINT fk_import_sessions_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS import_backups (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  reason VARCHAR(100) NOT NULL DEFAULT '',
  payload LONGTEXT NOT NULL,
  university_count INT UNSIGNED NOT NULL DEFAULT 0,
  program_count INT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_import_backups_user (user_id),
  CONSTRAINT fk_import_backups_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Upgrade complete. Smart Merge import is ready to use.' AS result;
