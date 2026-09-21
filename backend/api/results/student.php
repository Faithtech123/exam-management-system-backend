<?php
require_once __DIR__ . '/../../bootstrap.php';
$user = requireAuth('student');
$items = [];
foreach (collection('results')->find(['student_id' => objectId($user['id'])], ['sort' => ['submitted_at' => -1]]) as $result) $items[] = serializeDocument($result);
jsonResponse(['results' => $items]);
