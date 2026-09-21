<?php

header("Content-Type: application/json");

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireRole("student");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only GET requests are allowed."
    ]);

    exit;
}

$examId = trim($_GET['exam_id'] ?? '');

if ($examId === '') {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Exam ID is required."
    ]);

    exit;
}

if (!MongoDB\BSON\ObjectId::isValid($examId)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid exam ID."
    ]);

    exit;
}

try {

    $db = getDatabase();

    $exams = $db->selectCollection("exams");

    $exam = $exams->findOne([
        "_id" => new MongoDB\BSON\ObjectId($examId),
        "published" => true
    ]);

    if ($exam === null) {
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Published exam not found."
        ]);

        exit;
    }

    $questions = $db->selectCollection("questions");

    $cursor = $questions->find([
        "exam_id" => new MongoDB\BSON\ObjectId($examId)
    ]);

    $questionList = [];

    foreach ($cursor as $question) {

        $questionList[] = [
            "id" => (string) $question['_id'],
            "question" => $question['question'],
            "options" => $question['options']
        ];
    }

    echo json_encode([
        "success" => true,
        "exam" => [
            "id" => (string) $exam['_id'],
            "title" => $exam['title'],
            "description" => $exam['description'] ?? '',
            "duration" => $exam['duration'],
            "questions" => $questionList
        ]
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to start exam.",
        "error" => $e->getMessage()
    ]);
}