<?php
require __DIR__ . '/../config.php';

/**
 * Table 1: rates (Country-level)
 */
$pdo->exec("
CREATE TABLE IF NOT EXISTS rates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  country_name VARCHAR(100) NOT NULL,  
  country_code VARCHAR(5) NULL,             
  last_update DATE DEFAULT NULL,                     
  expiry_info VARCHAR(255) DEFAULT 'No expiry date', 
  connection_fee_note VARCHAR(255) DEFAULT NULL,     
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
");

/**
 * Table 2: rate_details (Carrier-level)
 */
$pdo->exec("
CREATE TABLE IF NOT EXISTS rate_details (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rate_id INT NOT NULL,                              -- Linked with rates.id
  carrier_name VARCHAR(100) NOT NULL,                -- e.g. 'Somalia Mobile'
  sms_rate VARCHAR(20) DEFAULT NULL,               -- e.g. 0.074
  sms_unit VARCHAR(20) DEFAULT 'sms',                -- e.g. '/sms'
  call_rate VARCHAR(20) DEFAULT NULL,              -- e.g. 0.3
  call_unit VARCHAR(20) DEFAULT 'min',               -- e.g. '/min'
  bundle_info VARCHAR(100) DEFAULT NULL,             -- e.g. '3 min/ $1'
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (rate_id) REFERENCES rates(id) ON DELETE CASCADE
);
");
?>
