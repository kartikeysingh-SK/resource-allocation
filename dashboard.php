<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth_check.php';

$pageTitle    = 'Live Control Center | RTRAE Engine';
$pageSubtitle = 'Real-Time Resource Allocation & Operations';
$currentPage  = 'dashboard';

$extraHeadTags = '
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    /* Control Center Editorial Theme */
    .kpi-card {
        background: #FFFDF7 !important;
        border: 1.5px solid rgba(49, 95, 67, 0.16) !important;
        border-radius: 20px !important;
        box-shadow: 0 10px 28px -6px rgba(23, 27, 24, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.95);
        padding: 1.5rem !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }
    .kpi-card:hover {
        border-color: rgba(49, 95, 67, 0.38) !important;
        transform: translateY(-4px);
        box-shadow: 0 18px 40px -8px rgba(49, 95, 67, 0.14), 0 0 0 1px rgba(49, 95, 67, 0.1);
    }
    .kpi-card::before {
        content: "";
        position: absolute;
        top: 0; left: 12%; right: 12%; height: 2.5px;
        background: linear-gradient(90deg, transparent, rgba(49, 95, 67, 0.5), transparent);
    }
    .kpi-card-warning::before {
        background: linear-gradient(90deg, transparent, rgba(189, 137, 87, 0.6), transparent);
    }
    .kpi-num {
        font-family: "Playfair Display", Georgia, serif !important;
        font-size: 2.5rem !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        color: #171B18 !important;
        letter-spacing: -0.02em;
    }
    .icon-box {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 1.4rem;
        background: rgba(49, 95, 67, 0.09) !important;
        border: 1px solid rgba(49, 95, 67, 0.2);
        color: var(--brand-forest) !important;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }
    .kpi-card:hover .icon-box {
        transform: scale(1.1) rotate(-3deg);
        background: rgba(49, 95, 67, 0.16) !important;
    }
    .icon-box-bronze {
        background: rgba(189, 137, 87, 0.1) !important;
        border-color: rgba(189, 137, 87, 0.25) !important;
        color: var(--brand-bronze) !important;
    }
    .kpi-card:hover .icon-box-bronze {
        background: rgba(189, 137, 87, 0.18) !important;
    }

    /* Natural Language Command Bar */
    .command-bar {
        background: #FFFFFF !important;
        border: 1.5px solid rgba(49, 95, 67, 0.22) !important;
        border-radius: 18px;
        padding: 7px 10px;
        box-shadow: 0 6px 20px rgba(49, 95, 67, 0.05);
        transition: all 0.25s ease;
    }
    .command-bar:focus-within {
        border-color: var(--brand-forest) !important;
        box-shadow: 0 0 0 4px rgba(49, 95, 67, 0.12), 0 10px 30px rgba(49, 95, 67, 0.08);
    }
    .command-bar .form-control {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        font-size: 0.95rem;
        color: #171B18 !important;
    }
    .prompt-example {
        background: rgba(49, 95, 67, 0.06);
        border: 1px solid rgba(49, 95, 67, 0.18);
        color: var(--brand-forest);
        border-radius: 10px;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.4rem 0.8rem;
        transition: all 0.2s ease;
    }
    .prompt-example:hover {
        background: var(--brand-forest);
        color: var(--brand-cream);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(49, 95, 67, 0.2);
    }

    /* Facility Architecture Cards */
    .facility-card {
        background: #FFFDF7;
        border: 1px solid var(--brand-border);
        border-radius: 20px;
        padding: 1.35rem;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 20px -4px rgba(23, 27, 24, 0.04);
    }
    .facility-card:hover {
        border-color: rgba(49, 95, 67, 0.35);
        transform: translateY(-4px);
        box-shadow: 0 16px 36px -6px rgba(49, 95, 67, 0.12);
    }
    .facility-graphic {
        width: 100%;
        height: 110px;
        border-radius: 14px;
        background: linear-gradient(135deg, rgba(184, 203, 177, 0.22), rgba(246, 243, 233, 0.75));
        border: 1px solid rgba(49, 95, 67, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.95rem;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    .facility-card:hover .facility-graphic {
        transform: scale(1.02);
    }
    .facility-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        background: rgba(49, 95, 67, 0.1);
        color: var(--brand-forest);
        border: 1px solid rgba(49, 95, 67, 0.22);
    }

    /* Resource Directory Items */
    .resource-item {
        padding: 15px 16px !important;
        margin-bottom: 12px;
        background: #FFFDF7 !important;
        border: 1.5px solid rgba(49, 95, 67, 0.15) !important;
        border-radius: 16px !important;
        transition: all 0.22s ease;
        box-shadow: 0 3px 10px rgba(23, 27, 24, 0.03);
    }
    .resource-item:hover {
        transform: translateX(4px);
        background: #FFFFFF !important;
        border-color: rgba(49, 95, 67, 0.35) !important;
        box-shadow: 0 6px 20px rgba(49, 95, 67, 0.09);
    }
    .skill-tag {
        padding: 3px 9px;
        margin: 4px 4px 0 0;
        color: #234731 !important;
        background: rgba(49, 95, 67, 0.08);
        border: 1px solid rgba(49, 95, 67, 0.18);
        border-radius: 7px;
        font-size: 0.72rem;
        font-weight: 600;
        display: inline-block;
        transition: all 0.15s ease;
    }
    .skill-tag:hover {
        background: rgba(49, 95, 67, 0.16);
        border-color: rgba(49, 95, 67, 0.3);
    }

    /* Live Operational Ticker Badge */
    .operational-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(49, 95, 67, 0.08);
        border: 1px solid rgba(49, 95, 67, 0.22);
        color: var(--brand-forest);
        font-family: "JetBrains Mono", monospace;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.35rem 0.85rem;
        border-radius: 50px;
        box-shadow: 0 2px 8px rgba(49, 95, 67, 0.05);
    }

    /* Dynamic Spatial Vector HUD Scanner Card */
    .radar-hud-card {
        background: #171F1A !important;
        border: 1.5px solid rgba(49, 95, 67, 0.38) !important;
        border-radius: 20px !important;
        box-shadow: 0 16px 40px -10px rgba(0, 0, 0, 0.28), inset 0 1px 0 rgba(187, 238, 202, 0.12);
        color: #BBECCA;
        position: relative;
        overflow: hidden;
    }
    .radar-hud-card::after {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 50% 50%, rgba(49, 95, 67, 0.18) 0%, transparent 75%);
        pointer-events: none;
    }
    .radar-node-badge {
        font-family: "JetBrains Mono", monospace;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        background: rgba(49, 95, 67, 0.1);
        color: var(--brand-forest);
        border: 1px solid rgba(49, 95, 67, 0.22);
    }
</style>
';

$currentUserJson = json_encode(
    ['name' => $userName, 'role' => $userRole],
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
);

require_once __DIR__ . '/includes/header.php';
?>

