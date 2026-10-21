<?php
/**
 * Shared Header — renders <head> resources and top navigation bar.
 * Refined Editorial Luxury Theme (Forest Green, Warm Ivory, Charcoal, Muted Sage, Bronze).
 */
$pageTitle    = $pageTitle ?? 'RTRAE Engine — Real-Time Resource Allocation Engine';
$pageSubtitle = $pageSubtitle ?? 'Control Center';
$currentPage  = $currentPage ?? '';
$extraHeadTags = $extraHeadTags ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Google Fonts: Playfair Display (Editorial Serif), Plus Jakarta Sans (Clean UI Sans), JetBrains Mono -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet"/>
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
            --brand-bronze-light: #E8C59C;
            --brand-text-primary: #252B25;
            --brand-text-muted: #6B7168;
            --brand-border: rgba(49, 95, 67, 0.14);
        }
        * { box-sizing: border-box; }
        body {
            background-color: var(--brand-ivory);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--brand-text-primary);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }
        h1, h2, h3, h4, h5, h6, .font-serif {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--brand-charcoal);
            letter-spacing: -0.02em;
        }
        .text-dark { color: var(--brand-charcoal) !important; }
        .text-muted { color: var(--brand-text-muted) !important; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* Top Editorial Navigation Bar */
        .top-navbar {
            background: rgba(246, 243, 233, 0.94);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid var(--brand-border);
            padding: 0.9rem 0;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 4px 20px rgba(49, 95, 67, 0.05);
        }
        .brand-badge {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--brand-forest);
            color: var(--brand-cream);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.35rem;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(49, 95, 67, 0.25);
            transition: transform 0.25s ease;
        }
        .brand-badge:hover {
            transform: scale(1.05);
        }
        .nav-pill-link {
            color: var(--brand-text-muted);
            text-decoration: none;
            padding: 0.38rem 0.65rem;
            border-radius: 9px;
            font-weight: 600;
            font-size: 0.81rem;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }
        .nav-pill-link:hover, .nav-pill-link.active {
            color: var(--brand-forest) !important;
            background: rgba(49, 95, 67, 0.08);
            border-color: rgba(49, 95, 67, 0.22);
            transform: translateY(-1px);
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Editorial Cards */
        .glass-card, .analytics-card, .info-card {
            background: var(--brand-cream) !important;
            border: 1px solid var(--brand-border) !important;
            border-radius: 20px !important;
            box-shadow: 0 10px 30px -5px rgba(23, 27, 24, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.8);
            color: var(--brand-text-primary);
            transition: border-color 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease;
        }
        .glass-card:hover, .analytics-card:hover {
            border-color: rgba(49, 95, 67, 0.35) !important;
            box-shadow: 0 16px 36px -6px rgba(49, 95, 67, 0.12);
        }

        /* Editorial Tables */
        .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--brand-text-primary);
            border-color: var(--brand-border);
        }
        .table > thead th {
            background: rgba(49, 95, 67, 0.05) !important;
            color: var(--brand-forest) !important;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 700;
            border-bottom: 1px solid var(--brand-border);
            padding: 0.85rem 1rem;
        }
        .table > tbody td {
            color: var(--brand-text-primary) !important;
            border-bottom: 1px solid rgba(49, 95, 67, 0.08);
            padding: 0.95rem 1rem;
            vertical-align: middle;
            font-size: 0.86rem;
        }
        .table-hover > tbody > tr:hover > * {
            background-color: rgba(184, 203, 177, 0.18) !important;
        }

        /* Form Inputs */
        .form-control, .form-select {
            background-color: var(--brand-cream) !important;
            border: 1px solid rgba(49, 95, 67, 0.2) !important;
            color: var(--brand-text-primary) !important;
            border-radius: 12px;
            font-weight: 500;
            padding: 0.62rem 0.95rem;
            transition: all 0.25s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand-forest) !important;
            box-shadow: 0 0 0 3px rgba(49, 95, 67, 0.15) !important;
            background-color: #FFFFFF !important;
        }
        .form-control::placeholder {
            color: #8C9389;
        }

        /* Buttons */
        .btn {
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.25s ease;
        }
        .btn-primary {
            background: var(--brand-forest) !important;
            color: var(--brand-cream) !important;
            border: none !important;
            box-shadow: 0 4px 14px rgba(49, 95, 67, 0.25);
        }
        .btn-primary:hover:not(:disabled) {
            background: var(--brand-forest-hover) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(49, 95, 67, 0.35);
        }
        .btn-outline-primary {
            color: var(--brand-forest) !important;
            border-color: rgba(49, 95, 67, 0.4) !important;
            background: transparent;
        }
        .btn-outline-primary:hover {
            background: var(--brand-forest) !important;
            color: var(--brand-cream) !important;
            border-color: var(--brand-forest) !important;
        }
        .btn-outline-secondary, .btn-outline-dark {
            color: var(--brand-text-muted) !important;
            border-color: rgba(49, 95, 67, 0.25) !important;
            background: transparent;
        }
        .btn-outline-secondary:hover, .btn-outline-dark:hover {
            color: var(--brand-charcoal) !important;
            border-color: var(--brand-forest) !important;
            background: rgba(49, 95, 67, 0.05);
        }

        /* Badges */
        .badge {
            font-weight: 600;
            border-radius: 8px;
            padding: 0.4em 0.7em;
        }
        .badge.bg-primary {
            background: rgba(49, 95, 67, 0.14) !important;
            color: var(--brand-forest) !important;
            border: 1px solid rgba(49, 95, 67, 0.25);
        }
        .badge.bg-success {
            background: rgba(49, 95, 67, 0.12) !important;
            color: var(--brand-forest) !important;
            border: 1px solid rgba(49, 95, 67, 0.3);
        }
        .badge.bg-warning {
            background: rgba(189, 137, 87, 0.14) !important;
            color: var(--brand-bronze) !important;
            border: 1px solid rgba(189, 137, 87, 0.3);
        }
        .badge.bg-danger {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #DC2626 !important;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }
        .badge.bg-secondary, .badge.bg-light {
            background: rgba(184, 203, 177, 0.3) !important;
            color: var(--brand-text-primary) !important;
            border: 1px solid rgba(49, 95, 67, 0.15);
        }

        /* Progress Bar */
        .progress {
            background-color: rgba(49, 95, 67, 0.12);
            border-radius: 8px;
            overflow: hidden;
            height: 8px;
        }
        .progress-bar {
            background: linear-gradient(90deg, var(--brand-forest), var(--brand-bronze));
        }

        /* Modals */
        .modal-content {
            background: var(--brand-cream) !important;
            border: 1px solid var(--brand-border) !important;
            border-radius: 22px !important;
            color: var(--brand-text-primary) !important;
            box-shadow: 0 25px 70px rgba(23, 27, 24, 0.2);
        }
        .modal-header, .modal-footer {
            border-color: var(--brand-border) !important;
        }

        /* Animations & Micro-Interactions */
        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.96);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(49, 95, 67, 0.4);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(49, 95, 67, 0);
            }
        }
        @keyframes pulseBronzeGlow {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(189, 137, 87, 0.4);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(189, 137, 87, 0);
            }
        }
        @keyframes pingRing {
            0% {
                transform: scale(1);
                opacity: 0.9;
            }
            80%, 100% {
                transform: scale(2.4);
                opacity: 0;
            }
        }
        @keyframes floatGentle {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-4px); }
        }
        @keyframes shimmerGlint {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }

        .anim-fade-up {
            animation: fadeSlideUp 0.65s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .anim-scale {
            animation: fadeInScale 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .anim-float {
            animation: floatGentle 4s ease-in-out infinite;
        }
        .delay-1 { animation-delay: 0.08s; }
        .delay-2 { animation-delay: 0.16s; }
        .delay-3 { animation-delay: 0.24s; }
        .delay-4 { animation-delay: 0.32s; }
        .delay-5 { animation-delay: 0.40s; }

        /* Live Indicator Dots */
        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            background-color: var(--brand-forest);
        }
        .live-dot::after {
            content: "";
            position: absolute;
            inset: -2px;
            border-radius: 50%;
            background-color: var(--brand-forest);
            animation: pingRing 2s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        .live-dot-bronze {
            background-color: var(--brand-bronze);
        }
        .live-dot-bronze::after {
            background-color: var(--brand-bronze);
        }

        /* Sleek Modern Scrollbars */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(49, 95, 67, 0.03);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(49, 95, 67, 0.2);
            border-radius: 8px;
            transition: background 0.2s ease;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(49, 95, 67, 0.4);
        }

        /* Footer */
        .footer-bar {
            background: #F6F3E9;
            border-top: 1px solid var(--brand-border);
            color: var(--brand-text-muted);
            padding: 2.2rem 0;
            margin-top: 4.5rem;
        }

        @media print {
            .top-navbar, .footer-bar, .no-print, #aiChatWidget { display: none !important; }
            body { background: #fff !important; color: #000 !important; }
            .glass-card, .analytics-card, .info-card { border: 1px solid #ccc !important; box-shadow: none !important; background: #fff !important; }
        }
    </style>
    <?= $extraHeadTags ?>
</head>
<body>

<nav class="top-navbar mb-4 no-print">
    <div class="container-fluid px-3 px-xl-4 d-flex align-items-center justify-content-between flex-nowrap" style="max-width: 1680px; margin: 0 auto; gap: 0.75rem;">
        <!-- Logo -->
        <a href="dashboard.php" class="text-decoration-none d-flex align-items-center gap-2.5 flex-shrink-0" title="RTRAE Campus Allocation Engine">
            <div class="brand-badge" style="width:38px;height:38px;font-size:1.2rem;">R</div>
            <div class="d-flex align-items-center gap-1.5">
                <span class="fw-bold tracking-wider fs-5" style="color:var(--brand-forest);font-family:'Plus Jakarta Sans',sans-serif;">RTRAE</span>
                <span class="rounded-circle d-none d-lg-inline-block" style="width:5px;height:5px;background:var(--brand-bronze);"></span>
                <small class="text-muted ms-1 d-none d-xl-inline" style="font-size:0.75rem;">/ <?= htmlspecialchars($pageSubtitle) ?></small>
            </div>
        </a>

        <!-- Main Navigation Links (Single systematic line with compact spacing) -->
        <div class="d-flex align-items-center flex-nowrap gap-1 overflow-x-auto no-scrollbar mx-1" style="white-space: nowrap;">
            <a href="dashboard.php" class="nav-pill-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
            <a href="calendar.php" class="nav-pill-link <?= $currentPage === 'calendar' ? 'active' : '' ?>"><i class="bi bi-calendar3 me-1"></i> Calendar</a>
            <a href="analytics.php" class="nav-pill-link <?= $currentPage === 'analytics' ? 'active' : '' ?>"><i class="bi bi-graph-up-arrow me-1"></i> Analytics</a>
            <a href="audit.php" class="nav-pill-link <?= $currentPage === 'audit' ? 'active' : '' ?>"><i class="bi bi-bell me-1"></i> Alerts &amp; Audit</a>
            <a href="settings.php" class="nav-pill-link <?= $currentPage === 'settings' ? 'active' : '' ?>"><i class="bi bi-sliders me-1"></i> Settings</a>
            <a href="info.php" class="nav-pill-link <?= $currentPage === 'info' ? 'active' : '' ?>"><i class="bi bi-info-circle me-1"></i> System Info</a>
            <a href="index.php" class="nav-pill-link"><i class="bi bi-book-half me-1"></i> Guide</a>
        </div>

        <!-- User Profile Pill & Logout (Seamlessly placed in the same line) -->
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <div class="user-profile-badge d-flex align-items-center gap-2 px-2.5 py-1 rounded-pill" style="background:#FFFDF7;border:1px solid rgba(49,95,67,0.2);box-shadow:0 2px 6px rgba(23,27,24,0.03);">
                <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,var(--brand-forest),#234731);color:#FFFDF7;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.75rem;box-shadow:0 2px 5px rgba(49,95,67,0.2);">
                    <?= strtoupper(substr($userName, 0, 1)) ?>
                </div>
                <div class="d-flex flex-column text-start pe-1" style="line-height:1.15;">
                    <span class="fw-bold text-truncate" style="max-width:130px;font-size:0.80rem;color:var(--brand-charcoal);letter-spacing:-0.01em;"><?= htmlspecialchars($userName) ?></span>
                    <span class="badge p-0 text-start" style="background:transparent;color:var(--brand-bronze);font-size:0.62rem;font-family:'JetBrains Mono',monospace;font-weight:700;letter-spacing:0.04em;">
                        <span class="d-inline-block rounded-circle me-1" style="width:5px;height:5px;background:#10B981;vertical-align:middle;"></span><?= strtoupper(htmlspecialchars($userRole)) ?>
                    </span>
                </div>
            </div>
            <a href="logout.php" class="btn btn-sm btn-outline-danger fw-semibold px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1" style="font-size:0.78rem;border-color:rgba(220,38,38,0.25);background:rgba(220,38,38,0.04);color:#DC2626;white-space:nowrap;" title="Sign out of Control Center">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Logout</span>
            </a>
        </div>
    </div>
</nav>
