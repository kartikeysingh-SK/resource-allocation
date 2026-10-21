<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $_SESSION['full_name'] ?? 'Guest User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Introduction &amp; Guide | RTRAE Engine</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-forest: #315F43;
            --brand-forest-hover: #234731;
            --brand-charcoal: #171B18;
            --brand-ivory: #F6F3E9;
            --brand-cream: #FFFDF7;
            --brand-sage: #B8CBB1;
            --brand-bronze: #BD8957;
            --brand-text-primary: #252B25;
            --brand-text-muted: #6B7168;
            --brand-border: rgba(49, 95, 67, 0.16);
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--brand-ivory);
            color: var(--brand-text-primary);
        }
        .navbar-editorial {
            background: rgba(246, 243, 233, 0.95);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--brand-border);
            padding: 0.9rem 0;
        }
        .hero-section {
            background: linear-gradient(135deg, #171B18 0%, #234731 55%, #315F43 100%);
            color: #FFFDF7;
            padding: 5rem 0 4.5rem;
            border-bottom-left-radius: 36px;
            border-bottom-right-radius: 36px;
            box-shadow: 0 15px 35px rgba(23, 27, 24, 0.15);
        }
        .editorial-card {
            background: var(--brand-cream);
            border: 1px solid var(--brand-border);
            border-radius: 20px;
            padding: 1.85rem;
            height: 100%;
            box-shadow: 0 10px 25px -5px rgba(23, 27, 24, 0.05);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .editorial-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 35px -8px rgba(49, 95, 67, 0.15);
        }
        .step-badge {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--brand-forest);
            color: #FFFDF7;
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(49, 95, 67, 0.25);
        }
        .formula-banner {
            background: rgba(49, 95, 67, 0.06);
            border: 1px solid var(--brand-border);
            border-radius: 20px;
            padding: 2.25rem;
        }
        .contact-card {
            background: #171B18;
            color: #FFFDF7;
            border-radius: 24px;
            padding: 2.75rem;
            border: 1px solid rgba(49, 95, 67, 0.3);
        }
    </style>
</head>
<body>

<!-- Top Navigation -->
<nav class="navbar-editorial sticky-top shadow-sm">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
        <a href="index.php" class="d-flex align-items-center gap-2.5 text-decoration-none">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-white font-serif fw-bold" style="width:38px;height:38px;background:var(--brand-forest);font-family:'Playfair Display',serif;font-size:1.25rem;">
                R
            </div>
            <div>
                <span class="fw-bold" style="font-family:'Playfair Display',serif;color:var(--brand-charcoal);font-size:1.15rem;">RTRAE Engine</span>
                <span class="badge ms-1.5" style="background:rgba(49,95,67,0.1);color:var(--brand-forest);font-size:0.68rem;border:1px solid rgba(49,95,67,0.25);">Universal Campus Edition</span>
            </div>
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="index.php" class="btn btn-sm btn-outline-secondary fw-semibold">Home</a>
            <?php if ($isLoggedIn): ?>
                <a href="dashboard.php" class="btn btn-sm fw-semibold text-white" style="background:var(--brand-forest);"><i class="bi bi-speedometer2 me-1"></i> Control Center</a>
                <a href="logout.php" class="btn btn-sm btn-outline-danger">Sign Out</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-sm btn-outline-secondary fw-semibold">Sign In</a>
                <a href="register.php" class="btn btn-sm fw-semibold text-white" style="background:var(--brand-forest);">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Hero Banner -->
<section class="hero-section mb-5">
    <div class="container text-center">
        <span class="badge px-3 py-1.5 rounded-pill fw-bold mb-3" style="background:rgba(189,137,87,0.2);color:var(--brand-bronze-light);border:1px solid rgba(189,137,87,0.4);font-size:0.75rem;">
            <i class="bi bi-stars me-1"></i> Universal Multi-Facility Optimization Platform
        </span>
        <h1 class="display-5 fw-bold mb-3" style="font-family:'Playfair Display',serif;letter-spacing:-0.02em;">Real-Time Resource Allocation Engine</h1>
        <p class="lead mx-auto mb-4" style="max-width: 780px; color: #B8CBB1;">
            An intelligent, ACID-compliant optimization platform that matches incoming tasks, lecture bookings, laboratory computing jobs, and fleet dispatch to the optimal resource in real time.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="dashboard.php" class="btn btn-lg fw-bold px-4 shadow text-white" style="background:var(--brand-bronze);border:none;">
                <i class="bi bi-rocket-takeoff-fill me-2"></i>Launch Live Allocation Engine
            </a>
            <a href="#how-to-use" class="btn btn-outline-light btn-lg fw-semibold px-4">
                <i class="bi bi-play-circle me-2"></i>How to Use This System
            </a>
        </div>
    </div>
</section>

