<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

session_start();
require_once __DIR__ . '/../config/database.php';

// Authenticate session
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized access. Please sign in.']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
$action  = $payload['action'] ?? '';

try {
    if ($action === 'add_resource') {
        $name     = trim($payload['name'] ?? '');
        $category = $payload['category'] ?? 'Human / Staff';
        $capacity = (float)($payload['capacity'] ?? 8.0);
        $skills   = trim($payload['skills'] ?? '');

        if (!$name || !$skills || $capacity <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Please fill all resource fields properly.']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO resources (name, category, capacity, active_load, status, skills) VALUES (?, ?, ?, 0.00, 'available', ?)");
        $stmt->execute([$name, $category, $capacity, $skills]);

        // Audit log entry
        if (isset($_SESSION['user_id'])) {
            $audit = $pdo->prepare("INSERT INTO audit_logs (user_id, action, target_entity, details) VALUES (?, 'ADD_RESOURCE', 'resources', ?)");
            $audit->execute([$_SESSION['user_id'], "Added resource: {$name} ({$category})"]);
        }

        echo json_encode(['success' => true, 'message' => 'Resource added successfully!']);
        exit;
    }

    if ($action === 'add_task') {
        $title    = trim($payload['title'] ?? '');
        $skill    = trim($payload['required_skill'] ?? '');
        $priority = (int)($payload['priority'] ?? 3);
        $effort   = (float)($payload['estimated_effort'] ?? 2.0);

        if (!$title || !$skill || $effort <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Please fill all task fields properly.']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO tasks (title, required_skill, priority, estimated_effort, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$title, $skill, $priority, $effort]);

        echo json_encode(['success' => true, 'message' => 'New task added to queue!']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid action requested.']);
} catch (PDOException $e) {
    error_log('[Database Error in add_item.php] ' . $e->getMessage());
    http_response_code(500);
    // RETURN THE EXACT SQL ERROR MESSAGE FOR EASY DEBUGGING
    echo json_encode([
        'success' => false, 
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}