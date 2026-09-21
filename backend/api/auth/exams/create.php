<?php

header("Content-Type: application/json");

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireRole("admin");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only POST requests are allowed."
    ]);

    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$title = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');
$duration = (int) ($data['duration'] ?? 0);

if ($title === '' || $duration <= 0) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Title and a valid duration are required."
    ]);

    exit;
}

try {
    $db = getDatabase();

    $exams = $db->selectCollection("exams");

    $result = $exams->insertOne([
        "title" => $title,
        "description" => $description,
        "duration" => $duration,
        "published" => true,
        "created_by" => $_SESSION['user_id'],
        "created_at" => new MongoDB\BSON\UTCDateTime()
    ]);

    http_response_code(201);

    echo json_encode([
        "success" => true,
        "message" => "Exam created successfully.",
        "exam_id" => (string) $result->getInsertedId()
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to create exam.",
        "error" => $e->getMessage()
    ]);
}