<main class="container-fluid px-3 px-md-4 px-xl-5 pb-5" style="max-width: 1680px; margin: 0 auto;">
    <!-- Header Control Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 anim-fade-up">
        <div>
            <div class="mb-2">
                <span class="operational-pill">
                    <span class="live-dot"></span>
                    <span class="fw-bold" style="color:#234731;">LIVE STATUS: NOMINAL</span>
                    <span class="text-muted">/</span>
                    <span class="fw-semibold">CONCURRENCY LOCKING: ACTIVE</span>
                </span>
            </div>
            <h1 class="fw-bold mb-1" style="font-family:'Playfair Display',Georgia,serif; color:#171B18 !important; font-size:1.85rem; letter-spacing:-0.02em;">
                Campus Resource Allocation Command Center
            </h1>
            <p class="small mb-0 fw-semibold" style="color:#4B5563 !important;">Managing Classrooms B01–B03 &amp; B11–B13, Wings B–I, Computer Labs 101–104, Faculty Staff &amp; Logistics.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="calendar.php" class="btn btn-outline-secondary btn-sm fw-semibold px-3"><i class="bi bi-calendar3 me-1"></i> Schedule View</a>
            <button class="btn btn-outline-secondary btn-sm fw-semibold px-3" onclick="exportAllocationsCSV()"><i class="bi bi-download me-1"></i> Export CSV</button>
            <button class="btn btn-outline-primary btn-sm fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#addResourceModal"><i class="bi bi-plus-circle me-1"></i> Add Resource</button>
            <button class="btn btn-primary btn-sm fw-semibold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addTaskModal"><i class="bi bi-lightning-charge-fill me-1"></i> New Task</button>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4 anim-fade-up delay-1">
        <div class="col-md-3 col-sm-6">
            <div class="kpi-card d-flex align-items-center justify-content-between h-100">
                <div>
                    <small class="fw-bold text-uppercase tracking-wider" style="color:#555E54 !important;font-size:0.72rem;">Total Resources</small>
                    <div class="kpi-num mb-0 mt-1" id="kpi-resources" style="color:#171B18 !important;">0</div>
                    <small class="text-success fw-bold d-inline-flex align-items-center gap-1 mt-1" style="font-size:0.75rem;"><i class="bi bi-check2-circle"></i> Active Campus Pool</small>
                </div>
                <div class="icon-box"><i class="bi bi-layers-fill"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="kpi-card kpi-card-warning d-flex align-items-center justify-content-between h-100">
                <div>
                    <small class="fw-bold text-uppercase tracking-wider" style="color:#555E54 !important;font-size:0.72rem;">Pending Queue</small>
                    <div class="kpi-num mb-0 mt-1" id="kpi-pending" style="color:#BD8957 !important;">0</div>
                    <small class="fw-bold d-inline-flex align-items-center gap-1 mt-1" style="color:#BD8957 !important;font-size:0.75rem;"><i class="bi bi-hourglass-split"></i> Awaiting Allocation</small>
                </div>
                <div class="icon-box icon-box-bronze"><i class="bi bi-hourglass-split"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="kpi-card d-flex align-items-center justify-content-between h-100">
                <div>
                    <small class="fw-bold text-uppercase tracking-wider" style="color:#555E54 !important;font-size:0.72rem;">Active Assignments</small>
                    <div class="kpi-num mb-0 mt-1" id="kpi-active" style="color:#315F43 !important;">0</div>
                    <small class="fw-bold d-inline-flex align-items-center gap-1 mt-1" style="color:#315F43 !important;font-size:0.75rem;"><i class="bi bi-check2-all"></i> Allocated Duties</small>
                </div>
                <div class="icon-box"><i class="bi bi-check2-all"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="kpi-card d-flex align-items-center justify-content-between h-100">
                <div>
                    <small class="fw-bold text-uppercase tracking-wider" style="color:#555E54 !important;font-size:0.72rem;">System Utilization</small>
                    <div class="kpi-num mb-0 mt-1" id="kpi-utilization" style="color:#171B18 !important;">0%</div>
                    <small class="fw-bold d-inline-flex align-items-center gap-1 mt-1" style="color:#555E54 !important;font-size:0.75rem;"><i class="bi bi-pie-chart-fill"></i> Capacity Metric</small>
                </div>
                <div class="icon-box icon-box-bronze"><i class="bi bi-pie-chart-fill"></i></div>
            </div>
        </div>
    </div>

    <!-- Natural Language Automated Allocator Section -->
    <section class="glass-card p-4 mb-4 anim-fade-up delay-2">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
            <h5 class="fw-bold mb-0 text-dark" style="font-family:'Playfair Display',serif;">
                <i class="bi bi-stars me-2" style="color:var(--brand-forest);"></i>Natural Language Automated Allocator
            </h5>
            <span class="badge" style="background:rgba(49,95,67,0.1);color:var(--brand-forest);border:1px solid rgba(49,95,67,0.22);font-family:'JetBrains Mono',monospace;">NLP ENGINE V2.5</span>
        </div>
        <p class="text-muted small mb-3">Describe campus allocation requirements in natural language. RTRAE extracts constraints, matches required skills, and allocates the highest-scoring resource automatically.</p>
        <div class="command-bar d-flex align-items-center">
            <i class="bi bi-terminal fs-5 ms-2" style="color:var(--brand-forest) !important;"></i>
            <input type="text" id="nlPromptInput" class="form-control fw-semibold" placeholder="e.g. I need Classroom B01 for 60 students from 2:00–4:00 PM or a computer lab" aria-label="Describe resource requirement">
            <button class="btn btn-primary fw-bold px-4 ms-2 shadow-sm" onclick="submitNaturalLanguagePrompt()"><i class="bi bi-magic me-1"></i> Auto-Allocate</button>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
            <small class="text-muted fw-semibold me-1">Quick Prompts:</small>
            <button class="btn btn-sm prompt-example" data-prompt="I need Classroom B01 for 60 students"><i class="bi bi-easel me-1"></i>Classroom B01 for 60 students</button>
            <button class="btn btn-sm prompt-example" data-prompt="I need a computer lab for 90 students practical exam"><i class="bi bi-pc-display me-1"></i>Computer lab for 90 students</button>
            <button class="btn btn-sm prompt-example" data-prompt="I need a vehicle for campus transport"><i class="bi bi-truck me-1"></i>Campus transport vehicle</button>
            <button class="btn btn-sm prompt-example" data-prompt="I need catering service for an event"><i class="bi bi-cup-hot me-1"></i>Event catering service</button>
        </div>
    </section>

    <!-- Campus Infrastructure & Facilities Deep Dive (Classrooms, Labs, Wings) -->
    <section class="glass-card p-4 mb-4 anim-fade-up delay-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark" style="font-family:'Playfair Display',serif;">
                    <i class="bi bi-building-fill-gear me-2" style="color:var(--brand-forest);"></i>Campus Facilities &amp; Laboratory Infrastructure
                </h5>
                <p class="text-muted small mb-0">Hardware specifications, seating capacities, and equipment profiles of managed campus resources</p>
            </div>
            <button class="btn btn-outline-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#facilitySpecsModal">
                <i class="bi bi-info-circle me-1"></i> View Full Architecture Specs
            </button>
        </div>

        <div class="row g-3">
            <!-- Classrooms B01–B03 & B11–B13 Card -->
            <div class="col-lg-3 col-md-6">
                <div class="facility-card h-100">
                    <div class="facility-graphic">
                        <!-- High-End SVG Schematic for Classrooms -->
                        <svg width="80" height="80" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="15" y="20" width="70" height="45" rx="6" stroke="#315F43" stroke-width="2.5" fill="rgba(49,95,67,0.06)"/>
                            <rect x="25" y="28" width="50" height="28" rx="3" stroke="#BD8957" stroke-width="1.5" stroke-dasharray="3 3"/>
                            <circle cx="50" cy="42" r="5" fill="#315F43"/>
                            <path d="M35 75L50 65L65 75" stroke="#315F43" stroke-width="2.5" stroke-linecap="round"/>
                            <line x1="20" y1="88" x2="80" y2="88" stroke="rgba(49,95,67,0.2)" stroke-width="2"/>
                            <circle cx="30" cy="84" r="3" fill="#315F43"/>
                            <circle cx="50" cy="84" r="3" fill="#315F43"/>
                            <circle cx="70" cy="84" r="3" fill="#315F43"/>
                        </svg>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="facility-badge">Smart Lecture</span>
                        <small class="text-success fw-bold" style="font-size:0.7rem;"><i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i>Active Pool</small>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Classrooms B01–B13</h6>
                    <p class="text-muted small mb-2" style="font-size:0.77rem;">60–120 student lecture halls equipped with 4K dual smartboards, ceiling projectors, and centralized AC.</p>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="skill-tag">B01–B03</span>
                        <span class="skill-tag">B11–B13</span>
                        <span class="skill-tag">Smartboard</span>
                        <span class="skill-tag">Dual AC</span>
                    </div>
                </div>
            </div>

            <!-- Advanced Computing Labs 101–104 Card -->
            <div class="col-lg-3 col-md-6">
                <div class="facility-card h-100">
                    <div class="facility-graphic">
                        <!-- High-End SVG Schematic for Computing Labs -->
                        <svg width="80" height="80" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="20" y="25" width="60" height="38" rx="5" stroke="#BD8957" stroke-width="2.5" fill="rgba(189,137,87,0.08)"/>
                            <rect x="28" y="32" width="44" height="24" rx="2" fill="rgba(49,95,67,0.12)"/>
                            <path d="M42 63L36 78H64L58 63" stroke="#BD8957" stroke-width="2" stroke-linecap="round"/>
                            <line x1="28" y1="84" x2="72" y2="84" stroke="#BD8957" stroke-width="3" stroke-linecap="round"/>
                            <circle cx="50" cy="44" r="4" fill="#315F43"/>
                            <path d="M34 44H42M58 44H66" stroke="#315F43" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="facility-badge" style="background:rgba(189,137,87,0.12);color:var(--brand-bronze);border-color:rgba(189,137,87,0.25);">High Compute</span>
                        <small class="text-success fw-bold" style="font-size:0.7rem;"><i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i>64 Rigs / Lab</small>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Computer Labs 101–104</h6>
                    <p class="text-muted small mb-2" style="font-size:0.77rem;">64 i9 rigs with 32GB RAM, NVIDIA RTX GPUs, 10Gbps fiber, and uninterrupted 4hr industrial UPS backup.</p>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="skill-tag">RTX GPUs</span>
                        <span class="skill-tag">Python &amp; Docker</span>
                        <span class="skill-tag">10G Fiber</span>
                    </div>
                </div>
            </div>

            <!-- Departmental Wings B–I Card -->
            <div class="col-lg-3 col-md-6">
                <div class="facility-card h-100">
                    <div class="facility-graphic">
                        <!-- High-End SVG Schematic for Wings -->
                        <svg width="80" height="80" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="22" y="20" width="24" height="60" rx="3" stroke="#315F43" stroke-width="2" fill="rgba(49,95,67,0.06)"/>
                            <rect x="54" y="20" width="24" height="60" rx="3" stroke="#315F43" stroke-width="2" fill="rgba(49,95,67,0.06)"/>
                            <line x1="46" y1="40" x2="54" y2="40" stroke="#315F43" stroke-width="2"/>
                            <line x1="46" y1="60" x2="54" y2="60" stroke="#315F43" stroke-width="2"/>
                            <rect x="27" y="28" width="6" height="8" rx="1" fill="#BD8957"/>
                            <rect x="36" y="28" width="6" height="8" rx="1" fill="#BD8957"/>
                            <rect x="59" y="28" width="6" height="8" rx="1" fill="#BD8957"/>
                            <rect x="68" y="28" width="6" height="8" rx="1" fill="#BD8957"/>
                        </svg>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="facility-badge">Academic Wings</span>
                        <small class="text-muted fw-bold" style="font-size:0.7rem;">Wings B to I</small>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Academic Wings B–I</h6>
                    <p class="text-muted small mb-2" style="font-size:0.77rem;">Multi-story faculty suites, departmental committee meeting chambers, and specialized tutorial rooms.</p>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="skill-tag">Faculty Cabins</span>
                        <span class="skill-tag">Tutorial Rooms</span>
                        <span class="skill-tag">Briefing</span>
                    </div>
                </div>
            </div>

            <!-- Fleet, Servers & Catering Card -->
            <div class="col-lg-3 col-md-6">
                <div class="facility-card h-100">
                    <div class="facility-graphic">
                        <!-- High-End SVG Schematic for Fleet & Infrastructure -->
                        <svg width="80" height="80" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="18" y="30" width="46" height="30" rx="4" stroke="#BD8957" stroke-width="2" fill="rgba(189,137,87,0.06)"/>
                            <path d="M64 42H76L82 52V60H64V42Z" stroke="#BD8957" stroke-width="2" fill="rgba(189,137,87,0.06)"/>
                            <circle cx="32" cy="62" r="7" stroke="#315F43" stroke-width="2" fill="#F6F3E9"/>
                            <circle cx="72" cy="62" r="7" stroke="#315F43" stroke-width="2" fill="#F6F3E9"/>
                            <path d="M41 40V50M36 45H46" stroke="#315F43" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="facility-badge" style="background:rgba(189,137,87,0.12);color:var(--brand-bronze);border-color:rgba(189,137,87,0.25);">Fleet &amp; Logistics</span>
                        <small class="fw-bold" style="color:var(--brand-bronze);font-size:0.7rem;">On-Call Duty</small>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Fleet &amp; Service Logistics</h6>
                    <p class="text-muted small mb-2" style="font-size:0.77rem;">Emergency Medical Unit #4, transit vans, cloud nodes, and scheduled event banquet catering quotas.</p>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="skill-tag">Emergency Van</span>
                        <span class="skill-tag">GPU Nodes</span>
                        <span class="skill-tag">Hospitality</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Real-Time Dynamic Spatial Vector HUD Radar & Federated Mesh Section -->
    <div class="row g-4 mb-4 anim-fade-up delay-3">
        <!-- Radar Vector Reticle Panel (From User Specification) -->
        <div class="col-lg-5 col-xl-4">
            <div class="radar-hud-card p-3 h-100 d-flex flex-column justify-content-between">
                <!-- Top Title Bar -->
                <div class="d-flex justify-content-between align-items-center pb-2 mb-2" style="border-bottom: 1px solid rgba(187, 238, 202, 0.2);">
                    <span class="text-uppercase fw-bold" style="font-family: 'JetBrains Mono', monospace; font-size: 0.72rem; letter-spacing: 0.12em; color: #BBECCA;">
                        <i class="bi bi-radar me-1"></i> Dynamic Spatial Vector HUD
                    </span>
                    <span id="radar-azimuth" style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #B9CCB2;">AZIMUTH: 184.22°</span>
                </div>

                <!-- Center Radar Reticle SVG -->
                <div class="position-relative w-100 my-auto d-flex align-items-center justify-content-center" style="max-width: 320px; aspect-ratio: 1; margin: 0 auto;">
                    <svg class="w-100 h-100" viewBox="0 0 300 300" style="color: rgba(187, 238, 202, 0.4);">
                        <!-- Concentric rings -->
                        <circle cx="150" cy="150" fill="none" r="130" stroke="currentColor" stroke-dasharray="3 3" stroke-width="0.75"></circle>
                        <circle cx="150" cy="150" fill="none" r="95" stroke="currentColor" stroke-width="0.75"></circle>
                        <circle cx="150" cy="150" fill="none" r="60" stroke="currentColor" stroke-dasharray="4 2" stroke-width="1"></circle>
                        <circle cx="150" cy="150" fill="none" r="25" stroke="currentColor" stroke-width="1"></circle>
                        <!-- Crosshairs -->
                        <line stroke="currentColor" stroke-width="0.5" x1="150" x2="150" y1="10" y2="290"></line>
                        <line stroke="currentColor" stroke-width="0.5" x1="10" x2="290" y1="150" y2="150"></line>
                        <!-- Diagonal rays -->
                        <line stroke="currentColor" stroke-dasharray="2 4" stroke-width="0.3" x1="50" x2="250" y1="50" y2="250"></line>
                        <line stroke="currentColor" stroke-dasharray="2 4" stroke-width="0.3" x1="50" x2="250" y1="250" y2="50"></line>
                        <!-- Node anchors -->
                        <circle cx="150" cy="70" fill="#a0d2af" r="4">
                            <animate attributeName="r" values="3.5;4.5;3.5" dur="2s" repeatCount="indefinite" />
                        </circle>
                        <text fill="#a0d2af" font-family="'JetBrains Mono', monospace" font-size="8" font-weight="600" x="158" y="74">WING-B</text>
                        <circle cx="220" cy="120" fill="#fec38c" r="3.5">
                            <animate attributeName="r" values="3;4.5;3" dur="2.5s" repeatCount="indefinite" />
                        </circle>
                        <text fill="#fec38c" font-family="'JetBrains Mono', monospace" font-size="8" font-weight="600" x="228" y="124">HPC-A1</text>
                        <circle cx="85" cy="190" fill="#a0d2af" r="3.5">
                            <animate attributeName="r" values="3;4.5;3" dur="1.8s" repeatCount="indefinite" />
                        </circle>
                        <text fill="#a0d2af" font-family="'JetBrains Mono', monospace" font-size="8" font-weight="600" x="38" y="195">AUD-C101</text>
                        <circle cx="195" cy="210" fill="#a0d2af" r="4">
                            <animate attributeName="r" values="3.5;5;3.5" dur="2.2s" repeatCount="indefinite" />
                        </circle>
                        <text fill="#a0d2af" font-family="'JetBrains Mono', monospace" font-size="8" font-weight="600" x="202" y="218">BIO-LAB</text>
                        <circle cx="110" cy="100" fill="#a0d2af" r="3">
                            <animate attributeName="r" values="2.5;4;2.5" dur="3s" repeatCount="indefinite" />
                        </circle>
                        <text fill="#a0d2af" font-family="'JetBrains Mono', monospace" font-size="8" font-weight="600" x="66" y="98">CLIN-04</text>
                        <!-- Active Scanning Sweep Line with Indefinite Rotation -->
                        <line stroke="#bbeeca" stroke-width="1.5" x1="150" x2="270" y1="150" y2="100" stroke-linecap="round">
                            <animateTransform attributeName="transform" type="rotate" from="0 150 150" to="360 150 150" dur="8s" repeatCount="indefinite"></animateTransform>
                        </line>
                    </svg>
                </div>

                <!-- Telemetry Footer Grid -->
                <div class="pt-2 mt-2" style="border-top: 1px solid rgba(187, 238, 202, 0.2); font-family: 'JetBrains Mono', monospace; font-size: 0.72rem; color: #DEE4DB;">
                    <div class="row g-1">
                        <div class="col-6">MESH NODES: <span style="color:#A0D2AF; font-weight:600;">14 ONLINE</span></div>
                        <div class="col-6 text-end">DRIFT: <span style="color:#A0D2AF; font-weight:600;">0.003%</span></div>
                        <div class="col-6">INSPECTION: <span style="color:#FEC38C; font-weight:600;">QUAD-VECTOR</span></div>
                        <div class="col-6 text-end">QUORUM: <span style="color:#A0D2AF; font-weight:600;">3/3 SYNC</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-Time Radar Node Diagnostics & Spatial Quorum Status -->
        <div class="col-lg-7 col-xl-8">
            <section class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark" style="font-family:'Playfair Display',serif;">
                                <i class="bi bi-geo-alt-fill me-2" style="color:var(--brand-forest);"></i>Spatial Vector Radar &amp; Federated Nodes
                            </h5>
                            <p class="text-muted small mb-0">Live spatial coordinates, sub-5ms heartbeat latencies, and ACID lock attestations across campus sectors</p>
                        </div>
                        <span class="badge" style="background:rgba(49,95,67,0.1);color:var(--brand-forest);border:1px solid rgba(49,95,67,0.22);font-family:'JetBrains Mono',monospace;">
                            <i class="bi bi-broadcast me-1"></i> RAFT QUORUM: 3/3 SYNC
                        </span>
                    </div>

                    <!-- 5 Monitored Radar Nodes Table/Grid -->
                    <div class="table-responsive mt-3">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead>
                                <tr class="text-uppercase" style="font-family:'JetBrains Mono',monospace; font-size:0.7rem; color:#6B7168;">
                                    <th>Vector Target</th>
                                    <th>Physical Node</th>
                                    <th>Sector Domain</th>
                                    <th>Deliberation</th>
                                    <th>Capacity State</th>
                                    <th class="text-end">ACID Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="radar-node-badge" style="background:rgba(49,95,67,0.12);color:var(--brand-forest);"><i class="bi bi-dot"></i>WING-B</span></td>
                                    <td class="fw-semibold text-dark">Classrooms B01–B03</td>
                                    <td><span class="badge bg-light text-dark border">Wing B</span></td>
                                    <td style="font-family:'JetBrains Mono',monospace; color:var(--brand-forest); font-weight:600;">1.2ms</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 5px;">
                                                <div class="progress-bar" style="width: 92%; background-color: var(--brand-forest);"></div>
                                            </div>
                                            <span style="font-size:0.72rem; font-family:'JetBrains Mono',monospace;">92%</span>
                                        </div>
                                    </td>
                                    <td class="text-end"><span class="badge" style="background:rgba(49,95,67,0.12);color:var(--brand-forest);font-family:'JetBrains Mono',monospace;">ACTIVE LEASE</span></td>
                                </tr>
                                <tr>
                                    <td><span class="radar-node-badge" style="background:rgba(189,137,87,0.14);color:var(--brand-bronze);"><i class="bi bi-dot"></i>HPC-A1</span></td>
                                    <td class="fw-semibold text-dark">Computer Labs 101–104</td>
                                    <td><span class="badge bg-light text-dark border">HPC Mesh</span></td>
                                    <td style="font-family:'JetBrains Mono',monospace; color:var(--brand-forest); font-weight:600;">0.8ms</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 5px;">
                                                <div class="progress-bar" style="width: 98%; background-color: var(--brand-bronze);"></div>
                                            </div>
                                            <span style="font-size:0.72rem; font-family:'JetBrains Mono',monospace;">98%</span>
                                        </div>
                                    </td>
                                    <td class="text-end"><span class="badge" style="background:rgba(189,137,87,0.15);color:var(--brand-bronze);font-family:'JetBrains Mono',monospace;">HIGH LOAD</span></td>
                                </tr>
                                <tr>
                                    <td><span class="radar-node-badge" style="background:rgba(49,95,67,0.12);color:var(--brand-forest);"><i class="bi bi-dot"></i>AUD-C101</span></td>
                                    <td class="fw-semibold text-dark">Auditorium &amp; Summit Hall</td>
                                    <td><span class="badge bg-light text-dark border">Facility</span></td>
                                    <td style="font-family:'JetBrains Mono',monospace; color:var(--brand-forest); font-weight:600;">1.4ms</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 5px;">
                                                <div class="progress-bar" style="width: 100%; background-color: var(--brand-charcoal);"></div>
                                            </div>
                                            <span style="font-size:0.72rem; font-family:'JetBrains Mono',monospace;">100%</span>
                                        </div>
                                    </td>
                                    <td class="text-end"><span class="badge bg-dark text-light" style="font-family:'JetBrains Mono',monospace;">ROW-LOCKED</span></td>
                                </tr>
                                <tr>
                                    <td><span class="radar-node-badge" style="background:rgba(49,95,67,0.12);color:var(--brand-forest);"><i class="bi bi-dot"></i>BIO-LAB</span></td>
                                    <td class="fw-semibold text-dark">Science &amp; Wet Lab 02</td>
                                    <td><span class="badge bg-light text-dark border">Lab-X</span></td>
                                    <td style="font-family:'JetBrains Mono',monospace; color:var(--brand-forest); font-weight:600;">1.9ms</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 5px;">
                                                <div class="progress-bar" style="width: 45%; background-color: var(--brand-forest);"></div>
                                            </div>
                                            <span style="font-size:0.72rem; font-family:'JetBrains Mono',monospace;">45%</span>
                                        </div>
                                    </td>
                                    <td class="text-end"><span class="badge" style="background:rgba(49,95,67,0.12);color:var(--brand-forest);font-family:'JetBrains Mono',monospace;">SCHEDULED</span></td>
                                </tr>
                                <tr>
                                    <td><span class="radar-node-badge" style="background:rgba(49,95,67,0.12);color:var(--brand-forest);"><i class="bi bi-dot"></i>CLIN-04</span></td>
                                    <td class="fw-semibold text-dark">Clinical Care Suite 04</td>
                                    <td><span class="badge bg-light text-dark border">Clinical</span></td>
                                    <td style="font-family:'JetBrains Mono',monospace; color:var(--brand-forest); font-weight:600;">1.1ms</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 5px;">
                                                <div class="progress-bar" style="width: 25%; background-color: var(--brand-forest);"></div>
                                            </div>
                                            <span style="font-size:0.72rem; font-family:'JetBrains Mono',monospace;">25%</span>
                                        </div>
                                    </td>
                                    <td class="text-end"><span class="badge" style="background:rgba(49,95,67,0.12);color:var(--brand-forest);font-family:'JetBrains Mono',monospace;">NOMINAL</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Summary Metric -->
                <div class="pt-3 mt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2" style="font-family:'JetBrains Mono',monospace; font-size:0.75rem;">
                    <span class="text-muted"><i class="bi bi-shield-check text-success me-1"></i>NODE DRIFT: <strong>0.003%</strong> (TOLERANCE: &lt;0.05%)</span>
                    <span style="color:var(--brand-forest); font-weight:600;"><i class="bi bi-cpu me-1"></i>AGGREGATE OBJECTIVE: S = 0.964</span>
                </div>
            </section>
        </div>
    </div>

    <!-- Workload Visualizer & Active Assignments Grid -->
    <div class="row g-4 mb-4 anim-fade-up delay-4">
        <div class="col-lg-7">
            <section class="glass-card p-4 h-100">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark" style="font-family:'Playfair Display',serif;">
                            <i class="bi bi-bar-chart-fill me-2" style="color:var(--brand-forest);"></i>Workload vs Capacity Telemetry
                        </h6>
                        <small class="text-muted">Live resource load in active operational hours</small>
                    </div>
                    <span class="badge" style="background:rgba(49,95,67,0.1);color:var(--brand-forest);border:1px solid rgba(49,95,67,0.22);font-family:'JetBrains Mono',monospace;">LIVE TELEMETRY</span>
                </div>
                <div style="height:300px;position:relative"><canvas id="workloadChart" aria-label="Workload versus capacity chart"></canvas></div>
            </section>
        </div>
        <div class="col-lg-5">
            <section class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark" style="font-family:'Playfair Display',serif;">
                            <i class="bi bi-lightning-fill me-2" style="color:var(--brand-forest);"></i>Active Assignments
                        </h6>
                        <small class="text-muted">Currently locked jobs &amp; duties</small>
                    </div>
                    <span class="badge bg-success" id="allocation-count">0</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th>Task Title</th>
                                <th>Assigned To</th>
                                <th>Score</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="allocation-table-body">
                            <tr><td colspan="4" class="text-center text-muted py-4">Loading active allocations…</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <!-- Full-Width Campus Resource Directory & Asset Inventory -->
    <div class="row g-4 mb-4 anim-fade-up delay-5">
        <div class="col-12">
            <section class="glass-card p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3 pb-3" style="border-bottom:1px solid rgba(49,95,67,0.12);">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="fw-bold mb-0 text-dark" style="font-family:'Playfair Display',serif;">
                                <i class="bi bi-collection-fill me-2" style="color:var(--brand-forest);"></i>Campus Resource Directory &amp; Capacity Inventory
                            </h5>
                            <span class="badge" id="res-badge-count" style="background:rgba(49,95,67,0.1);color:var(--brand-forest);border:1px solid rgba(49,95,67,0.22);font-family:'JetBrains Mono',monospace;">0 RESOURCES</span>
                        </div>
                        <p class="text-muted small mb-0">Real-time status, skill taxonomy, and operational workloads across all campus facilities, IT servers, and departments.</p>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <input type="search" id="resourceSearch" class="form-control form-control-sm" style="min-width:210px;" placeholder="Search resource, room, skill…" oninput="renderResources()" aria-label="Search resources">
                        <select id="categoryFilter" class="form-select form-select-sm w-auto fw-semibold" onchange="renderResources()" aria-label="Filter resource category">
                            <option value="ALL">All Campus Sectors</option>
                            <option value="Human / Staff">Human / Staff</option>
                            <option value="Room / Facility">Room / Facility (Classrooms, Wings)</option>
                            <option value="IT / Cloud Server">IT / Computing Labs</option>
                            <option value="Equipment / Fleet">Fleet &amp; Equipment</option>
                            <option value="Service / Catering">Catering &amp; Logistics</option>
                        </select>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Directory View Mode">
                            <button type="button" class="btn btn-outline-secondary active" id="btnViewTable" onclick="setResourceView('table')" title="Table View">
                                <i class="bi bi-table me-1"></i> Table
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btnViewGrid" onclick="setResourceView('grid')" title="Card Grid View">
                                <i class="bi bi-grid-3x3-gap me-1"></i> Cards
                            </button>
                        </div>
                        <button class="btn btn-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#addResourceModal">
                            <i class="bi bi-plus-circle me-1"></i> Add Resource
                        </button>
                    </div>
                </div>

                <!-- Table View Container (Full width) -->
                <div id="resource-table-view" class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-uppercase" style="font-family:'JetBrains Mono',monospace;font-size:0.72rem;letter-spacing:0.04em;">
                                <th style="min-width:210px;">Resource Name</th>
                                <th>Sector Domain</th>
                                <th style="min-width:220px;">Capabilities &amp; Features</th>
                                <th>Capacity</th>
                                <th>Active Load</th>
                                <th style="min-width:180px;">Workload Utilization</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody id="resource-table-body">
                            <tr><td colspan="7" class="text-center text-muted py-4">Loading resources…</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Grid View Container (Full width 3/4 cols) -->
                <div id="resource-grid-view" class="row row-cols-1 row-cols-md-2 row-cols-xl-3 row-cols-xxl-4 g-3 d-none">
                    <div class="col-12 text-center text-muted py-4">Loading resources…</div>
                </div>
            </section>
        </div>
    </div>

    <!-- Full-Width Task & Request Queue Row -->
    <div class="row g-4 anim-fade-up delay-5">
        <div class="col-12">
            <section class="glass-card p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 pb-3" style="border-bottom:1px solid rgba(49,95,67,0.12);">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="fw-bold mb-0 text-dark" style="font-family:'Playfair Display',serif;">
                                <i class="bi bi-list-task me-2" style="color:var(--brand-forest);"></i>Task &amp; Request Queue
                            </h5>
                            <span class="badge" id="task-badge-count" style="background:rgba(189,137,87,0.12);color:var(--brand-bronze);border:1px solid rgba(189,137,87,0.25);font-family:'JetBrains Mono',monospace;">0 IN QUEUE</span>
                        </div>
                        <p class="text-muted small mb-0">Incoming academic requests, exam sessions, facility bookings, and algorithm-matched assignments.</p>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <input type="search" id="taskSearch" class="form-control form-control-sm" style="min-width:220px;" placeholder="Search task or skill…" oninput="renderTasks()" aria-label="Search tasks">
                        <button class="btn btn-primary btn-sm fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                            <i class="bi bi-lightning-charge-fill me-1"></i> New Task
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-uppercase" style="font-family:'JetBrains Mono',monospace;font-size:0.72rem;letter-spacing:0.04em;">
                                <th>Task Title</th>
                                <th>Skill / Need</th>
                                <th>Priority</th>
                                <th>Effort</th>
                                <th>Status</th>
                                <th class="text-end">Execution</th>
                            </tr>
                        </thead>
                        <tbody id="task-table-body">
                            <tr><td colspan="6" class="text-center text-muted py-4">Loading tasks…</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</main>

