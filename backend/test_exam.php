<?php

require_once __DIR__ . '/config/database.php';

try {

    $db = getDatabase();

    echo "Database connection successful\n";

    $exam = $db->selectCollection("exams")->findOne([
        "_id" => new MongoDB\BSON\ObjectId("6ab0926522fc5614e002dd95")
    ]);

    if ($exam === null) {
        echo "Exam not found.\n";
        exit;
    }

    echo "Exam found:\n";

    echo "Title: " . $exam['title'] . "\n";

    echo "Published: ";

    var_dump($exam['published']);

} catch (Throwable $e) {

    echo "ERROR:\n";
    echo $e->getMessage();
}