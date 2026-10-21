<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth_check.php';

$pageTitle    = 'Analytics & Reports | RTRAE Engine';
$pageSubtitle = 'Analytics & Reports';
$currentPage  = 'analytics';
$extraHeadTags = '<style>
    .analytics-card { background: #FFFDF7 !important; border: 1px solid rgba(49, 95, 67, 0.16) !important; border-radius: 20px !important; box-shadow: 0 10px 30px -5px rgba(23, 27, 24, 0.05); }
    .kpi-num { font-family: "Playfair Display", Georgia, serif; font-size: 2.2rem; font-weight: 700; line-height: 1.1; }
    .progress-bar-forest { background-color: #315F43 !important; }
    .progress-bar-bronze { background-color: #BD8957 !important; }
    .progress-bar-danger { background-color: #DC2626 !important; }
</style>';

// Fetch live analytical metrics from database safely
$resStats = ['total_resources' => 0, 'total_capacity' => 0, 'total_load' => 0];
$taskStats = [];
$categoryStats = [];
$recentAllocations = [];

try {
    // 1. Total resources and total capacity/load
    $resStmt = $pdo->query("
        SELECT 
            COUNT(*) as total_resources,
            SUM(capacity) as total_capacity,
            SUM(active_load) as total_load
        FROM resources
    ");
    if ($resStmt) {
        $resStats = $resStmt->fetch() ?: $resStats;
    }

    // 2. Task status distribution
    $taskStmt = $pdo->query("
        SELECT status, COUNT(*) as count 
        FROM tasks 
        GROUP BY status
    ");
    if ($taskStmt) {
        $taskStats = $taskStmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    // 3. Category-wise utilization breakdown
    $catStmt = $pdo->query("
        SELECT 
            category,
            COUNT(*) as resource_count,
            SUM(capacity) as sector_capacity,
            SUM(active_load) as sector_load
        FROM resources
        GROUP BY category
    ");
    if ($catStmt) {
        $categoryStats = $catStmt->fetchAll() ?: [];
    }

    // 4. Recent allocation audit history
    $allocStmt = $pdo->query("
        SELECT a.score, a.assigned_at, t.title AS task_title, r.name AS resource_name, r.category
        FROM allocations a
        JOIN tasks t ON a.task_id = t.id
        JOIN resources r ON a.resource_id = r.id
        ORDER BY a.assigned_at DESC
        LIMIT 10
    ");
    if ($allocStmt) {
        $recentAllocations = $allocStmt->fetchAll() ?: [];
    }

} catch (PDOException $e) {
    error_log('[Analytics Error] ' . $e->getMessage());
}

$totalCap = isset($resStats['total_capacity']) ? (float)$resStats['total_capacity'] : 0.0;
$totalLoad = isset($resStats['total_load']) ? (float)$resStats['total_load'] : 0.0;
$overallUtil = ($totalCap > 0) ? round(($totalLoad / $totalCap) * 100) : 0;

$totalTasksCount = array_sum($taskStats);
$completedTasks = isset($taskStats['completed']) ? (int)$taskStats['completed'] : 0;
$successRate = ($totalTasksCount > 0) ? round(($completedTasks / $totalTasksCount) * 100) : 100;

require_once __DIR__ . '/includes/header.php';
?>

<div class="container pb-5 anim-fade-up">
    <!-- Header Toolbar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">Operational Telemetry &amp; Reports</h3>
            <p class="text-muted small mb-0">Real-time resource utilization telemetry, sector load distribution, and assignment audit logs</p>
        </div>
        <div class="no-print d-flex gap-2">
            <button class="btn btn-primary btn-sm fw-semibold px-3" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print / Save PDF Report
            </button>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm fw-semibold px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Executive Summary KPIs -->
    <div class="row g-3 mb-4 anim-fade-up delay-1">
        <div class="col-md-3 col-sm-6">
            <div class="analytics-card p-3.5 h-100">
                <small class="text-muted fw-semibold">Overall System Utilization</small>
                <div class="kpi-num mt-1" style="color:var(--brand-forest);"><?= $overallUtil ?>%</div>
                <small class="text-muted" style="font-size:0.75rem;"><?= $totalLoad ?>h active of <?= $totalCap ?>h capacity</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="analytics-card p-3.5 h-100">
                <small class="text-muted fw-semibold">Allocation Success Rate</small>
                <div class="kpi-num mt-1" style="color:var(--brand-forest);"><?= $successRate ?>%</div>
                <small class="text-muted" style="font-size:0.75rem;"><?= $completedTasks ?> completed tasks</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="analytics-card p-3.5 h-100">
                <small class="text-muted fw-semibold">Total Managed Resources</small>
                <div class="kpi-num mt-1" style="color:var(--brand-charcoal);"><?= isset($resStats['total_resources']) ? (int)$resStats['total_resources'] : 0 ?></div>
                <small style="color:var(--brand-bronze); font-size:0.75rem;">Across all campus wings &amp; labs</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="analytics-card p-3.5 h-100">
                <small class="text-muted fw-semibold">Pending Task Queue</small>
                <div class="kpi-num mt-1" style="color:var(--brand-bronze);"><?= isset($taskStats['pending']) ? (int)$taskStats['pending'] : 0 ?></div>
                <small class="text-muted" style="font-size:0.75rem;">Awaiting smart allocation</small>
            </div>
        </div>
    </div>

    <!-- Sector Breakdown Table -->
    <div class="analytics-card p-4 mb-4 anim-fade-up delay-2">
        <h5 class="fw-bold mb-3" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">
            <i class="bi bi-pie-chart-fill me-2" style="color:var(--brand-forest);"></i>Infrastructure Sector Utilization Breakdown
        </h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Sector Category</th>
                        <th>Total Resources</th>
                        <th>Total Capacity (Hours)</th>
                        <th>Active Load (Hours)</th>
                        <th>Sector Utilization</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categoryStats)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">No sector data available.</td></tr>
                    <?php else: ?>
                        <?php foreach ($categoryStats as $cat): 
                            $catName = isset($cat['category']) ? (string)$cat['category'] : 'General';
                            $resCount = isset($cat['resource_count']) ? (int)$cat['resource_count'] : 0;
                            $secCap = isset($cat['sector_capacity']) ? (float)$cat['sector_capacity'] : 0.0;
                            $secLoad = isset($cat['sector_load']) ? (float)$cat['sector_load'] : 0.0;
                            $secPct = ($secCap > 0) ? round(($secLoad / $secCap) * 100) : 0;
                            $barClass = $secPct >= 80 ? 'progress-bar-danger' : ($secPct >= 50 ? 'progress-bar-bronze' : 'progress-bar-forest');
                        ?>
                            <tr>
                                <td><strong style="color:var(--brand-charcoal);"><?= htmlspecialchars($catName) ?></strong></td>
                                <td class="font-mono"><?= $resCount ?></td>
                                <td class="font-mono"><?= $secCap ?> hrs</td>
                                <td class="font-mono"><?= $secLoad ?> hrs</td>
                                <td style="width: 30%;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 10px; background: rgba(49, 95, 67, 0.1); border-radius: 6px;">
                                            <div class="progress-bar <?= $barClass ?>" style="width: <?= min(100, $secPct) ?>%; border-radius: 6px;"></div>
                                        </div>
                                        <span class="small fw-bold font-mono text-muted"><?= $secPct ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Assignment Audit Trail -->
    <div class="analytics-card p-4 anim-fade-up delay-3">
        <h5 class="fw-bold mb-3" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">
            <i class="bi bi-clock-history me-2" style="color:var(--brand-bronze);"></i>Recent Assignment Audit Trail
        </h5>
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0 small">
                <thead>
                    <tr>
                        <th>Task Title</th>
                        <th>Assigned Resource</th>
                        <th>Sector</th>
                        <th>Suitability Score</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentAllocations)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">No allocation history recorded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentAllocations as $alloc): 
                            $tTitle = isset($alloc['task_title']) ? (string)$alloc['task_title'] : 'Task';
                            $rName  = isset($alloc['resource_name']) ? (string)$alloc['resource_name'] : 'Resource';
                            $rCat   = isset($alloc['category']) ? (string)$alloc['category'] : 'General';
                            $aScore = isset($alloc['score']) ? (string)$alloc['score'] : '0';
                            $aTime  = isset($alloc['assigned_at']) ? (string)$alloc['assigned_at'] : '';
                        ?>
                            <tr>
                                <td><strong style="color:var(--brand-charcoal);"><?= htmlspecialchars($tTitle) ?></strong></td>
                                <td><?= htmlspecialchars($rName) ?></td>
                                <td><span class="badge" style="background:rgba(49,95,67,0.08);color:var(--brand-forest);border:1px solid rgba(49,95,67,0.2);"><?= htmlspecialchars($rCat) ?></span></td>
                                <td><span class="badge" style="background:#315F43;color:#FFFDF7;"><?= htmlspecialchars($aScore) ?></span></td>
                                <td class="text-muted font-mono"><?= htmlspecialchars($aTime) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>