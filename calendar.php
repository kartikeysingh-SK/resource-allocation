<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth_check.php';

$pageTitle    = 'Scheduling Calendar | RTRAE Engine';
$pageSubtitle = 'Scheduling Calendar';
$currentPage  = 'calendar';
$extraHeadTags = '<style>
    .slot-cell { min-width: 95px; height: 58px; vertical-align: middle; text-align: center; font-size: 0.75rem; border-left: 1px solid rgba(49, 95, 67, 0.1); }
    .slot-booked { background: linear-gradient(135deg, #315F43, #234731); color: #FFFDF7; border-radius: 8px; padding: 6px; cursor: pointer; font-weight: 600; box-shadow: 0 4px 10px rgba(49, 95, 67, 0.25); border: 1px solid rgba(189, 137, 87, 0.4); transition: transform 0.2s, box-shadow 0.2s; }
    .slot-booked:hover { transform: translateY(-2px); box-shadow: 0 6px 14px rgba(49, 95, 67, 0.35); }
    .slot-preload { background: rgba(184, 203, 177, 0.3); color: #252B25; border-radius: 8px; padding: 6px; font-weight: 500; border: 1px solid rgba(49, 95, 67, 0.15); }
    .slot-free { color: #8C9389; font-size: 0.7rem; font-family: "JetBrains Mono", monospace; }
</style>';

$selectedSector = isset($_GET['sector']) ? (string)$_GET['sector'] : 'ALL';
$selectedDate   = isset($_GET['date']) ? (string)$_GET['date'] : date('Y-m-d');

$resources = [];
$activeAllocations = [];
$allocByResource = [];

try {
    // 1. Fetch resources safely
    if ($selectedSector !== 'ALL') {
        $resStmt = $pdo->prepare("SELECT id, name, category, capacity, active_load, status, skills FROM resources WHERE category = ? ORDER BY name ASC");
        $resStmt->execute([$selectedSector]);
        $resources = $resStmt->fetchAll() ?: [];
    } else {
        $resStmt = $pdo->query("SELECT id, name, category, capacity, active_load, status, skills FROM resources ORDER BY category ASC, name ASC");
        $resources = $resStmt ? $resStmt->fetchAll() : [];
    }

    // 2. Fetch active allocations safely
    $allocStmt = $pdo->query("
        SELECT a.id AS alloc_id, a.task_id, a.resource_id, a.score, a.assigned_at,
               t.title AS task_title, t.required_skill, t.priority, t.estimated_effort
        FROM allocations a
        JOIN tasks t ON a.task_id = t.id
        WHERE a.status = 'active'
        ORDER BY a.assigned_at ASC
    ");
    $activeAllocations = $allocStmt ? $allocStmt->fetchAll() : [];

    foreach ($activeAllocations as $al) {
        $rId = isset($al['resource_id']) ? (int)$al['resource_id'] : 0;
        if (!isset($allocByResource[$rId])) {
            $allocByResource[$rId] = [];
        }
        $allocByResource[$rId][] = $al;
    }
} catch (PDOException $e) {
    error_log('[Calendar Error] ' . $e->getMessage());
}

$timeSlots = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];
$slotCount = count($timeSlots);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container pb-5 anim-fade-up">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">Campus &amp; Resource Timetable</h3>
            <p class="text-muted small mb-0">Hourly allocation matrix (08:00 – 17:00) with real-time double-booking prevention</p>
        </div>
        <form method="GET" class="d-flex flex-wrap gap-2 align-items-center">
            <input type="date" name="date" class="form-control form-control-sm w-auto" value="<?= htmlspecialchars($selectedDate) ?>" onchange="this.form.submit()">
            <select name="sector" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="ALL" <?= $selectedSector === 'ALL' ? 'selected' : '' ?>>All Sectors</option>
                <option value="Room / Facility" <?= $selectedSector === 'Room / Facility' ? 'selected' : '' ?>>Room / Facility (B01-B13, Wings)</option>
                <option value="Human / Staff" <?= $selectedSector === 'Human / Staff' ? 'selected' : '' ?>>Human / Staff (Teachers, Peons)</option>
                <option value="IT / Cloud Server" <?= $selectedSector === 'IT / Cloud Server' ? 'selected' : '' ?>>IT / Cloud Server (Labs 101-104)</option>
                <option value="Equipment / Fleet" <?= $selectedSector === 'Equipment / Fleet' ? 'selected' : '' ?>>Equipment / Fleet (Vehicles)</option>
                <option value="Service / Catering" <?= $selectedSector === 'Service / Catering' ? 'selected' : '' ?>>Service / Catering (Food)</option>
            </select>
            <a href="dashboard.php" class="btn btn-sm btn-primary fw-semibold"><i class="bi bi-plus-circle me-1"></i> Allocate Task</a>
        </form>
    </div>

    <!-- Editorial Legend Chips -->
    <div class="d-flex flex-wrap gap-3 mb-3 small align-items-center anim-fade-up delay-1">
        <span><span class="badge" style="background:#315F43;color:#FFFDF7;border:1px solid rgba(189,137,87,0.4);">■</span> Active RTRAE Assignment (Click to inspect)</span>
        <span><span class="badge" style="background:rgba(184, 203, 177, 0.4);color:#252B25;border:1px solid rgba(49, 95, 67, 0.2);">■</span> Baseline Scheduled Load</span>
        <span><span class="badge" style="background:#FFFDF7;color:#8C9389;border:1px solid rgba(49, 95, 67, 0.15);">■</span> Available Slot</span>
        <span><span class="badge" style="background:#DC2626;color:#FFFFFF;">■</span> At 100% Capacity (Locked)</span>
    </div>

    <div class="glass-card p-3 anim-fade-up delay-2">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="small text-center">
                    <tr>
                        <th class="text-start" style="min-width: 230px;">Resource &amp; Sector</th>
                        <th>Load</th>
                        <?php foreach ($timeSlots as $slot): ?>
                            <th><?= htmlspecialchars($slot) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($resources)): ?>
                        <tr><td colspan="11" class="text-center text-muted py-4">No resources found for this sector.</td></tr>
                    <?php else: ?>
                        <?php foreach ($resources as $res): 
                            $resId     = isset($res['id']) ? (int)$res['id'] : 0;
                            $resName   = isset($res['name']) ? (string)$res['name'] : 'Resource';
                            $resCat    = isset($res['category']) ? (string)$res['category'] : 'General';
                            $cap       = isset($res['capacity']) ? (float)$res['capacity'] : 8.0;
                            $load      = isset($res['active_load']) ? (float)$res['active_load'] : 0.0;
                            $resAllocs = isset($allocByResource[$resId]) ? $allocByResource[$resId] : [];

                            $slotMap = array_fill(0, $slotCount, null);
                            $currentHourIdx = 0;
                            $allocatedHoursSum = 0;

                            foreach ($resAllocs as $al) {
                                $effortVal = isset($al['estimated_effort']) ? (float)$al['estimated_effort'] : 1.0;
                                $hrsCount  = max(1, (int)round($effortVal));
                                $allocatedHoursSum += $hrsCount;

                                for ($h = 0; $h < $hrsCount && $currentHourIdx < $slotCount; $h++) {
                                    $slotMap[$currentHourIdx] = ['type' => 'alloc', 'data' => $al];
                                    $currentHourIdx++;
                                }
                            }

                            $baselineHours = max(0, (int)round($load - $allocatedHoursSum));
                            for ($b = 0; $b < $baselineHours && $currentHourIdx < $slotCount; $b++) {
                                $slotMap[$currentHourIdx] = ['type' => 'baseline'];
                                $currentHourIdx++;
                            }

                            $isFull = ($cap > 0 && $load >= $cap);
                        ?>
                            <tr>
                                <td>
                                    <div class="fw-bold small" style="color:var(--brand-charcoal);"><?= htmlspecialchars($resName) ?></div>
                                    <span class="badge" style="font-size:0.68rem; background:rgba(49, 95, 67, 0.08); color:var(--brand-forest); border:1px solid rgba(49, 95, 67, 0.2);"><?= htmlspecialchars($resCat) ?></span>
                                    <?php if ($isFull): ?>
                                        <span class="badge bg-danger ms-1" style="font-size:0.65rem;">FULL</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center small fw-bold <?= $isFull ? 'text-danger' : 'text-muted' ?>" style="font-family:'JetBrains Mono',monospace;">
                                    <?= htmlspecialchars((string)$load) ?> / <?= htmlspecialchars((string)$cap) ?>h
                                </td>
                                <?php for ($idx = 0; $idx < $slotCount; $idx++): 
                                    $cell = $slotMap[$idx];
                                ?>
                                    <td class="slot-cell">
                                        <?php if (is_array($cell) && $cell['type'] === 'alloc'): 
                                            $al = $cell['data'];
                                            $tId     = isset($al['task_id']) ? (int)$al['task_id'] : 0;
                                            $tTitle  = isset($al['task_title']) ? (string)$al['task_title'] : 'Task';
                                            $tSkill  = isset($al['required_skill']) ? (string)$al['required_skill'] : '';
                                            $tPrio   = isset($al['priority']) ? (int)$al['priority'] : 3;
                                            $tEffort = isset($al['estimated_effort']) ? (string)$al['estimated_effort'] : '1';
                                            $tScore  = isset($al['score']) ? (string)$al['score'] : '0';
                                            $tTime   = isset($al['assigned_at']) ? (string)$al['assigned_at'] : '';
                                        ?>
                                            <div class="slot-booked"
                                                 data-task-id="<?= $tId ?>"
                                                 data-title="<?= htmlspecialchars($tTitle, ENT_QUOTES) ?>"
                                                 data-resource="<?= htmlspecialchars($resName, ENT_QUOTES) ?>"
                                                 data-skill="<?= htmlspecialchars($tSkill, ENT_QUOTES) ?>"
                                                 data-priority="<?= $tPrio ?>"
                                                 data-effort="<?= htmlspecialchars($tEffort, ENT_QUOTES) ?>"
                                                 data-score="<?= htmlspecialchars($tScore, ENT_QUOTES) ?>"
                                                 data-assigned="<?= htmlspecialchars($tTime, ENT_QUOTES) ?>"
                                                 onclick="openSlotModalFromEl(this)">
                                                <div class="text-truncate" style="max-width: 90px;"><?= htmlspecialchars($tTitle) ?></div>
                                                <small style="color:var(--brand-sage);font-size:0.68rem;">P<?= $tPrio ?> · S:<?= htmlspecialchars($tScore) ?></small>
                                            </div>
                                        <?php elseif (is_array($cell) && $cell['type'] === 'baseline'): ?>
                                            <div class="slot-preload">
                                                <div>Scheduled</div>
                                                <small style="color:var(--brand-text-muted);font-size:0.68rem;">Base Duty</small>
                                            </div>
                                        <?php else: ?>
                                            <span class="slot-free">Available</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Inspect Scheduled Slot -->
<div class="modal fade" id="slotDetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:#FFFDF7;border:1px solid rgba(49, 95, 67, 0.2);border-radius:20px;">
            <div class="modal-header" style="border-bottom:1px solid rgba(49, 95, 67, 0.15);">
                <h5 class="modal-title fw-bold" style="font-family:'Playfair Display',serif;color:var(--brand-charcoal);">Scheduled Assignment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body small" id="slotModalBody"></div>
            <div class="modal-footer" style="border-top:1px solid rgba(49, 95, 67, 0.15);">
                <button type="button" class="btn btn-outline-danger btn-sm fw-semibold" id="modalReleaseBtn">Release Resource Slot</button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script>
function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
}

function openSlotModalFromEl(el) {
    const d = el.dataset;
    document.getElementById('slotModalBody').innerHTML = `
        <div class="mb-2"><strong>Task Title:</strong> ${escapeHtml(d.title)}</div>
        <div class="mb-2"><strong>Assigned Resource:</strong> ${escapeHtml(d.resource)}</div>
        <div class="mb-2"><strong>Required Capability:</strong> <span class="badge" style="background:rgba(49,95,67,0.1);color:#315F43;">${escapeHtml(d.skill)}</span></div>
        <div class="mb-2"><strong>Priority Level:</strong> P${escapeHtml(d.priority)}</div>
        <div class="mb-2"><strong>Duration / Effort:</strong> ${escapeHtml(d.effort)} Hours</div>
        <div class="mb-2"><strong>Suitability Score (S):</strong> <span class="badge" style="background:#315F43;color:#FFFDF7;">${escapeHtml(d.score)}</span></div>
        <div class="mb-0"><strong>Assigned Timestamp:</strong> <span class="font-mono">${escapeHtml(d.assigned)}</span></div>
    `;
    const btn = document.getElementById('modalReleaseBtn');
    btn.onclick = async () => {
        const res = await fetch('api/release_task.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ task_id: Number(d.taskId) })
        });
        const r = await res.json();
        if (r.success) location.reload();
    };
    new bootstrap.Modal(document.getElementById('slotDetailModal')).show();
}
</script>
</body>
</html>