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

$examId = trim($data['exam_id'] ?? '');
$question = trim($data['question'] ?? '');
$options = $data['options'] ?? [];
$correctAnswer = trim($data['correct_answer'] ?? '');

if (
    $examId === '' ||
    $question === '' ||
    !is_array($options) ||
    count($options) < 2 ||
    $correctAnswer === ''
) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Exam ID, question, options and correct answer are required."
    ]);

    exit;
}

if (!in_array($correctAnswer, $options, true)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Correct answer must be one of the options."
    ]);

    exit;
}

try {
    $db = getDatabase();

    $exams = $db->selectCollection("exams");

    $exam = $exams->findOne([
        "_id" => new MongoDB\BSON\ObjectId($examId)
    ]);

    if ($exam === null) {
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Exam not found."
        ]);

        exit;
    }

    $questions = $db->selectCollection("questions");

    $result = $questions->insertOne([
        "exam_id" => new MongoDB\BSON\ObjectId($examId),
        "question" => $question,
        "options" => array_values($options),
        "correct_answer" => $correctAnswer,
        "created_at" => new MongoDB\BSON\UTCDateTime()
    ]);

    http_response_code(201);

    echo json_encode([
        "success" => true,
        "message" => "Question created successfully.",
        "question_id" => (string) $result->getInsertedId()
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to create question.",
        "error" => $e->getMessage()
    ]);
}