<div class="container pb-5">

    <!-- 1. What is it & Why is it Used? -->
    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="editorial-card">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 p-3 mb-3 text-white" style="background:var(--brand-forest);width:48px;height:48px;">
                    <i class="bi bi-question-diamond-fill fs-5"></i>
                </div>
                <h4 class="fw-bold mb-2" style="font-family:'Playfair Display',serif;color:var(--brand-charcoal);">What is the RTRAE Engine?</h4>
                <p class="text-muted small">
                    In universities and modern institutions, coordinators continuously struggle to decide <strong>which classroom</strong> has sufficient capacity and smartboards, <strong>which computing lab</strong> has free GPU slots, or <strong>which faculty member</strong> has available hours.
                </p>
                <p class="text-muted small mb-0">
                    The <strong>Real-Time Resource Allocation Engine</strong> automates this completely. It evaluates resources via a relational SQL schema and multi-factor suitability scoring, committing optimal assignments in milliseconds.
                </p>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="editorial-card">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 p-3 mb-3 text-white" style="background:var(--brand-bronze);width:48px;height:48px;">
                    <i class="bi bi-check2-all fs-5"></i>
                </div>
                <h4 class="fw-bold mb-2" style="font-family:'Playfair Display',serif;color:var(--brand-charcoal);">Why Use This System? (Core Benefits)</h4>
                <ul class="list-unstyled text-muted small mb-0">
                    <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--brand-forest);"></i><strong>Zero Double-Booking:</strong> Enforces MySQL <code>SELECT ... FOR UPDATE</code> row locks inside transactions.</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--brand-forest);"></i><strong>100% Capability Precision:</strong> Ensures tasks are assigned only to rooms or staff matching required equipment.</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--brand-forest);"></i><strong>Prevents Congestion:</strong> Mathematical fairness factor balances load evenly across campus facilities.</li>
                    <li><i class="bi bi-check-circle-fill me-2" style="color:var(--brand-forest);"></i><strong>Live Real-Time Telemetry:</strong> Updates utilization graphs and capacity status dynamically.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- 2. Mathematical Formula Banner -->
    <div class="formula-banner mb-5">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge mb-2" style="background:rgba(49,95,67,0.1);color:var(--brand-forest);border:1px solid rgba(49,95,67,0.25);">Core Mathematical Model</span>
                <h4 class="fw-bold mb-2" style="font-family:'Playfair Display',serif;color:var(--brand-charcoal);">Suitability Scoring Algorithm</h4>
                <p class="text-muted small mb-3">
                    Every candidate resource with sufficient free hours and required capabilities is scored using:
                </p>
                <div class="row g-2 small">
                    <div class="col-md-4">
                        <div class="bg-white p-2.5 rounded-3 border h-100">
                            <strong style="color:var(--brand-forest);" class="d-block">K (Skill Match: 50%)</strong>
                            1.0 if asset possesses required specs; 0 otherwise.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-white p-2.5 rounded-3 border h-100">
                            <strong style="color:var(--brand-bronze);" class="d-block">A (Availability: 30%)</strong>
                            Free Hours divided by Total Capacity.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-white p-2.5 rounded-3 border h-100">
                            <strong style="color:var(--brand-charcoal);" class="d-block">F (Fairness: 20%)</strong>
                            1 - (Active Load / Capacity) to balance work.
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 text-center mt-4 mt-lg-0">
                <div class="bg-white p-4 rounded-4 shadow-sm border" style="border-color:rgba(49,95,67,0.2)!important;">
                    <div class="text-muted small fw-semibold mb-1" style="font-family:'JetBrains Mono',monospace;">WEIGHTED FORMULA</div>
                    <h3 class="fw-bold mb-1" style="font-family:'Playfair Display',serif;color:var(--brand-forest);">S = 0.50K + 0.30A + 0.20F</h3>
                    <span class="badge" style="background:rgba(49,95,67,0.1);color:var(--brand-forest);">Range: 0.00 to 100.00 Points</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Step-by-Step User Guide -->
    <div id="how-to-use" class="mb-5">
        <div class="text-center mb-4">
            <span class="fw-bold text-uppercase small" style="color:var(--brand-forest);letter-spacing:0.08em;">Operating Manual</span>
            <h3 class="fw-bold" style="font-family:'Playfair Display',serif;color:var(--brand-charcoal);">Step-by-Step System Workflow</h3>
            <p class="text-muted small">Follow these simple steps to operate and demonstrate the platform.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="editorial-card d-flex gap-3 align-items-start">
                    <div class="step-badge">1</div>
                    <div>
                        <h6 class="fw-bold mb-1" style="font-family:'Playfair Display',serif;color:var(--brand-charcoal);">Register Account &amp; Sign In</h6>
                        <p class="text-muted small mb-0">
                            Open <code>register.php</code> with password confirmation matching, then log in via <code>login.php</code> to access the live control center.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="editorial-card d-flex gap-3 align-items-start">
                    <div class="step-badge">2</div>
                    <div>
                        <h6 class="fw-bold mb-1" style="font-family:'Playfair Display',serif;color:var(--brand-charcoal);">Inspect Campus Infrastructure</h6>
                        <p class="text-muted small mb-0">
                            Review Classrooms B01–B13 (60–120 seats), Advanced Computing Labs 101–104 (Intel i9, RTX 4080s), and Departmental Wings B–I on the dashboard.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="editorial-card d-flex gap-3 align-items-start">
                    <div class="step-badge">3</div>
                    <div>
                        <h6 class="fw-bold mb-1" style="font-family:'Playfair Display',serif;color:var(--brand-charcoal);">Create Tasks &amp; Booking Requests</h6>
                        <p class="text-muted small mb-0">
                            Click <strong>"+ New Task / Booking"</strong> or type naturally into the <em>Natural Language Allocator</em> bar on the Dashboard.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="editorial-card d-flex gap-3 align-items-start">
                    <div class="step-badge">4</div>
                    <div>
                        <h6 class="fw-bold mb-1" style="font-family:'Playfair Display',serif;color:var(--brand-charcoal);">Automated Conflict-Free Allocation</h6>
                        <p class="text-muted small mb-0">
                            Click the <strong>"Allocate"</strong> button on any pending request. The engine acquires row locks and assigns the optimal resource instantly.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="editorial-card d-flex gap-3 align-items-start">
                    <div class="step-badge">5</div>
                    <div>
                        <h6 class="fw-bold mb-1" style="font-family:'Playfair Display',serif;color:var(--brand-charcoal);">Inspect Hourly Timetable &amp; Audit Logs</h6>
                        <p class="text-muted small mb-0">
                            Navigate to <code>calendar.php</code> for the 9-hour slot matrix, or inspect immutable allocation logs in <code>audit.php</code>.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="editorial-card d-flex gap-3 align-items-start">
                    <div class="step-badge">6</div>
                    <div>
                        <h6 class="fw-bold mb-1" style="font-family:'Playfair Display',serif;color:var(--brand-charcoal);">Release Finished Tasks</h6>
                        <p class="text-muted small mb-0">
                            Click <strong>"Release"</strong> in Active Allocations or inside any calendar slot to restore capacity immediately.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Project Contacts Section -->
    <div class="contact-card">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge mb-2 fw-semibold" style="background:rgba(189,137,87,0.25);color:var(--brand-bronze-light);border:1px solid rgba(189,137,87,0.4);">Operations Support Desk</span>
                <h3 class="fw-bold mb-2" style="font-family:'Playfair Display',serif;">Academic Operations &amp; Resource Center</h3>
                <p class="mb-3" style="color:#B8CBB1;font-size:0.9rem;">
                    Engineering project leads: <strong>Kartikey Singh</strong>, <strong>Adhiraj Jha</strong>, and <strong>Nitin Tiwari</strong>. Direct support for demonstrations, database customization, and scheduling deployment:
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <div class="p-2.5 rounded-3 border" style="background:rgba(255,255,255,0.06);border-color:rgba(255,255,255,0.15)!important;">
                        <small class="d-block" style="color:#B8CBB1;font-size:0.75rem;">Official Helpline</small>
                        <a href="tel:7048700224" class="text-white text-decoration-none fw-bold" style="font-family:'JetBrains Mono',monospace;">
                            <i class="bi bi-telephone-fill me-1" style="color:var(--brand-bronze);"></i> +91 7048700224
                        </a>
                    </div>
                    <div class="p-2.5 rounded-3 border" style="background:rgba(255,255,255,0.06);border-color:rgba(255,255,255,0.15)!important;">
                        <small class="d-block" style="color:#B8CBB1;font-size:0.75rem;">Official Email</small>
                        <a href="mailto:25amtics@gmail.com" class="text-white text-decoration-none fw-bold" style="font-family:'JetBrains Mono',monospace;">
                            <i class="bi bi-envelope-fill me-1" style="color:var(--brand-forest);"></i> 25amtics@gmail.com
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="p-4 rounded-4 text-start" style="background:#FFFDF7;color:#252B25;border:1px solid rgba(49,95,67,0.2);">
                    <h6 class="fw-bold mb-2" style="font-family:'Playfair Display',serif;color:var(--brand-charcoal);">Launch Applications</h6>
                    <p class="text-muted small mb-3">Jump directly to any module of the application:</p>
                    <div class="d-grid gap-2">
                        <a href="dashboard.php" class="btn btn-sm fw-bold text-white" style="background:var(--brand-forest);"><i class="bi bi-speedometer2 me-1"></i> Control Center Dashboard</a>
                        <a href="calendar.php" class="btn btn-sm btn-outline-secondary fw-semibold"><i class="bi bi-calendar3 me-1"></i> Scheduling Timetable</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>