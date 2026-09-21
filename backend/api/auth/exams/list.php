<?php

header("Content-Type: application/json");

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only GET requests are allowed."
    ]);

    exit;
}

try {

    $db = getDatabase();

    $exams = $db->selectCollection("exams");

    $filter = $_SESSION['role'] === 'admin' ? [] : ["published" => true];
    $cursor = $exams->find($filter,
        [
            "sort" => [
                "created_at" => -1
            ]
        ]
    );

    $examList = [];

    foreach ($cursor as $exam) {
        $examList[] = [
            "id" => (string) $exam['_id'],
            "title" => $exam['title'],
            "description" => $exam['description'] ?? '',
            "duration" => $exam['duration'],
            "created_at" => $exam['created_at'] ?? null,
            "published" => $exam['published'] ?? false
        ];
    }

    echo json_encode([
        "success" => true,
        "exams" => $examList
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to load exams.",
        "error" => $e->getMessage()
    ]);
}