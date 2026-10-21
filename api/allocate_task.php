<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../config/database.php';

$payload = json_decode(file_get_contents('php://input'), true);
$taskId  = isset($payload['task_id']) ? (int)$payload['task_id'] : 0;

if ($taskId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid task ID provided.']);
    exit;
}

try {
    // Begin transaction for concurrency control
    $pdo->beginTransaction();

    // 1. Lock the target task row
    $taskStmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND status = 'pending' FOR UPDATE");
    $taskStmt->execute([$taskId]);
    $task = $taskStmt->fetch();

    if (!$task) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Task not found or already assigned.']);
        exit;
    }

    $effort = (float)$task['estimated_effort'];
    $requiredSkill = trim($task['required_skill']);

    // 2. Lock and retrieve available candidate resources
    $resStmt = $pdo->query("SELECT * FROM resources WHERE status != 'inactive' FOR UPDATE");
    $resources = $resStmt->fetchAll();

    $bestResource = null;
    $highestScore = -1.0;

    foreach ($resources as $res) {
        $capacity = (float)$res['capacity'];
        $load     = (float)$res['active_load'];
        $freeCap  = $capacity - $load;

        // Skip candidate if remaining capacity is insufficient
        if ($freeCap < $effort) {
            continue;
        }

        // K: Skill Match Score (100 if skill matches, 40 partial/general fallback)
        $skillsList = array_map('trim', explode(',', $res['skills']));
        $hasSkill = in_array($requiredSkill, $skillsList, true) || stripos($res['skills'], $requiredSkill) !== false;
        $k = $hasSkill ? 100.0 : 40.0;

        // A: Available Capacity Ratio Score (0 to 100)
        $a = ($capacity > 0) ? (($freeCap / $capacity) * 100.0) : 0.0;

        // F: Workload Fairness Score (0 to 100)
        $f = ($capacity > 0) ? ((1.0 - ($load / $capacity)) * 100.0) : 100.0;

        // Weighted Suitability Formula: S = 0.50K + 0.30A + 0.20F
        $score = (0.50 * $k) + (0.30 * $a) + (0.20 * $f);

        if ($score > $highestScore) {
            $highestScore = $score;
            $bestResource = $res;
        }
    }

    if (!$bestResource) {
        $pdo->rollBack();
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'No eligible resource matches this task capacity or requirements.']);
        exit;
    }

    // 3. Update the chosen resource's active workload
    $updatedLoad   = (float)$bestResource['active_load'] + $effort;
    $updatedStatus = ($updatedLoad >= (float)$bestResource['capacity']) ? 'busy' : 'available';

    $updateRes = $pdo->prepare("UPDATE resources SET active_load = ?, status = ? WHERE id = ?");
    $updateRes->execute([$updatedLoad, $updatedStatus, $bestResource['id']]);

    // 4. Mark task as allocated
    $updateTask = $pdo->prepare("UPDATE tasks SET status = 'allocated' WHERE id = ?");
    $updateTask->execute([$taskId]);

    // 5. Insert audit allocation record[cite: 1]
    $insertAlloc = $pdo->prepare("INSERT INTO allocations (task_id, resource_id, score, status) VALUES (?, ?, ?, 'active')");
    $insertAlloc->execute([$taskId, $bestResource['id'], round($highestScore, 2)]);

    // Commit transaction successfully
    $pdo->commit();

    echo json_encode([
        'success'       => true,
        'resource_name' => $bestResource['name'],
        'score'         => round($highestScore, 2)
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('[API Error: allocate_task] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Allocation failed due to a database transaction error.']);
}