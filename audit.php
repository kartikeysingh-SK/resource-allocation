<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth_check.php';

$pageTitle    = 'Notifications & Audit History | RTRAE Engine';
$pageSubtitle = 'Alerts & Audit Trail';
$currentPage  = 'audit';
$extraHeadTags = '<style>
    .alert-item { border-left: 4px solid #BD8957; background: rgba(189, 137, 87, 0.08); padding: 14px 18px; border-radius: 12px; margin-bottom: 12px; border: 1px solid rgba(189, 137, 87, 0.2); border-left-width: 4px; }
    .alert-critical { border-left-color: #DC2626 !important; background: rgba(220, 38, 38, 0.06) !important; border-color: rgba(220, 38, 38, 0.25) !important; border-left-width: 4px !important; }
</style>';

// 1. Fetch overloaded or near-capacity resources (>= 75% load) for live notifications
$overloadedResources = $pdo->query("
    SELECT name, category, active_load, capacity 
    FROM resources 
    WHERE capacity > 0 AND (active_load / capacity) >= 0.75
    ORDER BY (active_load / capacity) DESC
")->fetchAll();

// 2. Fetch critical pending tasks (Priority >= 4)
$urgentTasks = $pdo->query("
    SELECT id, title, required_skill, priority, estimated_effort, created_at
    FROM tasks
    WHERE status = 'pending' AND priority >= 4
    ORDER BY priority DESC, created_at ASC
")->fetchAll();

// 3. Fetch complete allocation audit log (both active and released)
$auditHistory = $pdo->query("
    SELECT a.id, a.task_id, a.score, a.status AS alloc_status, a.assigned_at,
           t.title AS task_title, t.required_skill, t.estimated_effort, t.status AS task_status,
           r.name AS resource_name, r.category
    FROM allocations a
    JOIN tasks t ON a.task_id = t.id
    JOIN resources r ON a.resource_id = r.id
    ORDER BY a.assigned_at DESC
")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container pb-5 anim-fade-up">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">System Notifications &amp; Audit Log</h3>
            <p class="text-muted small mb-0">Real-time capacity alerts, high-priority queue warnings, and immutable assignment history</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm fw-semibold"><i class="bi bi-arrow-left me-1"></i> Return to Control Center</a>
    </div>

    <!-- Live Notifications Row -->
    <div class="row g-4 mb-4 anim-fade-up delay-1">
        <div class="col-lg-6">
            <div class="glass-card p-4 h-100">
                <h6 class="fw-bold mb-3" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Capacity &amp; Overload Warnings (≥75% Load)
                </h6>
                <?php if (empty($overloadedResources)): ?>
                    <div class="text-muted small py-3 text-center"><i class="bi bi-check-circle-fill me-1" style="color:var(--brand-forest);"></i> All campus resources are operating within healthy capacity limits.</div>
                <?php else: ?>
                    <?php foreach ($overloadedResources as $or): 
                        $pct = round(($or['active_load'] / $or['capacity']) * 100);
                    ?>
                        <div class="alert-item <?= $pct >= 100 ? 'alert-critical' : '' ?> small">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong><?= htmlspecialchars($or['name']) ?> <span class="badge" style="background:rgba(49,95,67,0.1);color:#315F43;"><?= htmlspecialchars($or['category']) ?></span></strong>
                                <span class="fw-bold font-mono <?= $pct >= 100 ? 'text-danger' : '' ?>" style="<?= $pct < 100 ? 'color:var(--brand-bron);' : '' ?>"><?= $pct ?>% Load</span>
                            </div>
                            <div class="text-muted mt-1">Currently at <?= $or['active_load'] ?> / <?= $or['capacity'] ?> hours. Release completed tasks before assigning new duties.</div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="glass-card p-4 h-100">
                <h6 class="fw-bold mb-3" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">
                    <i class="bi bi-lightning-charge-fill me-2" style="color:var(--brand-bronze);"></i>High-Priority Pending Tasks (P4 – P5)
                </h6>
                <?php if (empty($urgentTasks)): ?>
                    <div class="text-muted small py-3 text-center"><i class="bi bi-check-circle-fill me-1" style="color:var(--brand-forest);"></i> No urgent P4/P5 tasks are waiting in the queue.</div>
                <?php else: ?>
                    <?php foreach ($urgentTasks as $ut): ?>
                        <div class="alert-item small d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= htmlspecialchars($ut['title']) ?></strong>
                                <div class="text-muted">Requires: <span class="badge" style="background:#171B18;color:#FFFDF7;"><?= htmlspecialchars($ut['required_skill']) ?></span> · Priority P<?= (int)$ut['priority'] ?> · <?= $ut['estimated_effort'] ?>h</div>
                            </div>
                            <a href="dashboard.php" class="btn btn-sm btn-primary fw-semibold">Allocate Now</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Complete Assignment History Table -->
    <div class="glass-card p-4 anim-fade-up delay-2">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h6 class="fw-bold mb-0" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">
                <i class="bi bi-journal-check me-2" style="color:var(--brand-forest);"></i>Complete Allocation &amp; Release Audit History
            </h6>
            <input type="text" id="auditSearch" class="form-control form-control-sm w-auto" placeholder="Filter history..." oninput="filterAudit()">
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small" id="auditTable">
                <thead>
                    <tr>
                        <th>Audit ID</th>
                        <th>Task / Requirement</th>
                        <th>Required Skill</th>
                        <th>Assigned Resource</th>
                        <th>Sector</th>
                        <th>Effort</th>
                        <th>Suitability Score</th>
                        <th>Lifecycle Status</th>
                        <th>Assigned Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($auditHistory)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">No allocation events have been recorded yet. Click "Allocate" on the Dashboard to generate audit records.</td></tr>
                    <?php else: ?>
                        <?php foreach ($auditHistory as $row): ?>
                            <tr>
                                <td class="fw-bold font-mono text-muted">#AUD-<?= (int)$row['id'] ?></td>
                                <td><strong style="color:var(--brand-charcoal);"><?= htmlspecialchars($row['task_title']) ?></strong></td>
                                <td><span class="badge" style="background:rgba(49,95,67,0.08);color:#315F43;border:1px solid rgba(49,95,67,0.2);"><?= htmlspecialchars($row['required_skill']) ?></span></td>
                                <td><?= htmlspecialchars($row['resource_name']) ?></td>
                                <td><?= htmlspecialchars($row['category']) ?></td>
                                <td class="font-mono"><?= $row['estimated_effort'] ?>h</td>
                                <td><span class="badge" style="background:#315F43;color:#FFFDF7;"><?= $row['score'] ?></span></td>
                                <td>
                                    <?php if ($row['alloc_status'] === 'active'): ?>
                                        <span class="badge" style="background:rgba(189,137,87,0.15);color:var(--brand-bronze);border:1px solid rgba(189,137,87,0.3);">ACTIVE</span>
                                    <?php else: ?>
                                        <span class="badge" style="background:rgba(49,95,67,0.12);color:var(--brand-forest);border:1px solid rgba(49,95,67,0.3);">RELEASED</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted font-mono"><?= htmlspecialchars($row['assigned_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
function filterAudit() {
    const q = document.getElementById('auditSearch').value.toLowerCase();
    document.querySelectorAll('#auditTable tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
</body>
</html>