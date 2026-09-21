<?php
require_once __DIR__ . '/../../bootstrap.php';
jsonResponse(['user' => currentUser()]);