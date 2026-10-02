-- German University Research Manager — Multi-user MySQL schema
-- Run this once against an empty database, e.g.:
--   mysql -u root -p german_uni_manager < schema.sql

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- users
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','student') NOT NULL DEFAULT 'student',
  status ENUM('active','disabled') NOT NULL DEFAULT 'active',
  email_verified_at DATETIME NULL DEFAULT NULL,
  verification_token VARCHAR(64) NULL DEFAULT NULL,
  verification_token_expires DATETIME NULL DEFAULT NULL,
  verification_last_sent_at DATETIME NULL DEFAULT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_users_email (email),
  KEY idx_users_verification_token (verification_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- universities  (one row per university a user is researching)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS universities (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  official_name VARCHAR(255) NOT NULL DEFAULT '',
  city VARCHAR(120) NOT NULL DEFAULT '',
  state VARCHAR(120) NOT NULL DEFAULT '',
  type VARCHAR(60) NOT NULL DEFAULT 'Public',
  website VARCHAR(500) NOT NULL DEFAULT '',
  intl_website VARCHAR(500) NOT NULL DEFAULT '',
  application_portal VARCHAR(500) NOT NULL DEFAULT '',
  application_method VARCHAR(60) NOT NULL DEFAULT 'Direct',
  application_fee VARCHAR(100) NOT NULL DEFAULT '',
  tuition_fee VARCHAR(100) NOT NULL DEFAULT '',
  semester_contribution VARCHAR(100) NOT NULL DEFAULT '',
  general_notes TEXT,
  status VARCHAR(60) NOT NULL DEFAULT 'Not Started',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_universities_user (user_id),
  CONSTRAINT fk_universities_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- programs  (one row per Master's program under a university)
-- Nested research sections are stored as JSON columns so the PHP layer
-- can keep working with them as plain associative arrays, exactly like
-- the original single-user JSON version.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS programs (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  university_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  degree VARCHAR(60) NOT NULL DEFAULT 'M.Sc.',
  subject VARCHAR(150) NOT NULL DEFAULT '',
  department VARCHAR(150) NOT NULL DEFAULT '',
  faculty VARCHAR(150) NOT NULL DEFAULT '',
  website VARCHAR(500) NOT NULL DEFAULT '',
  description TEXT,
  study_location VARCHAR(150) NOT NULL DEFAULT '',
  duration VARCHAR(60) NOT NULL DEFAULT '',
  ects VARCHAR(30) NOT NULL DEFAULT '',
  study_mode VARCHAR(30) NOT NULL DEFAULT 'Full-time',
  intake VARCHAR(30) NOT NULL DEFAULT 'Winter',
  language_json JSON NOT NULL,
  fees_json JSON NOT NULL,
  application_json JSON NOT NULL,
  admission_json JSON NOT NULL,
  documents_json JSON NOT NULL,
  links_json JSON NOT NULL,
  personal_json JSON NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_programs_user (user_id),
  KEY idx_programs_university (university_id),
  CONSTRAINT fk_programs_university FOREIGN KEY (university_id) REFERENCES universities (id) ON DELETE CASCADE,
  CONSTRAINT fk_programs_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- settings  (simple key/value store for admin-toggleable options,
-- e.g. whether email verification is required at registration)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS settings (
  name VARCHAR(100) NOT NULL,
  value VARCHAR(255) NOT NULL DEFAULT '',
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email verification is OFF by default so a fresh install behaves
-- exactly like before until an admin turns it on from the Admin Panel.
INSERT INTO settings (name, value, updated_at)
VALUES ('require_email_verification', '0', NOW())
ON DUPLICATE KEY UPDATE name = name;

-- ---------------------------------------------------------------------
-- import_sessions  (holds an uploaded import file + its computed merge
-- plan between the "preview" and "confirm" steps of the Smart Merge
-- import wizard. Short-lived — rows are deleted once committed or
-- cancelled, and any left over after 2 hours are treated as expired.)
-- ---------------------------------------------------------------------
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

-- ---------------------------------------------------------------------
-- import_backups  (automatic snapshots taken right before a destructive
-- operation — currently "Replace All" — so it can be undone. Also used
-- to hold the "before restore" snapshot when restoring an older backup.
-- Only the most recent few per user are kept; see delete_old_backups().)
-- ---------------------------------------------------------------------
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