<!-- Detailed Facility Specs Modal -->
<div class="modal fade" id="facilitySpecsModal" tabindex="-1" aria-labelledby="facilitySpecsTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <div class="small fw-bold" style="color:var(--brand-forest);letter-spacing:0.04em;">CAMPUS INFRASTRUCTURE ARCHITECTURE</div>
                    <h5 class="modal-title fw-bold text-dark" id="facilitySpecsTitle" style="font-family:'Playfair Display',serif;">Classrooms &amp; Laboratories Blueprint</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body small">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:var(--brand-cream);border:1px solid var(--brand-border);">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-easel2-fill me-2" style="color:var(--brand-forest);"></i>Classrooms B01–B03 &amp; B11–B13</h6>
                            <ul class="list-unstyled mb-0 text-muted">
                                <li class="mb-1">• <strong>Seating Layout</strong>: Tiered theater seating (60 to 120 students capacity).</li>
                                <li class="mb-1">• <strong>Display Tech</strong>: Dual 86" 4K Interactive Touch Panels + motorized screen.</li>
                                <li class="mb-1">• <strong>Audio/Video</strong>: Ceiling microphone array + wireless lapel + HD presenter cam.</li>
                                <li class="mb-1">• <strong>Environment</strong>: Automated inverter HVAC maintaining 22°C.</li>
                                <li>• <strong>Scheduling Rule</strong>: Max 8 hours/day with automatic 10-minute turnaround gap.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:var(--brand-cream);border:1px solid var(--brand-border);">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-pc-display me-2" style="color:var(--brand-bronze);"></i>Computing Labs 101–104</h6>
                            <ul class="list-unstyled mb-0 text-muted">
                                <li class="mb-1">• <strong>Workstations</strong>: 64 systems per lab (Intel Core i9, 32GB RAM, 1TB NVMe).</li>
                                <li class="mb-1">• <strong>GPU Hardware</strong>: NVIDIA RTX 4080 (16GB GDDR6X) for AI &amp; graphics.</li>
                                <li class="mb-1">• <strong>Network</strong>: Dual redundant 10 Gbps fiber backbones with low latency.</li>
                                <li class="mb-1">• <strong>Power Backup</strong>: 4-Hour industrial online UPS with generator sync.</li>
                                <li>• <strong>Stack</strong>: Python 3.12, PyTorch, Docker, Apache, MySQL 8, VS Code.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:var(--brand-cream);border:1px solid var(--brand-border);">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-building me-2" style="color:var(--brand-forest);"></i>Academic Wings B through I</h6>
                            <p class="text-muted mb-0">Contains dedicated faculty research suites, HOD chambers, departmental tutorial rooms, and centralized seminar auditoriums (capacity 250+).</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:var(--brand-cream);border:1px solid var(--brand-border);">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-shield-check me-2" style="color:var(--brand-bronze);"></i>Allocation Engine Safeguards</h6>
                            <p class="text-muted mb-0">InnoDB row-level locking (<code>SELECT ... FOR UPDATE</code>) completely prevents double-booking and enforces strict workload fairness limits.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close Specification</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Resource Modal -->
