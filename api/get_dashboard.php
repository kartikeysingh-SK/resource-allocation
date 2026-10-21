<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

try {
    $resStmt = $pdo->query("SELECT id, name, category, capacity, active_load, status, skills FROM resources ORDER BY category ASC, id ASC");
    $resources = $resStmt->fetchAll();

    $taskStmt = $pdo->query("SELECT id, title, required_skill, priority, estimated_effort, status FROM tasks ORDER BY status ASC, priority DESC");
    $tasks = $taskStmt->fetchAll();

    $allocStmt = $pdo->query("
        SELECT a.id, a.task_id, t.title AS task_title, r.name AS resource_name, r.category, a.score, a.assigned_at 
        FROM allocations a
        JOIN tasks t ON a.task_id = t.id
        JOIN resources r ON a.resource_id = r.id
        WHERE a.status = 'active'
        ORDER BY a.assigned_at DESC
    ");
    $allocations = $allocStmt->fetchAll();

    echo json_encode([
        'success'     => true,
        'resources'   => $resources,
        'tasks'       => $tasks,
        'allocations' => $allocations
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to load dashboard data.']);
}