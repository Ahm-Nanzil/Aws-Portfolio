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
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_users_email (email)
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