<div class="modal fade" id="addResourceModal" tabindex="-1" aria-labelledby="addResourceTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" onsubmit="submitNewResource(event)">
            <div class="modal-header">
                <div>
                    <div class="small fw-bold" style="color:var(--brand-forest);">RESOURCE INVENTORY</div>
                    <h5 class="modal-title fw-bold text-dark" id="addResourceTitle" style="font-family:'Playfair Display',serif;">Register New Resource</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold" for="res_name">Resource Name</label>
                    <input type="text" id="res_name" class="form-control" placeholder="e.g. Classroom B02, Lab 102, or Faculty Name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold" for="res_category">Campus Sector</label>
                    <select id="res_category" class="form-select">
                        <option value="Room / Facility">Room / Facility (Classroom B01-B13, Wings B-I)</option>
                        <option value="Human / Staff">Human / Staff (Faculty, Staff, Duty)</option>
                        <option value="IT / Cloud Server">IT / Computing Labs (Labs 101-104)</option>
                        <option value="Equipment / Fleet">Equipment / Fleet (Transit, Medical Van)</option>
                        <option value="Service / Catering">Service / Catering (Event Hospitality)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold" for="res_capacity">Daily Capacity (Hours)</label>
                    <input type="number" min="0.5" step="0.5" id="res_capacity" class="form-control" value="8" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold" for="res_skills">Capabilities / Feature Tags (comma-separated)</label>
                    <input type="text" id="res_skills" class="form-control" placeholder="e.g. Smartboard, Projector, 4K Display, AC" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="bi bi-check2-circle me-1"></i> Save Resource</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" onsubmit="submitNewTask(event)">
            <div class="modal-header">
                <div>
                    <div class="small fw-bold" style="color:var(--brand-forest);">QUEUE MANAGEMENT</div>
                    <h5 class="modal-title fw-bold text-dark" id="addTaskTitle" style="font-family:'Playfair Display',serif;">Create New Allocation Task</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold" for="task_title">Task Title</label>
                    <input type="text" id="task_title" class="form-control" placeholder="e.g. B.Tech Practical Exam or Faculty Seminar" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold" for="task_skill">Required Capability / Facility Tag</label>
                    <input type="text" id="task_skill" class="form-control" placeholder="e.g. Smartboard, Computer Lab, Food, Transport" required>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-semibold" for="task_priority">Priority Level</label>
                        <select id="task_priority" class="form-select">
                            <option value="5">P5 (Critical / Exam / VIP)</option>
                            <option value="4">P4 (High)</option>
                            <option value="3" selected>P3 (Standard)</option>
                            <option value="1">P1 (Low)</option>
                        </select>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-semibold" for="task_effort">Duration / Effort (Hours)</label>
                        <input type="number" min="0.5" step="0.5" id="task_effort" class="form-control" value="2" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="bi bi-plus-circle me-1"></i> Enqueue Task</button>
            </div>
        </form>
    </div>
