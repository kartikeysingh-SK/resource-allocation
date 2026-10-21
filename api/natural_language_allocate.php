<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$payload = json_decode(file_get_contents('php://input'), true);
$prompt  = trim($payload['prompt'] ?? '');

if (!$prompt) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Please provide a resource request prompt.']);
    exit;
}

try {
    $lowerPrompt = strtolower($prompt);
    
    // 1. Identify Required Skill / Facility Category
    $requiredSkill = 'General';
    if (strpos($lowerPrompt, 'lab') !== false || strpos($lowerPrompt, 'computer') !== false || strpos($lowerPrompt, 'coding') !== false) {
        $requiredSkill = 'Computer Lab';
    } elseif (strpos($lowerPrompt, 'classroom') !== false || strpos($lowerPrompt, 'lecture') !== false || strpos($lowerPrompt, 'students') !== false) {
        $requiredSkill = 'Smartboard';
    } elseif (strpos($lowerPrompt, 'server') !== false || strpos($lowerPrompt, 'cloud') !== false || strpos($lowerPrompt, 'ai') !== false) {
        $requiredSkill = 'AI Training';
    } elseif (strpos($lowerPrompt, 'meeting') !== false || strpos($lowerPrompt, 'room') !== false || strpos($lowerPrompt, 'boardroom') !== false) {
        $requiredSkill = 'Meeting';
    } elseif (strpos($lowerPrompt, 'peon') !== false || strpos($lowerPrompt, 'delivery') !== false) {
        $requiredSkill = 'Peon';
    } elseif (strpos($lowerPrompt, 'vehicle') !== false || strpos($lowerPrompt, 'van') !== false || strpos($lowerPrompt, 'car') !== false) {
        $requiredSkill = 'Vehicle';
    } elseif (strpos($lowerPrompt, 'food') !== false || strpos($lowerPrompt, 'catering') !== false || strpos($lowerPrompt, 'lunch') !== false) {
        $requiredSkill = 'Food';
    }

    // 2. Extract Student/Capacity Count (e.g., "90 students")
    $requiredCapacity = 1.0;
    if (preg_match('/(\d+)\s*(students|people|seats|attendees)/', $lowerPrompt, $matches)) {
        $requiredCapacity = (float)$matches[1];
    }

    // 3. Calculate Effort Duration (e.g., "2:00–4:00 PM" or "2 hours")
    $effort = 2.00; // Default duration
    if (strpos($lowerPrompt, '2:00') !== false && strpos($lowerPrompt, '4:00') !== false) {
        $effort = 2.00; // 2:00 to 4:00 is 2 hours
    } elseif (strpos($lowerPrompt, '9:00') !== false && strpos($lowerPrompt, '11:00') !== false) {
        $effort = 2.00;
    } elseif (preg_match('/(\d+)\s*hour/', $lowerPrompt, $matches)) {
        $effort = (float)$matches[1];
    }

    $pdo->beginTransaction();

    // 4. Record prompt as a pending task request
    $taskStmt = $pdo->prepare("INSERT INTO tasks (title, required_skill, priority, estimated_effort, status) VALUES (?, ?, 5, ?, 'pending')");
    $taskStmt->execute([$prompt, $requiredSkill, $effort]);
    $taskId = (int)$pdo->lastInsertId();

    // 5. Fetch candidate resources with row locking
    $resStmt = $pdo->query("SELECT * FROM resources WHERE status != 'inactive' FOR UPDATE");
    $resources = $resStmt->fetchAll();

    $bestResource = null;
    $highestScore = -1.0;

    foreach ($resources as $res) {
        $maxCap = (float)$res['capacity'];
        $load   = (float)$res['active_load'];
        $freeCap = $maxCap - $load;

        // Skip if remaining load capacity is insufficient
        if ($freeCap < $effort) {
            continue;
        }

        // Check if resource matches skill or capacity requirements
        $skillsList = array_map('trim', explode(',', $res['skills']));
        $hasSkillMatch = false;
        foreach ($skillsList as $sk) {
            if (stripos($sk, $requiredSkill) !== false || stripos($requiredSkill, $sk) !== false || stripos($res['name'], $requiredSkill) !== false) {
                $hasSkillMatch = true;
                break;
            }
        }

        // If user requested specific student capacity (e.g. 90 students), check if resource description/skills accommodate it
        $accommodatesSize = true;
        if ($requiredCapacity > 10) {
            // Check if resource name/skills specify capacity or size (e.g. "Capacity 90")
            if (stripos($res['skills'], 'capacity') !== false || stripos($res['name'], 'capacity') !== false) {
                preg_match('/capacity\s*(\d+)/i', $res['skills'] . ' ' . $res['name'], $capMatch);
                if (isset($capMatch[1]) && (float)$capMatch[1] < $requiredCapacity) {
                    $accommodatesSize = false; // Room is too small for 90 students
                }
            }
        }

        if (!$hasSkillMatch || !$accommodatesSize) {
            continue;
        }

        // Scoring components: K (Skill), A (Availability), F (Fairness)
        $k = 100.0;
        $a = ($maxCap > 0) ? (($freeCap / $maxCap) * 100.0) : 0.0;
        $f = ($maxCap > 0) ? ((1.0 - ($load / $maxCap)) * 100.0) : 100.0;

        // Formula: S = 0.50K + 0.30A + 0.20F
        $score = (0.50 * $k) + (0.30 * $a) + (0.20 * $f);

        if ($score > $highestScore) {
            $highestScore = $score;
            $bestResource = $res;
        }
    }

    if (!$bestResource) {
        $pdo->rollBack();
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'No available resource found matching the student capacity or capability requirements.']);
        exit;
    }

    // 6. Update Resource Workload
    $updatedLoad   = (float)$bestResource['active_load'] + $effort;
    $updatedStatus = ($updatedLoad >= (float)$bestResource['capacity']) ? 'busy' : 'available';

    $pdo->prepare("UPDATE resources SET active_load = ?, status = ? WHERE id = ?")
        ->execute([$updatedLoad, $updatedStatus, $bestResource['id']]);

    // 7. Mark Task Allocated
    $pdo->prepare("UPDATE tasks SET status = 'allocated' WHERE id = ?")->execute([$taskId]);

    // 8. Record Allocation Audit
    $pdo->prepare("INSERT INTO allocations (task_id, resource_id, score, status) VALUES (?, ?, ?, 'active')")
        ->execute([$taskId, $bestResource['id'], round($highestScore, 2)]);

    $pdo->commit();

    echo json_encode([
        'success'       => true,
        'resource_name' => $bestResource['name'],
        'category'      => $bestResource['category'],
        'score'         => round($highestScore, 2),
        'parsed_effort' => $effort
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Natural language allocation failed.']);
}