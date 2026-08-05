<?php
require_once __DIR__ . '/../config.php';

// Default admin credentials
$username = 'admin';
$email = 'admin@example.com';
$password = password_hash('admin', PASSWORD_DEFAULT); // hashed password
$first_name = 'Admin';
$last_name = 'User';
$company = 'Your Company';
$address = '';
$city = '';
$country = '';
$postal_code = '';
$about_me = 'Default admin user';
$avatar = '';

try {
    $stmt = $pdo->prepare("
        INSERT INTO users (
            username, email, password, first_name, last_name,
            company, address, city, country, postal_code,
            about_me, avatar
        ) VALUES (
            :username, :email, :password, :first_name, :last_name,
            :company, :address, :city, :country, :postal_code,
            :about_me, :avatar
        )
    ");
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => $password,
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':company' => $company,
        ':address' => $address,
        ':city' => $city,
        ':country' => $country,
        ':postal_code' => $postal_code,
        ':about_me' => $about_me,
        ':avatar' => $avatar,
    ]);

    echo "✅ Default admin user created successfully.\n";

} catch (PDOException $e) {
    echo "❌ Admin seeding failed: " . $e->getMessage() . "\n";
}