</div>

<!-- Live Notification Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
    <div id="liveToast" class="toast align-items-center border-0 shadow-lg" role="status" aria-live="polite" aria-atomic="true" style="background:#FFFDF7;border:1px solid rgba(49,95,67,0.25)!important;color:var(--brand-text-primary);border-radius:16px;">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2 fw-semibold" id="toastMsg" style="font-size:0.86rem;">
                <i class="bi bi-info-circle-fill text-primary"></i> Ready.
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
'use strict';
const CURRENT_USER = <?= $currentUserJson ?: '{"name":"User","role":"Admin"}' ?>;
let workloadChart = null;
let latestDashboardData = {resources:[], tasks:[], allocations:[]};
let dashboardRequestInFlight = false;

function showToast(msg) {
    const el = document.getElementById('toastMsg');
    if (el) el.innerHTML = `<i class="bi bi-info-circle-fill" style="color:var(--brand-forest);"></i> ${escapeHtml(msg)}`;
    bootstrap.Toast.getOrCreateInstance(document.getElementById('liveToast'), {delay: 3500}).show();
}

function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
}

async function readJsonResponse(response) {
    let result;
    try { result = await response.json(); }
    catch { throw new Error(`Server returned an invalid response (HTTP ${response.status}).`); }
    if (!response.ok) throw new Error(result.error || `Request failed (HTTP ${response.status}).`);
    return result;
}

