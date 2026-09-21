<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../middleware/auth.php';
requireRole('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false, 'message' => 'Only PUT or POST requests are allowed.']); exit; }
$id = $_GET['id'] ?? $_GET['exam_id'] ?? '';
$data = json_decode(file_get_contents('php://input'), true) ?: [];
if (!MongoDB\BSON\ObjectId::isValid($id)) { http_response_code(422); echo json_encode(['success' => false, 'message' => 'Valid exam id is required.']); exit; }
$updates = [];
foreach (['title', 'description', 'duration', 'published'] as $field) if (array_key_exists($field, $data)) $updates[$field] = $field === 'duration' ? (int) $data[$field] : $data[$field];
if (!$updates) { http_response_code(422); echo json_encode(['success' => false, 'message' => 'No changes supplied.']); exit; }
try {
	$result = getDatabase()->selectCollection('exams')->updateOne(['_id' => new MongoDB\BSON\ObjectId($id)], ['$set' => $updates]);
	echo json_encode(['success' => true, 'message' => 'Exam updated.', 'updated' => $result->getModifiedCount()]);
} catch (Throwable $e) { http_response_code(500); echo json_encode(['success' => false, 'message' => 'Failed to update exam.', 'error' => $e->getMessage()]); }
