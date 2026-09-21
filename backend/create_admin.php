<?php

require_once __DIR__ . '/config/database.php';

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;

$db = getDatabase();

$users = $db->selectCollection("users");

$email = "admin@example.com";
$password = "Admin123!";

$existingAdmin = $users->findOne([
    "email" => $email
]);

if ($existingAdmin !== null) {
    if (($existingAdmin['role'] ?? '') !== 'admin') {
        $users->updateOne(
            ['_id' => $existingAdmin['_id']],
            ['$set' => [
                'name' => 'System Administrator',
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'admin'
            ]]
        );
        echo "Existing account converted to administrator.\n";
    } else {
        echo "Admin already exists.\n";
    }
    exit;
}

$users->insertOne([
    "name" => "System Administrator",
    "email" => $email,
    "password" => password_hash($password, PASSWORD_DEFAULT),
    "role" => "admin",
    "created_at" => new UTCDateTime()
]);

echo "Admin account created successfully.";