// Natural Language Allocation Handler
async function submitNaturalLanguagePrompt() {
    const input = document.getElementById('nlPromptInput');
    const promptText = input.value.trim();
    if (!promptText) {
        showToast('Please enter a resource requirement.');
        input.focus();
        return;
    }
    
    const btn = document.querySelector('button[onclick="submitNaturalLanguagePrompt()"]');
    if (btn) {
        btn.disabled = true;
        btn.dataset.originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Allocating…';
    }
    
    try {
        const res = await fetch('api/natural_language_allocate.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
            body: JSON.stringify({prompt: promptText})
        });
        
        const result = await readJsonResponse(res);
        if (!result.success) throw new Error(result.error || 'Allocation could not be completed.');

        showToast(`✓ Assigned to ${result.resource_name} (${result.category}) | Suitability Score: ${result.score}`);
        input.value = '';
        await loadDashboard(true);

    } catch (err) {
        console.error('Auto-allocation error:', err);
        showToast(`Auto-Allocate Error: ${err.message}`);
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = btn.dataset.originalText || '<i class="bi bi-magic me-1"></i> Auto-Allocate';
        }
    }
}

document.querySelectorAll('.prompt-example').forEach(btn => {
    btn.addEventListener('click', () => {
        const inp = document.getElementById('nlPromptInput');
        if (inp) {
            inp.value = btn.dataset.prompt;
            inp.focus();
        }
    });
});

const nlInp = document.getElementById('nlPromptInput');
if (nlInp) {
    nlInp.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            submitNaturalLanguagePrompt();
        }
    });
}

function exportAllocationsCSV() {
    const q = v => '"' + String(v ?? '').replace(/"/g, '""') + '"';
    const rows = [['Task Title', 'Assigned Resource', 'Category', 'Score', 'Timestamp']];
    (latestDashboardData.allocations || []).forEach(a => {
        rows.push([a.task_title, a.resource_name, a.category, a.score, a.assigned_at]);
    });
    const blob = new Blob(['\uFEFF' + rows.map(r => r.map(q).join(',')).join('\r\n')], {type: 'text/csv;charset=utf-8;'});
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'rtrae_allocations_report.csv';
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(url);
}

function updateChart(resources) {
    const labels = resources.map(r => r.name);
    const activeLoads = resources.map(r => Number(r.active_load) || 0);
    const capacities = resources.map(r => Number(r.capacity) || 0);
    const canvas = document.getElementById('workloadChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    const chartData = {
        labels,
        datasets: [
            {
                label: 'Active Workload (Hrs)',
                data: activeLoads,
                backgroundColor: 'rgba(49, 95, 67, 0.88)',
                borderColor: '#315F43',
                borderWidth: 1.5,
                borderRadius: 8
            },
            {
                label: 'Total Capacity (Hrs)',
                data: capacities,
                backgroundColor: 'rgba(189, 137, 87, 0.35)',
                borderColor: '#BD8957',
                borderWidth: 1.5,
                borderRadius: 8
            }
        ]
    };

    const options = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#6B7168',
                    usePointStyle: true,
                    pointStyle: 'circle',
                    padding: 16,
                    font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }
                }
            },
            tooltip: {
                backgroundColor: '#171B18',
                titleColor: '#FFFDF7',
                bodyColor: '#B8CBB1',
                borderColor: 'rgba(189, 137, 87, 0.3)',
                borderWidth: 1,
                padding: 12,
                cornerRadius: 12,
                titleFont: { family: 'Playfair Display', size: 13, weight: '600' },
                bodyFont: { family: 'Plus Jakarta Sans', size: 11 }
            }
        },
        scales: {
            x: {
                ticks: { color: '#6B7168', maxRotation: 35, minRotation: 0, font: { size: 10, family: 'Plus Jakarta Sans' } },
                grid: { display: false },
                border: { display: false }
            },
            y: {
                beginAtZero: true,
                ticks: { color: '#6B7168', precision: 0, font: { family: 'JetBrains Mono', size: 10 } },
                grid: { color: 'rgba(49, 95, 67, 0.08)' },
                border: { display: false }
            }
        },
        animation: { duration: 650, easing: 'easeOutQuart' }
    };

    if (workloadChart) {
        workloadChart.data = chartData;
        workloadChart.options = options;
        workloadChart.update();
    } else {
        workloadChart = new Chart(ctx, {type: 'bar', data: chartData, options});
    }
}

let currentResourceView = 'table';

