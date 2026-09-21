<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use MongoDB\Client;

function loadEnvironment(): void
{
    $envFile = __DIR__ . '/../../.env';
    if (!is_file($envFile)) return;
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        if (getenv(trim($key)) === false) putenv(trim($key) . '=' . trim($value, " \t\""));
    }
}

function getDatabase()
{
    static $database;
    if ($database !== null) return $database;
    loadEnvironment();
    $uri = getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1:27017';
    $databaseName = getenv('DB_NAME') ?: 'exam_management';

    $client = new Client($uri);

    $database = $client->selectDatabase($databaseName);
    return $database;
}