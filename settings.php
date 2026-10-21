<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth_check.php';

$pageTitle    = 'System Settings | RTRAE Engine';
$pageSubtitle = 'System Configuration';
$currentPage  = 'settings';
$msg = '';
$err = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $orgName = trim($_POST['org_name'] ?? 'RTRAE Campus Operations');
    $wk      = (float)($_POST['weight_skill'] ?? 0.50);
    $wa      = (float)($_POST['weight_availability'] ?? 0.30);
    $wf      = (float)($_POST['weight_fairness'] ?? 0.20);
    $start   = trim($_POST['workday_start'] ?? '08:00');
    $end     = trim($_POST['workday_end'] ?? '17:00');

    $sum = round($wk + $wa + $wf, 2);
    if (abs($sum - 1.00) > 0.01) {
        $err = "Formula weights (K + A + F) must add up to 1.00 (currently {$sum}).";
    } else {
        $upd = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        $upd->execute(['org_name', $orgName]);
        $upd->execute(['weight_skill', (string)$wk]);
        $upd->execute(['weight_availability', (string)$wa]);
        $upd->execute(['weight_fairness', (string)$wf]);
        $upd->execute(['workday_start', $start]);
        $upd->execute(['workday_end', $end]);
        $msg = "System configuration and suitability formula weights saved successfully.";
    }
}

$settings = $pdo->query("SELECT setting_key, setting_value FROM system_settings")->fetchAll(PDO::FETCH_KEY_PAIR);
$geminiConfigured = !empty(getenv('GEMINI_API_KEY'));

require_once __DIR__ . '/includes/header.php';
?>

<div class="container pb-5 anim-fade-up">
    <div class="mb-4">
        <h3 class="fw-bold mb-1" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">System Settings &amp; Algorithm Weights</h3>
        <p class="text-muted small mb-0">Configure suitability scoring weights, campus operating hours, and inspect backend service health</p>
    </div>

    <?php if ($msg): ?>
        <div class="alert py-2.5 small d-flex align-items-center gap-2 mb-3" style="background:rgba(49,95,67,0.1); border:1px solid rgba(49,95,67,0.25); color:var(--brand-forest); border-radius:12px;">
            <i class="bi bi-check-circle-fill"></i>
            <span><?= htmlspecialchars($msg) ?></span>
        </div>
    <?php endif; ?>
    <?php if ($err): ?>
        <div class="alert py-2.5 small d-flex align-items-center gap-2 mb-3" style="background:rgba(220,38,38,0.08); border:1px solid rgba(220,38,38,0.25); color:#DC2626; border-radius:12px;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span><?= htmlspecialchars($err) ?></span>
        </div>
    <?php endif; ?>

    <div class="row g-4 anim-fade-up delay-1">
        <div class="col-lg-7">
            <form method="POST" class="glass-card p-4">
                <h5 class="fw-bold mb-3" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">
                    <i class="bi bi-sliders me-2" style="color:var(--brand-forest);"></i>Suitability Scoring Weights (S = wK·K + wA·A + wF·F)
                </h5>
                <p class="small text-muted mb-3">Adjust how the RTRAE solver balances skill matching, availability, and equitable load sharing across facilities and staff.</p>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Skill Match (wK)</label>
                        <input type="number" step="0.05" min="0" max="1" name="weight_skill" class="form-control font-mono" value="<?= htmlspecialchars($settings['weight_skill'] ?? '0.50') ?>" required>
                        <small class="text-muted" style="font-size:0.72rem;">Default: 0.50 (50%)</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Availability (wA)</label>
                        <input type="number" step="0.05" min="0" max="1" name="weight_availability" class="form-control font-mono" value="<?= htmlspecialchars($settings['weight_availability'] ?? '0.30') ?>" required>
                        <small class="text-muted" style="font-size:0.72rem;">Default: 0.30 (30%)</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Fairness (wF)</label>
                        <input type="number" step="0.05" min="0" max="1" name="weight_fairness" class="form-control font-mono" value="<?= htmlspecialchars($settings['weight_fairness'] ?? '0.20') ?>" required>
                        <small class="text-muted" style="font-size:0.72rem;">Default: 0.20 (20%)</small>
                    </div>
                </div>

                <h5 class="fw-bold mb-3" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">
                    <i class="bi bi-building me-2" style="color:var(--brand-bronze);"></i>Organization &amp; Operating Hours
                </h5>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Organization / Campus Profile Name</label>
                    <input type="text" name="org_name" class="form-control" value="<?= htmlspecialchars($settings['org_name'] ?? 'RTRAE Campus & Institutional Operations') ?>" required>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Workday Start</label>
                        <input type="time" name="workday_start" class="form-control font-mono" value="<?= htmlspecialchars($settings['workday_start'] ?? '08:00') ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Workday End</label>
                        <input type="time" name="workday_end" class="form-control font-mono" value="<?= htmlspecialchars($settings['workday_end'] ?? '17:00') ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary fw-bold px-4"><i class="bi bi-save me-1"></i> Save System Settings</button>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold mb-3" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">
                    <i class="bi bi-shield-check me-2" style="color:var(--brand-forest);"></i>Backend &amp; AI Environment Diagnostics
                </h5>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent border-bottom">
                        <span>Database Engine</span>
                        <span class="badge" style="background:rgba(49,95,67,0.1);color:#315F43;">MySQL InnoDB (ACID Active)</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent border-bottom">
                        <span>Concurrency Control</span>
                        <span class="badge" style="background:#171B18;color:#FFFDF7;">SELECT ... FOR UPDATE</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent border-bottom">
                        <span>Built-In Webpage Assistant</span>
                        <span class="badge" style="background:rgba(49,95,67,0.1);color:#315F43;">Active (Webpage Knowledge)</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent border-bottom">
                        <span>External Gemini API Key</span>
                        <?php if ($geminiConfigured): ?>
                            <span class="badge" style="background:rgba(49,95,67,0.1);color:#315F43;">Configured</span>
                        <?php else: ?>
                            <span class="badge" style="background:rgba(189,137,87,0.15);color:var(--brand-bronze);">Local Engine Mode (No Key Set)</span>
                        <?php endif; ?>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent border-bottom">
                        <span>Official Support Helpline</span>
                        <a href="tel:7048700224" class="fw-bold font-mono text-decoration-none" style="color:var(--brand-forest);">+91 7048700224</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                        <span>Official Support Email</span>
                        <a href="mailto:25amtics@gmail.com" class="fw-bold font-mono text-decoration-none" style="color:var(--brand-forest);">25amtics@gmail.com</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>