function setResourceView(mode) {
    currentResourceView = mode;
    const tableEl = document.getElementById('resource-table-view');
    const gridEl = document.getElementById('resource-grid-view');
    const btnTable = document.getElementById('btnViewTable');
    const btnGrid = document.getElementById('btnViewGrid');
    if (mode === 'table') {
        tableEl?.classList.remove('d-none');
        gridEl?.classList.add('d-none');
        btnTable?.classList.add('active');
        btnGrid?.classList.remove('active');
    } else {
        tableEl?.classList.add('d-none');
        gridEl?.classList.remove('d-none');
        btnTable?.classList.remove('active');
        btnGrid?.classList.add('active');
    }
    renderResources();
}

function renderResources() {
    const data = latestDashboardData.resources || [];
    const selectedCat = document.getElementById('categoryFilter')?.value || 'ALL';
    const query = (document.getElementById('resourceSearch')?.value || '').toLowerCase().trim();

    const filtered = data.filter(r => {
        const matchCat = (selectedCat === 'ALL' || r.category === selectedCat);
        const matchSearch = !query || 
            String(r.name || '').toLowerCase().includes(query) || 
            String(r.category || '').toLowerCase().includes(query) || 
            String(r.skills || '').toLowerCase().includes(query);
        return matchCat && matchSearch;
    });

    const badgeCount = document.getElementById('res-badge-count');
    if (badgeCount) badgeCount.textContent = `${filtered.length} / ${data.length} RESOURCES`;

    // Render Table View Body
    const tableBody = document.getElementById('resource-table-body');
    if (tableBody) {
        if (!filtered.length) {
            tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4">${query ? 'No resources matched search.' : 'No resources in this sector.'}</td></tr>`;
        } else {
            tableBody.innerHTML = filtered.map(r => {
                const cap = Number(r.capacity) || 0;
                const load = Number(r.active_load) || 0;
                const pct = cap > 0 ? Math.min(100, Math.max(0, Math.round((load / cap) * 100))) : 0;
                const skills = String(r.skills ?? '').split(',').filter(s => s.trim()).map(s => `<span class="skill-tag">${escapeHtml(s.trim())}</span>`).join('');
                const barColor = pct >= 80 ? 'background:linear-gradient(90deg,#DC2626,#EF4444)' : (pct >= 50 ? 'background:linear-gradient(90deg,#BD8957,#E8C59C)' : 'background:linear-gradient(90deg,#315F43,#7A9A74)');

                let sectorIcon = 'bi-collection';
                const cat = String(r.category || '');
                if (cat.includes('Staff')) sectorIcon = 'bi-person-badge-fill';
                else if (cat.includes('Room') || cat.includes('Classroom')) sectorIcon = 'bi-easel2-fill';
                else if (cat.includes('Server') || cat.includes('Lab')) sectorIcon = 'bi-pc-display';
                else if (cat.includes('Fleet') || cat.includes('Equipment')) sectorIcon = 'bi-truck-front-fill';
                else if (cat.includes('Catering') || cat.includes('Service')) sectorIcon = 'bi-cup-hot-fill';

                const statusText = pct >= 100 ? 'LOCKED (100%)' : (pct > 0 ? `${pct}% Booked` : 'Available');
                const statusStyle = pct >= 100 
                    ? 'background:rgba(220,38,38,0.12);color:#DC2626;border:1px solid rgba(220,38,38,0.25);' 
                    : (pct >= 50 
                        ? 'background:rgba(189,137,87,0.14);color:#BD8957;border:1px solid rgba(189,137,87,0.25);' 
                        : 'background:rgba(49,95,67,0.1);color:#315F43;border:1px solid rgba(49,95,67,0.22);');

                return `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-3 d-flex align-items-center justify-content-center text-white shrink-0" style="width:34px;height:34px;background:linear-gradient(135deg,#315F43,#234731);font-size:0.95rem;box-shadow:0 2px 6px rgba(49,95,67,0.2);">
                                    <i class="bi ${sectorIcon}"></i>
                                </div>
                                <div>
                                    <strong class="text-dark d-block lh-sm">${escapeHtml(r.name)}</strong>
                                    <small class="text-muted" style="font-size:0.75rem;">ID #${Number(r.id)}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge" style="background:rgba(49,95,67,0.08);color:#234731;border:1px solid rgba(49,95,67,0.18);font-size:0.73rem;">${escapeHtml(r.category)}</span></td>
                        <td><div class="d-flex flex-wrap gap-1">${skills || '<em class="text-muted small">No tags</em>'}</div></td>
                        <td class="font-mono fw-semibold text-dark">${escapeHtml(r.capacity)}h</td>
                        <td class="font-mono fw-semibold" style="color:var(--brand-forest);">${escapeHtml(r.active_load)}h</td>
                        <td>
                            <div class="d-flex align-items-center gap-2" style="min-width:140px;">
                                <div class="progress flex-grow-1" style="height:7px;background:rgba(49,95,67,0.1);border-radius:6px;overflow:hidden;">
                                    <div class="progress-bar" style="width:${pct}%;${barColor};transition:width 0.4s ease;"></div>
                                </div>
                                <span class="font-mono fw-bold" style="font-size:0.75rem;min-width:35px;color:var(--brand-charcoal);">${pct}%</span>
                            </div>
                        </td>
                        <td class="text-end"><span class="badge" style="${statusStyle};font-size:0.72rem;font-weight:700;">${statusText}</span></td>
                    </tr>
                `;
            }).join('');
        }
    }

    // Render Grid View Container
    const gridView = document.getElementById('resource-grid-view');
    if (gridView) {
        if (!filtered.length) {
            gridView.innerHTML = `<div class="col-12 text-center text-muted py-4">${query ? 'No resources matched search.' : 'No resources in this sector.'}</div>`;
        } else {
            gridView.innerHTML = filtered.map(r => {
                const cap = Number(r.capacity) || 0;
                const load = Number(r.active_load) || 0;
                const pct = cap > 0 ? Math.min(100, Math.max(0, Math.round((load / cap) * 100))) : 0;
                const skills = String(r.skills ?? '').split(',').filter(s => s.trim()).map(s => `<span class="skill-tag">${escapeHtml(s.trim())}</span>`).join('');
                const barColor = pct >= 80 ? 'background:linear-gradient(90deg,#DC2626,#EF4444)' : (pct >= 50 ? 'background:linear-gradient(90deg,#BD8957,#E8C59C)' : 'background:linear-gradient(90deg,#315F43,#7A9A74)');

                let sectorIcon = 'bi-collection';
                const cat = String(r.category || '');
                if (cat.includes('Staff')) sectorIcon = 'bi-person-badge-fill';
                else if (cat.includes('Room') || cat.includes('Classroom')) sectorIcon = 'bi-easel2-fill';
                else if (cat.includes('Server') || cat.includes('Lab')) sectorIcon = 'bi-pc-display';
                else if (cat.includes('Fleet') || cat.includes('Equipment')) sectorIcon = 'bi-truck-front-fill';
                else if (cat.includes('Catering') || cat.includes('Service')) sectorIcon = 'bi-cup-hot-fill';

                const statusText = pct >= 100 ? 'LOCKED (100%)' : (pct > 0 ? `${pct}% Booked` : 'Available');
                const statusStyle = pct >= 100 
                    ? 'background:rgba(220,38,38,0.12);color:#DC2626;border:1px solid rgba(220,38,38,0.25);' 
                    : (pct >= 50 
                        ? 'background:rgba(189,137,87,0.14);color:#BD8957;border:1px solid rgba(189,137,87,0.25);' 
                        : 'background:rgba(49,95,67,0.1);color:#315F43;border:1px solid rgba(49,95,67,0.22);');

                return `
                    <div class="col">
                        <div class="resource-item h-100 p-3" style="background:#FFFDF7;border:1px solid rgba(49,95,67,0.18);border-radius:14px;box-shadow:0 3px 12px rgba(23,27,24,0.03);">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white shrink-0" style="width:36px;height:36px;background:linear-gradient(135deg,#315F43,#234731);font-size:1rem;box-shadow:0 3px 8px rgba(49,95,67,0.2);">
                                        <i class="bi ${sectorIcon}"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold" style="color:#171B18 !important;font-size:0.92rem;letter-spacing:-0.01em;">${escapeHtml(r.name)}</h6>
                                        <span class="badge mt-0.5" style="background:rgba(49,95,67,0.08);color:#234731;border:1px solid rgba(49,95,67,0.18);font-size:0.68rem;font-weight:600;">${escapeHtml(r.category)}</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge mb-1" style="${statusStyle};font-size:0.68rem;font-weight:700;">${statusText}</span>
                                    <div class="fw-bold font-mono" style="color:#315F43;font-size:0.76rem;">${escapeHtml(r.active_load)} / ${escapeHtml(r.capacity)}h</div>
                                </div>
                            </div>
                            <div class="progress my-2" style="height:7px;background:rgba(49,95,67,0.08);border-radius:6px;overflow:hidden;">
                                <div class="progress-bar" style="width:${pct}%;${barColor};transition:width 0.4s ease;"></div>
                            </div>
                            <div class="d-flex flex-wrap gap-1 mt-2">${skills}</div>
                        </div>
                    </div>
                `;
            }).join('');
        }
    }
}

