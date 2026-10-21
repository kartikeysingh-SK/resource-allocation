<?php
/**
 * Auth Guard — include this at the top of every authenticated page.
 * Starts the session, loads the database, and redirects to login if not authenticated.
 * Provides: $pdo, $userName, $userRole
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userName = (string)($_SESSION['full_name'] ?? 'User');
$userRole = (string)($_SESSION['role'] ?? 'Admin');
