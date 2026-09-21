<?php
require_once __DIR__ . '/../../bootstrap.php';
requireAuth('admin');
$items = [];
foreach (collection('results')->find([], ['sort' => ['submitted_at' => -1]]) as $result) $items[] = serializeDocument($result);
jsonResponse(['results' => $items]);
