<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth_check.php';

$pageTitle    = 'System Information & Architecture | RTRAE Engine';
$pageSubtitle = 'Architecture & Formula Documentation';
$currentPage  = 'info';
$extraHeadTags = '<style>
    .info-card { background: #FFFDF7 !important; border: 1px solid rgba(49, 95, 67, 0.16) !important; border-radius: 20px !important; box-shadow: 0 10px 30px -5px rgba(23, 27, 24, 0.05); }
    .formula-box { background: rgba(49, 95, 67, 0.06); border-left: 5px solid #315F43; border-radius: 14px; padding: 1.5rem; border: 1px solid rgba(49, 95, 67, 0.15); border-left-width: 5px; }
    .avatar-circle { width: 64px; height: 64px; border-radius: 50%; background: #315F43; color: #FFFDF7; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; font-family: "Playfair Display", serif; font-weight: 700; margin: 0 auto 12px; box-shadow: 0 4px 12px rgba(49,95,67,0.25); }
</style>';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container pb-5 anim-fade-up">
    <!-- Hero Banner -->
    <div class="info-card p-4 p-md-5 mb-4 anim-fade-up delay-1">
        <span class="badge mb-2 px-3 py-1.5 fw-semibold" style="background:rgba(49,95,67,0.1);color:#315F43;border:1px solid rgba(49,95,67,0.25);font-size:0.75rem;">Academic DBMS &amp; Web Engineering Architecture</span>
        <h2 class="fw-bold mb-2" style="font-family:'Playfair Display',serif;color:var(--brand-charcoal);">Real-Time Campus &amp; Facility Resource Allocation Engine</h2>
        <p class="text-muted mb-0" style="max-width: 900px;">
            An automated decision-support and scheduling platform engineered with <strong>PHP 8 (PDO)</strong>, <strong>MySQL 8 (InnoDB Strict Transactions)</strong>, <strong>Tailwind &amp; Bootstrap</strong>, and <strong>Chart.js</strong>. It dynamically schedules classrooms (B01–B13), computer labs (101–104), departmental wings, faculty duties, and university fleet based on multi-variable suitability scoring.
        </p>
    </div>

    <!-- Mathematical Formula & Concurrency Section -->
    <div class="row g-4 mb-4 anim-fade-up delay-2">
        <div class="col-lg-6">
            <div class="info-card p-4 h-100">
                <h5 class="fw-bold mb-3" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">
                    <i class="bi bi-calculator-fill me-2" style="color:var(--brand-forest);"></i>1. Multi-Factor Suitability Algorithm
                </h5>
                <p class="text-muted small">
                    Whenever an administrator clicks <strong>Allocate</strong> or uses natural language scheduling, the engine filters active resources matching requirements, then ranks them using:
                </p>
                <div class="formula-box my-3 text-center">
                    <h4 class="fw-bold mb-1" style="font-family:'Playfair Display',serif; color:var(--brand-forest);">S = 0.50(K) + 0.30(A) + 0.20(F)</h4>
                    <small class="text-muted">Suitability score range: 0.00 to 100.00</small>
                </div>
                <ul class="small text-muted mb-0">
                    <li class="mb-2"><strong>K (Skill &amp; Facility Match — 50% Weight):</strong> Evaluates to <code>100</code> if the facility or person satisfies the required capability (e.g., <em>Classroom B01</em>, <em>GPU Rig</em>, <em>DBMS Practical</em>).</li>
                    <li class="mb-2"><strong>A (Available Capacity Ratio — 30% Weight):</strong> Computed as <code>((Capacity - Active Load) / Capacity) × 100</code>. Prioritizes resources with ample free hours.</li>
                    <li><strong>F (Workload Fairness — 20% Weight):</strong> Computed as <code>(1 - (Active Load / Capacity)) × 100</code>. Balances workloads evenly to prevent facility congestion and staff burnout.</li>
                </ul>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="info-card p-4 h-100">
                <h5 class="fw-bold mb-3" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">
                    <i class="bi bi-shield-lock-fill me-2" style="color:var(--brand-bronze);"></i>2. ACID Transactions &amp; Concurrency Control
                </h5>
                <p class="text-muted small">
                    To eliminate double-booking when multiple coordinators book simultaneously, the backend enforces row-level locking inside InnoDB transactions:
                </p>
                <div class="p-3 rounded-3 small font-mono mb-3" style="background:#171B18;color:#F6F3E9;border:1px solid rgba(49,95,67,0.3);">
                    $pdo-&gt;beginTransaction();<br>
                    SELECT * FROM tasks WHERE id = ? FOR UPDATE;<br>
                    SELECT * FROM resources WHERE status != 'inactive' FOR UPDATE;<br>
                    // Compute suitability S &amp; lock candidate<br>
                    UPDATE resources SET active_load = ...;<br>
                    INSERT INTO allocations ...;<br>
                    $pdo-&gt;commit();
                </div>
                <p class="small text-muted mb-0">
                    Using <code>SELECT ... FOR UPDATE</code> holds row locks until commit, guaranteeing zero race conditions and zero double-booked rooms or personnel.
                </p>
            </div>
        </div>
    </div>

    <!-- Core Faculty & Team Profiles -->
    <h5 class="fw-bold mb-3" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">
        <i class="bi bi-people-fill me-2" style="color:var(--brand-forest);"></i>Featured Campus Faculty &amp; Operations Leads
    </h5>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="info-card p-4 text-center h-100">
                <div class="avatar-circle">KS</div>
                <h5 class="fw-bold mb-1" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">Kartikey Singh</h5>
                <span class="badge mb-2" style="background:rgba(49,95,67,0.1);color:#315F43;border:1px solid rgba(49,95,67,0.25);">Senior Faculty • DBMS &amp; Backend</span>
                <p class="text-muted small mb-0">
                    Database Management Systems, SQL query optimization, and PHP backend architecture. Manages laboratory evaluations and server infrastructure.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card p-4 text-center h-100">
                <div class="avatar-circle" style="background:#BD8957;">AJ</div>
                <h5 class="fw-bold mb-1" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">Adhiraj Jha</h5>
                <span class="badge mb-2" style="background:rgba(189,137,87,0.15);color:var(--brand-bronze);border:1px solid rgba(189,137,87,0.3);">Faculty • Algorithms &amp; Labs</span>
                <p class="text-muted small mb-0">
                    Coordinates Data Structures &amp; Algorithms assessments, Java programming practicals, and Computer Lab 101–104 supervision.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card p-4 text-center h-100">
                <div class="avatar-circle" style="background:#171B18;">NT</div>
                <h5 class="fw-bold mb-1" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">Nitin Tiwari</h5>
                <span class="badge mb-2" style="background:rgba(49,95,67,0.1);color:#315F43;border:1px solid rgba(49,95,67,0.25);">Faculty • Web Tech &amp; UI/UX</span>
                <p class="text-muted small mb-0">
                    Frontend Web Engineering, responsive human-computer interface design, and university examination invigilation coordination.
                </p>
            </div>
        </div>
    </div>

    <!-- Campus Infrastructure Categories -->
    <div class="info-card p-4">
        <h5 class="fw-bold mb-3" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal);">
            <i class="bi bi-building-gear me-2" style="color:var(--brand-bronze);"></i>Managed Campus Infrastructure Matrix
        </h5>
        <div class="row g-3 small">
            <div class="col-md-3">
                <div class="p-3 border rounded-3 h-100" style="background:rgba(49,95,67,0.04);border-color:rgba(49,95,67,0.15)!important;">
                    <strong class="d-block mb-1" style="color:var(--brand-forest);"><i class="bi bi-person-workspace me-1"></i> Faculty / Staff</strong>
                    Tracks daily teaching &amp; evaluation hours (max 8 hrs/day) to ensure balanced duty distribution.
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded-3 h-100" style="background:rgba(49,95,67,0.04);border-color:rgba(49,95,67,0.15)!important;">
                    <strong class="d-block mb-1" style="color:var(--brand-forest);"><i class="bi bi-easel2-fill me-1"></i> Classrooms (B01–B13)</strong>
                    Smartboard and digital projector lecture halls seating 60 to 120 students with climate control.
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded-3 h-100" style="background:rgba(189,137,87,0.06);border-color:rgba(189,137,87,0.2)!important;">
                    <strong class="d-block mb-1" style="color:var(--brand-bronze);"><i class="bi bi-pc-display me-1"></i> Computing Labs (101–104)</strong>
                    64 high-tier rigs per lab with Intel i9 CPUs, 32GB RAM, NVIDIA RTX 4080s, and 10 Gbps fiber.
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded-3 h-100" style="background:rgba(23,27,24,0.04);border-color:rgba(23,27,24,0.15)!important;">
                    <strong class="d-block mb-1" style="color:var(--brand-charcoal);"><i class="bi bi-truck me-1"></i> Fleet &amp; Facilities</strong>
                    Departmental wings B–I, emergency medical van, campus transit vehicles, and guest catering.
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>