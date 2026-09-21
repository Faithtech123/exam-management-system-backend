<?php

session_start();
header("Content-Type: application/json");

require_once __DIR__ . '/../../config/database.php';

use MongoDB\BSON\UTCDateTime;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only POST requests are allowed."
    ]);

    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data['name'] ?? '');
$email = strtolower(trim($data['email'] ?? ''));
$password = $data['password'] ?? '';

if ($name === '' || $email === '' || $password === '') {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Name, email and password are required."
    ]);

    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Please provide a valid email address."
    ]);

    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Password must be at least 6 characters."
    ]);

    exit;
}

try {
    $db = getDatabase();

    $users = $db->selectCollection("users");

    // Make email unique
    $users->createIndex(
        ["email" => 1],
        ["unique" => true]
    );

    // Check if email already exists
    $existingUser = $users->findOne([
        "email" => $email
    ]);

    if ($existingUser !== null) {
        http_response_code(409);

        echo json_encode([
            "success" => false,
            "message" => "An account with this email already exists."
        ]);

        exit;
    }

    // Hash password securely
    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $result = $users->insertOne([
        "name" => $name,
        "email" => $email,
        "password" => $hashedPassword,
        "role" => "student",
        "created_at" => new UTCDateTime()
    ]);

    $_SESSION['user_id'] = (string) $result->getInsertedId();
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['role'] = 'student';

    http_response_code(201);

    echo json_encode([
        "success" => true,
        "message" => "Student account created successfully.",
        "user_id" => (string) $result->getInsertedId(),
        "user" => [
            "id" => (string) $result->getInsertedId(),
            "name" => $name,
            "email" => $email,
            "role" => "student"
        ]
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Registration failed.",
        "error" => $e->getMessage()
    ]);
}