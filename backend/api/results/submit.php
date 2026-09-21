<?php
require_once __DIR__ . '/../../bootstrap.php';
$user = requireAuth('student');
$input = requestBody();
$examId = (string) ($input['exam_id'] ?? '');
$answers = $input['answers'] ?? [];
if (!MongoDB\BSON\ObjectId::isValid($examId) || !is_array($answers)) jsonResponse(['error' => 'Exam and answers are required'], 422);
$exam = collection('exams')->findOne(['_id' => objectId($examId), 'published' => true]);
if (!$exam) jsonResponse(['error' => 'Published exam not found'], 404);
$questions = iterator_to_array(collection('questions')->find(['exam_id' => objectId($examId)]));
$score = 0;
foreach ($questions as $question) if (($answers[(string) $question['_id']] ?? null) === $question['correct_answer']) $score++;
$result = ['exam_id' => objectId($examId), 'student_id' => objectId($user['id']), 'student_name' => $user['name'], 'exam_title' => $exam['title'], 'score' => $score, 'total' => count($questions), 'answers' => $answers, 'submitted_at' => new MongoDB\BSON\UTCDateTime()];
$inserted = collection('results')->insertOne($result);
$result['_id'] = $inserted->getInsertedId();
jsonResponse(['result' => serializeDocument($result)], 201);
