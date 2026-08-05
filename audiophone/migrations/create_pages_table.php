<?php
require __DIR__ . '/../config.php';

$sql = "CREATE TABLE IF NOT EXISTS pages (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_name VARCHAR(100) NOT NULL,
    section_name VARCHAR(100) NOT NULL,
    content JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$pdo->exec($sql);

echo "Pages table created successfully.";
?>