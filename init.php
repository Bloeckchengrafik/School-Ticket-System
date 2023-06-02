<?php
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/src/');
require_once 'modules/mysql.php';

// ask the user if they really want to reset the database
$confirm = readline('Are you sure you want to reset the database? [y/N] ');
if (strtolower($confirm) !== 'y') {
    echo 'Aborting' . PHP_EOL;
    exit(1);
}

$conn = Database\connection();
$init = file_get_contents(__DIR__ . '/sql/init.sql');
$conn->exec($init);
echo 'Database initialized successfully; creating initial admin user' . PHP_EOL;

try {
    $key = bin2hex(random_bytes(16));
} catch (Exception $e) {
    echo 'Failed to generate key: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

// ask for first name, last name, email
$first_name = readline('First name: ');
$last_name = readline('Last name: ');
$email = readline('Email: ');
$webroot = readline('Webroot: ');

$stmt = $conn->prepare("INSERT INTO User (first_name, last_name, email, account_class) VALUE (:first_name, :last_name, :email, 'admin')");
$stmt->execute([
    ':first_name' => $first_name,
    ':last_name' => $last_name,
    ':email' => $email,
]);

$user_id = $conn->lastInsertId();

$stmt = $conn->prepare("INSERT INTO UserMagicLinkKey (user_id, code) VALUE (:user_id, :code)");
$stmt->execute([
    ':user_id' => $user_id,
    ':code' => $key,
]);

echo 'User created successfully' . PHP_EOL;
echo 'User ID: ' . $user_id . PHP_EOL;
echo 'Login url: ' . $webroot . '/login.php?key=' . $key . PHP_EOL;