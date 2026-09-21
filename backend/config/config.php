<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
require_once __DIR__ . '/database.php';

function jsonResponse(array $data, int $status = 200): never { http_response_code($status); echo json_encode($data, JSON_UNESCAPED_SLASHES); exit; }
function requestBody(): array { $body = json_decode(file_get_contents('php://input'), true); return is_array($body) ? $body : $_POST; }
function objectId(string $id): MongoDB\BSON\ObjectId { try { return new MongoDB\BSON\ObjectId($id); } catch (Throwable) { jsonResponse(['error' => 'Invalid resource id'], 400); } }
