<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$payload = json_decode(file_get_contents('php://input'), true);
$taskId  = isset($payload['task_id']) ? (int)$payload['task_id'] : 0;

if ($taskId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid Task ID']);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT a.id AS alloc_id, a.resource_id, t.estimated_effort 
        FROM allocations a
        JOIN tasks t ON a.task_id = t.id
        WHERE a.task_id = ? AND a.status = 'active'
        FOR UPDATE
    ");
    $stmt->execute([$taskId]);
    $alloc = $stmt->fetch();

    if (!$alloc) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Active allocation not found']);
        exit;
    }

    $pdo->prepare("UPDATE resources SET active_load = GREATEST(0, active_load - ?), status = 'available' WHERE id = ?")
        ->execute([$alloc['estimated_effort'], $alloc['resource_id']]);

    $pdo->prepare("UPDATE tasks SET status = 'completed' WHERE id = ?")->execute([$taskId]);
    $pdo->prepare("UPDATE allocations SET status = 'released' WHERE id = ?")->execute([$alloc['alloc_id']]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Release failed']);
}