function renderTasks() {
    const data = latestDashboardData;
    const query = (document.getElementById('taskSearch')?.value || '').toLowerCase().trim();
    const filtered = (data.tasks || []).filter(t => 
        String(t.title || '').toLowerCase().includes(query) || 
        String(t.required_skill || '').toLowerCase().includes(query)
    );
    const badgeCount = document.getElementById('task-badge-count');
    if (badgeCount) badgeCount.textContent = `${filtered.length} IN QUEUE`;

    const body = document.getElementById('task-table-body');
    if (!body) return;

    body.innerHTML = filtered.length ? filtered.map(t => {
        const status = String(t.status || 'unknown').toLowerCase();
        const pending = status === 'pending';
        const allocated = status === 'allocated';
        const prio = Number(t.priority) || 3;
        const prioBadge = prio >= 5 
            ? '<span class="badge" style="background:rgba(220,38,38,0.12);color:#DC2626;border:1px solid rgba(220,38,38,0.25);">P5 CRITICAL</span>'
            : (prio >= 4 
                ? '<span class="badge" style="background:rgba(189,137,87,0.14);color:var(--brand-bronze);border:1px solid rgba(189,137,87,0.3);">P4 HIGH</span>'
                : `<span class="badge" style="background:rgba(49,95,67,0.1);color:var(--brand-forest);border:1px solid rgba(49,95,67,0.22);">P${prio}</span>`);

        const badgeCls = allocated ? 'bg-success' : (pending ? 'bg-warning text-dark' : 'bg-secondary');
        return `
            <tr>
                <td><strong class="text-dark">${escapeHtml(t.title)}</strong></td>
                <td><span class="badge" style="background:rgba(49,95,67,0.08);color:var(--brand-forest);border:1px solid rgba(49,95,67,0.2);">${escapeHtml(t.required_skill)}</span></td>
                <td>${prioBadge}</td>
                <td style="font-family:'JetBrains Mono',monospace;">${escapeHtml(t.estimated_effort)}h</td>
                <td><span class="badge ${badgeCls}">${escapeHtml(status.toUpperCase())}</span></td>
                <td class="text-end">
                    ${pending ? 
                        `<button class="btn btn-sm btn-primary fw-bold" onclick="triggerAllocation(${Number(t.id)})"><i class="bi bi-lightning-charge me-1"></i>Allocate</button>` : 
                        `<button class="btn btn-sm btn-outline-secondary" disabled>${allocated ? 'Allocated' : 'Done'}</button>`
                    }
                </td>
            </tr>
        `;
    }).join('') : `<tr><td colspan="6" class="text-center text-muted py-4">${query ? 'No tasks match search criteria.' : 'No tasks in queue.'}</td></tr>`;
}

async function loadDashboard(force = false) {
    if (dashboardRequestInFlight && !force) return;
    dashboardRequestInFlight = true;
    try {
        const res = await fetch('api/get_dashboard.php', {headers: {'Accept': 'application/json'}, cache: 'no-store'});
        const data = await readJsonResponse(res);
        if (!data.success) throw new Error(data.error || 'Unable to fetch dashboard telemetry.');

        latestDashboardData = {
            resources: Array.isArray(data.resources) ? data.resources : [],
            tasks: Array.isArray(data.tasks) ? data.tasks : [],
            allocations: Array.isArray(data.allocations) ? data.allocations : []
        };

        const totalCap = latestDashboardData.resources.reduce((acc, r) => acc + (Number(r.capacity) || 0), 0);
        const totalLoad = latestDashboardData.resources.reduce((acc, r) => acc + (Number(r.active_load) || 0), 0);

        document.getElementById('kpi-resources').textContent = latestDashboardData.resources.length;
        document.getElementById('kpi-pending').textContent = latestDashboardData.tasks.filter(t => String(t.status).toLowerCase() === 'pending').length;
        document.getElementById('kpi-active').textContent = latestDashboardData.allocations.length;
        document.getElementById('kpi-utilization').textContent = (totalCap > 0 ? Math.round((totalLoad / totalCap) * 100) : 0) + '%';
        document.getElementById('allocation-count').textContent = latestDashboardData.allocations.length;

        updateChart(latestDashboardData.resources);
        renderResources();

        // Render Active Allocations
        const allocBody = document.getElementById('allocation-table-body');
        allocBody.innerHTML = latestDashboardData.allocations.length ? latestDashboardData.allocations.map(a => `
            <tr>
                <td><strong class="text-dark">${escapeHtml(a.task_title)}</strong></td>
                <td>${escapeHtml(a.resource_name)}</td>
                <td><span class="badge bg-success">${escapeHtml(a.score)}</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary py-1 px-2.5" onclick="releaseTask(${Number(a.task_id)})" aria-label="Release ${escapeHtml(a.task_title)}">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Release
                    </button>
                </td>
            </tr>
        `).join('') : '<tr><td colspan="4" class="text-center text-muted py-4">No active allocations locked yet.</td></tr>';

        renderTasks();

    } catch (err) {
        console.error('Dashboard refresh failed:', err);
    } finally {
        dashboardRequestInFlight = false;
    }
}

async function triggerAllocation(taskId) {
    try {
        const res = await fetch('api/allocate_task.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
            body: JSON.stringify({task_id: taskId})
        });
        const result = await readJsonResponse(res);
        showToast(result.success ? `✓ Assigned to ${result.resource_name} (Score: ${result.score})` : `Allocation failed: ${result.error || 'Unknown error'}`);
        if (result.success) await loadDashboard(true);
    } catch (err) {
        showToast(err.message || 'Could not allocate task.');
    }
}

async function releaseTask(taskId) {
    if (!confirm('Release this task and make its resource slot available again?')) return;
    try {
        const res = await fetch('api/release_task.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
            body: JSON.stringify({task_id: taskId})
        });
        const result = await readJsonResponse(res);
        showToast(result.success ? '✓ Resource slot released successfully.' : `Release failed: ${result.error || 'Unknown error'}`);
        if (result.success) await loadDashboard(true);
    } catch (err) {
        showToast(err.message || 'Could not release task.');
    }
}

async function submitNewResource(e) {
    e.preventDefault();
    const form = e.currentTarget;
    const payload = {
        action: 'add_resource',
        name: document.getElementById('res_name').value.trim(),
        category: document.getElementById('res_category').value,
        capacity: document.getElementById('res_capacity').value,
        skills: document.getElementById('res_skills').value.trim()
    };
    try {
        const res = await fetch('api/add_item.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
            body: JSON.stringify(payload)
        });
        const result = await readJsonResponse(res);
        if (!result.success) throw new Error(result.error || 'Resource could not be saved.');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('addResourceModal')).hide();
        form.reset();
        showToast(result.message || 'Resource added successfully.');
        await loadDashboard(true);
    } catch (err) {
        showToast(err.message || 'Error saving resource.');
    }
}

async function submitNewTask(e) {
    e.preventDefault();
    const form = e.currentTarget;
    const payload = {
        action: 'add_task',
        title: document.getElementById('task_title').value.trim(),
        required_skill: document.getElementById('task_skill').value.trim(),
        priority: document.getElementById('task_priority').value,
        estimated_effort: document.getElementById('task_effort').value
    };
    try {
        const res = await fetch('api/add_item.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
            body: JSON.stringify(payload)
        });
        const result = await readJsonResponse(res);
        if (!result.success) throw new Error(result.error || 'Task could not be saved.');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('addTaskModal')).hide();
        form.reset();
        showToast(result.message || 'Task enqueued successfully.');
        await loadDashboard(true);
    } catch (err) {
        showToast(err.message || 'Error saving task.');
    }
}

loadDashboard(true);
setInterval(() => loadDashboard(), 10000);

// Live Radar Azimuth Sweep Angle Tracker
(function initRadarSweepTracker() {
    const azimuthEl = document.getElementById('radar-azimuth');
    if (!azimuthEl) return;
    let angle = 184.22;
    setInterval(() => {
        angle = (angle + 1.25) % 360;
        azimuthEl.textContent = `AZIMUTH: ${angle.toFixed(2)}°`;
    }, 100);
})();
</script>
</body>
</html>