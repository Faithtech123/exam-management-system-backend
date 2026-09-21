<?php
require_once __DIR__ . '/../config/config.php';
function currentUser(): ?array
{
    if (isset($_SESSION['user'])) return $_SESSION['user'];
    if (!isset($_SESSION['user_id'])) return null;
    return ['id' => $_SESSION['user_id'], 'name' => $_SESSION['name'] ?? '', 'email' => $_SESSION['email'] ?? '', 'role' => $_SESSION['role'] ?? 'student'];
}
function requireAuth(?string $role = null): array {
    $user = currentUser();
    if (!$user) jsonResponse(['error' => 'Authentication required'], 401);
    if ($role !== null && $user['role'] !== $role) jsonResponse(['error' => 'Permission denied'], 403);
    return $user;
}

function requireLogin()
{
    return requireAuth();
}

function requireRole($role)
{
    return requireAuth($role);
}