<?php
require_once __DIR__ . '/../../bootstrap.php';
requireAuth('admin');
$examId = (string) ($_GET['exam_id'] ?? '');
if (!MongoDB\BSON\ObjectId::isValid($examId)) jsonResponse(['error' => 'Valid exam_id is required'], 422);
$items = [];
foreach (collection('questions')->find(['exam_id' => objectId($examId)], ['sort' => ['created_at' => 1]]) as $question) $items[] = serializeDocument($question);
jsonResponse(['questions' => $items]);
