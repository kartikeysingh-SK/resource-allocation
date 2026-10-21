<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/database.php';
$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $_SESSION['full_name'] ?? '';

// Fetch live telemetry statistics from database
$totalRes = 14;
$totalTasks = 6;
try {
    $resCount = $pdo->query("SELECT COUNT(*) FROM resources")->fetchColumn();
    if ($resCount !== false) {
        $totalRes = (int)$resCount;
    }
    $taskCount = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status = 'pending'")->fetchColumn();
    if ($taskCount !== false) {
        $totalTasks = (int)$taskCount;
    }
} catch (Exception $e) {
    // Graceful fallback to seed counts
}
?>
<!DOCTYPE html>
<html class="scroll-smooth" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>RTRAE — Real-Time Resource Allocation Engine</title>
    <!-- Google Fonts: Playfair Display (Editorial Serif) & Plus Jakarta Sans (Clean UI Sans) & JetBrains Mono -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
    <!-- Tailwind CSS CDN with forms and container queries plugins -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            forest: '#315F43',
                            forestHover: '#234731',
                            charcoalDark: '#171B18',
                            ivory: '#F6F3E9',
                            cream: '#FFFDF7',
                            sage: '#B8CBB1',
                            bronze: '#BD8957',
                            bronzeLight: '#E8C59C',
                            textPrimary: '#252B25',
                            textMuted: '#6B7168',
                            borderPale: 'rgba(49, 95, 67, 0.12)'
                        }
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #F6F3E9;
            color: #252B25;
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
            -webkit-font-smoothing: antialiased;
        }
        .editorial-border {
            border-color: rgba(49, 95, 67, 0.14);
        }
        .editorial-border-light {
            border-color: rgba(246, 243, 233, 0.12);
        }
        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased text-brand-textPrimary bg-brand-ivory selection:bg-brand-sage selection:text-brand-charcoalDark">

<!-- BEGIN: Navigation -->
<header class="sticky top-0 z-50 bg-[#F6F3E9]/90 backdrop-blur-md border-b editorial-border">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 h-20 flex items-center justify-between">
        <!-- Brand Logo / Emblem -->
        <a class="flex items-center gap-3.5 group" href="index.php">
            <div class="w-10 h-10 rounded-xl bg-brand-forest text-brand-cream flex items-center justify-center font-serif text-xl font-bold tracking-wider shadow-sm transition-transform duration-300 group-hover:scale-105">
                R
            </div>
            <div class="flex items-center gap-1.5">
                <span class="font-sans font-bold text-lg tracking-wider text-brand-forest">RTRAE</span>
                <span class="w-1.5 h-1.5 rounded-full bg-brand-bronze"></span>
            </div>
        </a>

        <!-- Navigation Links -->
        <nav class="hidden xl:flex items-center space-x-6 text-sm font-medium text-brand-textMuted" data-purpose="main-navigation">
            <a class="hover:text-brand-forest transition-colors duration-200" href="#overview">Overview</a>
            <a class="hover:text-brand-forest transition-colors duration-200" href="#features">Capabilities</a>
            <a class="hover:text-brand-forest transition-colors duration-200" href="#sectors">Sectors</a>
            <a class="hover:text-brand-forest transition-colors duration-200" href="#algorithm">Decision Model</a>
            <a class="hover:text-brand-forest transition-colors duration-200" href="#guide">Workflow</a>
            <a class="hover:text-brand-forest transition-colors duration-200" href="#faq">FAQ</a>
            <a class="hover:text-brand-forest transition-colors duration-200" href="#contact">Contact</a>
        </nav>

        <!-- Navigation CTA Buttons & User Profile -->
        <div class="flex items-center gap-3 shrink-0">
            <?php if ($isLoggedIn): ?>
                <div class="hidden sm:flex items-center gap-2.5 px-3 py-1.5 rounded-full bg-brand-cream border border-brand-borderPale shadow-xs">
                    <div class="w-7 h-7 rounded-full bg-brand-forest text-brand-cream flex items-center justify-center font-bold text-xs">
                        <?= strtoupper(substr($userName, 0, 1)) ?>
                    </div>
                    <div class="text-left leading-none">
                        <span class="block text-xs font-bold text-brand-textPrimary whitespace-nowrap"><?= htmlspecialchars($userName) ?></span>
                        <span class="block text-[10px] text-brand-bronze font-mono uppercase tracking-wider font-semibold mt-0.5">Admin</span>
                    </div>
                </div>
                <a class="text-xs font-semibold text-brand-textMuted hover:text-red-700 transition-colors px-2.5 py-1.5 rounded-lg border border-transparent hover:border-red-200 hover:bg-red-50/70" href="logout.php">
                    Logout
                </a>
                <a class="inline-flex items-center gap-2 bg-brand-forest hover:bg-brand-forestHover text-brand-cream text-xs sm:text-sm font-semibold px-4 sm:px-5 py-2 sm:py-2.5 rounded-full shadow-sm transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 whitespace-nowrap" href="dashboard.php">
                    <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                    <span>Control Center</span>
                </a>
            <?php else: ?>
                <a class="text-sm font-semibold text-brand-forest hover:text-brand-forestHover transition-colors px-3 py-1.5" href="login.php">
                    Sign In
                </a>
                <a class="inline-flex items-center gap-2 bg-brand-forest hover:bg-brand-forestHover text-brand-cream text-sm font-semibold px-5 py-2.5 rounded-full shadow-sm transition-all duration-300 hover:shadow-md" href="register.php">
                    <span>Create Account</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
<!-- END: Navigation -->

<!-- BEGIN: HeroSection -->
<section class="relative pt-12 pb-20 md:pt-16 md:pb-28 overflow-hidden" data-purpose="hero-section">
    <div class="max-w-7xl mx-auto px-6 sm:px-8">
        <!-- Top Eyebrow Tag -->
        <div class="flex justify-start mb-6">
            <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-brand-cream border border-brand-sage/60 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-brand-bronze"></span>
                <span class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-forest">
                    Universal Multi-Sector Allocation Platform
                </span>
            </div>
        </div>

        <!-- Main Hero Content: Two-Column Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
            <!-- Left Column: Copy, CTAs, and Live Telemetry Badges -->
            <div class="lg:col-span-6 space-y-8">
                <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-medium tracking-tight text-brand-charcoalDark leading-[1.12]">
                    Allocate Smarter.<br/>
                    Work Better.<br/>
                    <span class="italic font-normal text-brand-forest">Operate in Real Time.</span>
                </h1>
                <p class="text-base sm:text-lg text-brand-textMuted leading-relaxed max-w-xl font-normal">
                    Automatically match incoming tasks, room bookings, computational workloads, and operational requests to available staff, classrooms, GPU clusters, laboratories, and specialized facilities in sub-3ms deliberation time.
                </p>

                <!-- Primary Actions -->
                <div class="flex flex-wrap items-center gap-5 pt-2">
                    <a class="inline-flex items-center gap-2.5 bg-brand-forest hover:bg-brand-forestHover text-brand-cream text-base font-medium px-8 py-3.5 rounded-full shadow-md hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300" href="<?= $isLoggedIn ? 'dashboard.php' : 'login.php' ?>">
                        <svg class="w-5 h-5 text-brand-sage" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                        <span><?= $isLoggedIn ? 'Go to Control Center' : 'Access Control Center' ?></span>
                    </a>
                    <a class="group inline-flex items-center gap-2 text-brand-forest font-semibold text-base hover:text-brand-charcoalDark transition-colors" href="#guide">
                        <span>Explore Documentation</span>
                        <span class="transform transition-transform group-hover:translate-x-1.5 font-serif text-lg">→</span>
                    </a>
                </div>

                <!-- Live Metrics Mini-Dashboard -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                    <!-- Metric 1: Active Managed Pool -->
                    <div class="bg-brand-cream border editorial-border rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-300 flex items-center justify-between" data-purpose="stat-managed-pool">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-brand-textMuted font-medium mb-1">Active Managed Pool</p>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-bold font-serif text-brand-forest"><?= $totalRes ?></span>
                                <span class="text-xs font-semibold text-brand-forest/90 uppercase tracking-wide">Active Resources</span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-brand-sage/25 flex items-center justify-center text-brand-forest">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Metric 2: Live Queue Backlog -->
                    <div class="bg-brand-cream border editorial-border rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-300 flex items-center justify-between" data-purpose="stat-queue-backlog">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-brand-textMuted font-medium mb-1">Live Queue Backlog</p>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-bold font-serif text-brand-bronze"><?= $totalTasks ?></span>
                                <span class="text-xs font-semibold text-brand-bronze/90 uppercase tracking-wide">Pending Requests</span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-amber-100/60 flex items-center justify-center text-brand-bronze">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Multi-Sector Photographic Collage (Real Operational Environments) -->
            <div class="lg:col-span-6 relative">
                <div class="relative bg-brand-cream p-3 rounded-3xl border editorial-border shadow-xl">
                    <!-- Multi-Sector 5-Asset Photo Collage Grid -->
                    <div class="grid grid-cols-12 gap-2.5 h-[500px]">
                        <!-- Asset 1: Modern University Classroom (Large Featured Left) -->
                        <div class="col-span-7 row-span-2 relative rounded-2xl overflow-hidden border border-[#315F43]/15 group">
                            <img alt="Modern university smart lecture hall" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAU8sSW3UdF8MYXFsf1K08UDBHKZ0aOSjWnAU33YEHYQGDgzjdsVRXh2eG0UescF2ttUy8k1BeKlnvafFpDZmhTtz6OdMWKtCjs_a43ZRfmvXXXk-1CyJstofNPh8HJK8LWvRuS612i0CLo6__WIOHogcBcBYU8p3WrgywV6pY3OgUJG2v9Q3wgIDrckA0loW40TIyvSnpmrVnBozULiZfuIN-1gIVfA09p778_bhvSV7IgZaltbrMXuQ"/>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                            <span class="absolute bottom-3 left-3 text-[11px] font-medium text-white/95 px-2.5 py-1 rounded-md bg-black/40 backdrop-blur-sm border border-white/10">Classrooms &amp; Halls (B01–B13)</span>
                        </div>

                        <!-- Asset 2: Enterprise Data Center Server Corridor -->
                        <div class="col-span-5 h-[155px] relative rounded-2xl overflow-hidden border border-[#315F43]/15 group">
                            <img alt="Enterprise data center server racks" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAOyzQeyitzlwVoH5ntGWl2EQA3noSU3-zag4cXA264ZTldNZIic6CH7TYmL2ADocjvox9qHjwrHJOdYb3aD64vHCMF0W7hq5qh4F1oagIoy7vaKsKx0jF5Rr7QRe4ji3vJMdY_0RrIRY0OWLsUy55HYvuw9ONpwO8TZJSXMoZy3om9ommI3PRTWUqKghccpCe-Jq1S6l3jOVyc-u1kQHS3cL1GCMI1hi4wNmFc168D1NrZNDx2PvOLwQ"/>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                            <span class="absolute bottom-2 left-2 text-[10px] font-medium text-white/95 px-2 py-0.5 rounded bg-black/40 backdrop-blur-sm border border-white/10">Cloud &amp; GPU Nodes</span>
                        </div>

                        <!-- Asset 3: Computer & Science Research Lab -->
                        <div class="col-span-5 h-[155px] relative rounded-2xl overflow-hidden border border-[#315F43]/15 group">
                            <img alt="High-tech computer and science laboratory" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBbVmIOHYDfxjm50Zxw3XDIs2A2chEpIajCdCHplwHWR0lvYaFCmN5G5vVOToCNDNrqcuIUOB0UOzjG-LZZmvDDccq4KTM_yTB0Dc4GeRidU7kJ4d78XunuJeRf2PIj33MnxVJy1gyv_wC5BFFWgVfAXDyJo4MGWHIneX5548dyDl1FGjsIpaVPNtiGnecjk9Q-BUTp9Ey0KczK2CMwGUTREK1YlH101ezQKir8RDzzHLzNNRzLBG_ttA"/>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                            <span class="absolute bottom-2 left-2 text-[10px] font-medium text-white/95 px-2 py-0.5 rounded bg-black/40 backdrop-blur-sm border border-white/10">Computing Labs (101–104)</span>
                        </div>

                        <!-- Asset 4: Healthcare Examination Suite -->
                        <div class="col-span-6 h-[165px] relative rounded-2xl overflow-hidden border border-[#315F43]/15 group">
                            <img alt="Clinical healthcare examination suite" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC03FxAymR9b43bjdAdKOo1TQBih7EwGToRbhjBU6u8KM6XRSMpO7S_Nw7-Zd7HackvGH47fgnBWnfiAwnqT8ZK-U5bUmaePf77CS4bfKkBJQKuOgomvr6g7ct_7MTog7Qu9vQk161bWHe8uD33VtREdQFrwcOLle45oSSeU--xgna7RfgUql8khA0Ti7C7um2dRwlsm9CnKCfc4VDaraAKSDm7aJqHIzJqdAkawtl85W1jCMh1X7UycQ"/>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                            <span class="absolute bottom-2 left-2 text-[10px] font-medium text-white/95 px-2 py-0.5 rounded bg-black/40 backdrop-blur-sm border border-white/10">Clinical &amp; Medical Units</span>
                        </div>

                        <!-- Asset 5: Corporate Executive Boardroom -->
                        <div class="col-span-6 h-[165px] relative rounded-2xl overflow-hidden border border-[#315F43]/15 group">
                            <img alt="Corporate executive conference boardroom" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA_fKJkhrujS-FZmnZfVi7jTam44eNMkPY47aHZQHLDTKTOHD8_wuXMZG_nZ534pJwlUCpTHppRFZ9ejAu3EaHY2vkijgxf5cmgDFFRPzK0clgFPTRFjOPQy--Umuqhft4_YQqJro0_zsN-EgCbhxGPUdZa6WMZJdmM9edVZLV2eb1jAkQD4veLBNrIw6M68FKBZ15QQsg0o1L58d74p-_Q97uAs3NVLtZ9hEDyb8GpHjwWwvxNoSUs7A"/>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                            <span class="absolute bottom-2 left-2 text-[10px] font-medium text-white/95 px-2 py-0.5 rounded bg-black/40 backdrop-blur-sm border border-white/10">Executive Wings (B–I)</span>
                        </div>
                    </div>

                    <!-- Central Floating Editorial Badge -->
                    <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-[#171B18]/90 backdrop-blur-md px-6 py-2.5 rounded-full border border-white/15 flex items-center gap-3 text-brand-ivory shadow-xl whitespace-nowrap">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="text-xs font-serif tracking-wider text-brand-cream">Smart Allocation — Across All Resources</span>
                        <span class="text-[10px] font-mono text-brand-bronze px-2 py-0.5 border border-brand-bronze/40 rounded-full">&lt; 3ms LATENCY</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- END: HeroSection -->

<!-- BEGIN: OverviewSection (4 Pillars) -->
<section class="py-20 border-t editorial-border bg-[#FFFDF7]/70" data-purpose="overview-section" id="overview">
    <div class="max-w-7xl mx-auto px-6 sm:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-forest block mb-2">Core Foundation</span>
            <h2 class="font-serif text-3xl sm:text-4xl font-medium text-brand-charcoalDark tracking-tight">
                Intelligent Allocation Architecture
            </h2>
            <div class="w-12 h-0.5 bg-brand-bronze mx-auto mt-4"></div>
            <p class="mt-4 text-sm sm:text-base text-brand-textMuted">
                A mathematically verified engine balancing instantaneous workload matching with rock-solid concurrency safeguards.
            </p>
        </div>

        <!-- 4 Horizontal Pillars -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Pillar 1 -->
            <div class="bg-brand-cream border editorial-border rounded-2xl p-7 hover:shadow-lg hover:-translate-y-1 transition-all duration-300" data-purpose="pillar-item">
                <div class="w-12 h-12 rounded-xl bg-brand-forest text-brand-cream flex items-center justify-center mb-5 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                        <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                    </svg>
                </div>
                <h3 class="font-serif text-xl font-semibold text-brand-charcoalDark mb-2.5">Resource Pools</h3>
                <p class="text-xs sm:text-sm text-brand-textMuted leading-relaxed">
                    Centralized tracking of faculty, rooms, compute clusters, and equipment with live capacity states and dynamic attributes.
                </p>
            </div>

            <!-- Pillar 2 -->
            <div class="bg-brand-cream border editorial-border rounded-2xl p-7 hover:shadow-lg hover:-translate-y-1 transition-all duration-300" data-purpose="pillar-item">
                <div class="w-12 h-12 rounded-xl bg-brand-forest text-brand-cream flex items-center justify-center mb-5 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                    </svg>
                </div>
                <h3 class="font-serif text-xl font-semibold text-brand-charcoalDark mb-2.5">Allocation Logic</h3>
                <p class="text-xs sm:text-sm text-brand-textMuted leading-relaxed">
                    Objective multi-factor convex suitability scoring eliminating heuristic bias and personal scheduling favoritism.
                </p>
            </div>

            <!-- Pillar 3 -->
            <div class="bg-brand-cream border editorial-border rounded-2xl p-7 hover:shadow-lg hover:-translate-y-1 transition-all duration-300" data-purpose="pillar-item">
                <div class="w-12 h-12 rounded-xl bg-brand-forest text-brand-cream flex items-center justify-center mb-5 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                        <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                    </svg>
                </div>
                <h3 class="font-serif text-xl font-semibold text-brand-charcoalDark mb-2.5">Booking Safety</h3>
                <p class="text-xs sm:text-sm text-brand-textMuted leading-relaxed">
                    Strict MySQL InnoDB row-level locking (<code class="font-mono text-xs text-brand-forest bg-brand-ivory px-1 py-0.5 rounded">SELECT ... FOR UPDATE</code>) ensuring zero double-booking collisions.
                </p>
            </div>

            <!-- Pillar 4 -->
            <div class="bg-brand-cream border editorial-border rounded-2xl p-7 hover:shadow-lg hover:-translate-y-1 transition-all duration-300" data-purpose="pillar-item">
                <div class="w-12 h-12 rounded-xl bg-brand-forest text-brand-cream flex items-center justify-center mb-5 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                    </svg>
                </div>
                <h3 class="font-serif text-xl font-semibold text-brand-charcoalDark mb-2.5">Real-Time Sync &amp; Audit</h3>
                <p class="text-xs sm:text-sm text-brand-textMuted leading-relaxed">
                    Cryptographic ledger tracking every lease acquisition, dispatch, and release event with immediate UI broadcasts.
                </p>
            </div>
        </div>
    </div>
</section>
<!-- END: OverviewSection -->

<!-- BEGIN: CapabilitiesGrid (6 Cards) -->
<section class="py-24" data-purpose="platform-features" id="features">
    <div class="max-w-7xl mx-auto px-6 sm:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-forest block mb-2">Engine Capabilities</span>
            <h2 class="font-serif text-3xl sm:text-4xl font-medium text-brand-charcoalDark">
                Engineered for Absolute Precision
            </h2>
            <p class="mt-3 text-base text-brand-textMuted">
                Six foundational platform modules built for multi-tenant enterprise orchestration.
            </p>
        </div>

        <!-- 6 Card Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="bg-brand-cream border editorial-border rounded-3xl p-8 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-brand-sage/20 text-brand-forest flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                        </svg>
                    </div>
                    <h3 class="font-serif text-2xl font-medium text-brand-charcoalDark mb-3">Neural Command Prompt</h3>
                    <p class="text-sm text-brand-textMuted leading-relaxed">
                        LLM natural language parser transforms phrases like <em class="text-brand-charcoalDark font-serif">"Reserve a 60-seat multimedia lab with GPUs"</em> into transactional query parameters automatically.
                    </p>
                </div>
                <div class="pt-6 mt-6 border-t editorial-border text-xs font-mono text-brand-forest flex items-center justify-between">
                    <span>LLM_PARSER // V2.5</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-brand-cream border editorial-border rounded-3xl p-8 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-brand-sage/20 text-brand-forest flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                        </svg>
                    </div>
                    <h3 class="font-serif text-2xl font-medium text-brand-charcoalDark mb-3">Conflict-Aware Allocation</h3>
                    <p class="text-sm text-brand-textMuted leading-relaxed">
                        Guarantees zero collision mathematical dispatch via serialized transaction isolation levels and atomic reservation commits.
                    </p>
                </div>
                <div class="pt-6 mt-6 border-t editorial-border text-xs font-mono text-brand-forest flex items-center justify-between">
                    <span>ZERO_COLLISION // ACID</span>
                    <span class="text-brand-bronze text-[11px]">SERIALIZABLE</span>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-brand-cream border editorial-border rounded-3xl p-8 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-brand-sage/20 text-brand-forest flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                        </svg>
                    </div>
                    <h3 class="font-serif text-2xl font-medium text-brand-charcoalDark mb-3">Capacity &amp; Fairness</h3>
                    <p class="text-sm text-brand-textMuted leading-relaxed">
                        Anti-fatigue load dispersion keeps staff workload balanced, prevents classroom wear hot-spots, and mitigates compute thermal stress.
                    </p>
                </div>
                <div class="pt-6 mt-6 border-t editorial-border text-xs font-mono text-brand-forest flex items-center justify-between">
                    <span>FAIRNESS_INDEX // 0.94</span>
                    <span class="text-brand-forest font-semibold">BALANCED</span>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="bg-brand-cream border editorial-border rounded-3xl p-8 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-brand-sage/20 text-brand-forest flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                        </svg>
                    </div>
                    <h3 class="font-serif text-2xl font-medium text-brand-charcoalDark mb-3">Scheduling Matrix</h3>
                    <p class="text-sm text-brand-textMuted leading-relaxed">
                        High-density 08:00 – 17:00 hourly timetable inspector providing synchronous slot views across buildings, wings, and compute clusters.
                    </p>
                </div>
                <div class="pt-6 mt-6 border-t editorial-border text-xs font-mono text-brand-forest flex items-center justify-between">
                    <span>TIMETABLE // 08:00–17:00</span>
                    <span class="text-emerald-700 font-semibold">LIVE MATRIX</span>
                </div>
            </div>

            <!-- Card 5 -->
            <div class="bg-brand-cream border editorial-border rounded-3xl p-8 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-brand-sage/20 text-brand-forest flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                        </svg>
                    </div>
                    <h3 class="font-serif text-2xl font-medium text-brand-charcoalDark mb-3">Alerts &amp; Audit History</h3>
                    <p class="text-sm text-brand-textMuted leading-relaxed">
                        Overload safeguards with cryptographic sha256 hashes verifying every single booking or reallocation in an immutable event ledger.
                    </p>
                </div>
                <div class="pt-6 mt-6 border-t editorial-border text-xs font-mono text-brand-forest flex items-center justify-between">
                    <span>SHA256_CHAIN // VERIFIED</span>
                    <span class="text-brand-bronze font-mono text-[11px]">IMMUTABLE</span>
                </div>
            </div>

            <!-- Card 6 -->
            <div class="bg-brand-cream border editorial-border rounded-3xl p-8 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-brand-sage/20 text-brand-forest flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                        </svg>
                    </div>
                    <h3 class="font-serif text-2xl font-medium text-brand-charcoalDark mb-3">Analytics &amp; Telemetry</h3>
                    <p class="text-sm text-brand-textMuted leading-relaxed">
                        S-curve distribution modeling, dynamic utilization heatmaps, and instant compliance-ready CSV and print-friendly PDF report exports.
                    </p>
                </div>
                <div class="pt-6 mt-6 border-t editorial-border text-xs font-mono text-brand-forest flex items-center justify-between">
                    <span>EXPORTS // CSV &amp; PDF</span>
                    <span class="text-brand-forest font-semibold">READY</span>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- END: CapabilitiesGrid -->

<!-- BEGIN: SectorsSection (4 Photographic Sector Cards) -->
<section class="py-24 bg-brand-cream border-y editorial-border" data-purpose="sectors-section" id="sectors">
    <div class="max-w-7xl mx-auto px-6 sm:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-forest block mb-2">Versatile Deployments</span>
            <h2 class="font-serif text-3xl sm:text-4xl font-medium text-brand-charcoalDark">
                Where Is RTRAE Useful?
            </h2>
            <p class="mt-3 text-base text-brand-textMuted">
                Engineered specifically for institutional campuses, research clusters, and high-stakes operations.
            </p>
        </div>

        <!-- 4 Photographic Sector Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Sector 1: Colleges & Universities -->
            <div class="bg-brand-ivory border editorial-border rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                <div class="h-48 overflow-hidden relative">
                    <img alt="Modern university smart classroom" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAU8sSW3UdF8MYXFsf1K08UDBHKZ0aOSjWnAU33YEHYQGDgzjdsVRXh2eG0UescF2ttUy8k1BeKlnvafFpDZmhTtz6OdMWKtCjs_a43ZRfmvXXXk-1CyJstofNPh8HJK8LWvRuS612i0CLo6__WIOHogcBcBYU8p3WrgywV6pY3OgUJG2v9Q3wgIDrckA0loW40TIyvSnpmrVnBozULiZfuIN-1gIVfA09p778_bhvSV7IgZaltbrMXuQ"/>
                    <span class="absolute top-3 left-3 bg-[#171B18]/80 backdrop-blur-sm text-[10px] uppercase font-mono font-bold tracking-wider text-brand-ivory px-2.5 py-1 rounded">Academia</span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="font-serif text-xl font-bold text-brand-charcoalDark mb-2">Colleges &amp; Universities</h3>
                        <p class="text-xs text-brand-textMuted leading-relaxed">
                            Allocate faculty, tiered lecture halls (<code class="font-mono bg-brand-cream px-1 rounded text-brand-forest">B01–B03</code> &amp; <code class="font-mono bg-brand-cream px-1 rounded text-brand-forest">B11–B13</code>), and specialized research laboratories.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t editorial-border text-[11px] font-mono text-brand-forest">
                        CAMPUS_MANAGEMENT // ENABLED
                    </div>
                </div>
            </div>

            <!-- Sector 2: IT & Cloud Centers -->
            <div class="bg-brand-ivory border editorial-border rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                <div class="h-48 overflow-hidden relative">
                    <img alt="Enterprise data center server corridor" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAOyzQeyitzlwVoH5ntGWl2EQA3noSU3-zag4cXA264ZTldNZIic6CH7TYmL2ADocjvox9qHjwrHJOdYb3aD64vHCMF0W7hq5qh4F1oagIoy7vaKsKx0jF5Rr7QRe4ji3vJMdY_0RrIRY0OWLsUy55HYvuw9ONpwO8TZJSXMoZy3om9ommI3PRTWUqKghccpCe-Jq1S6l3jOVyc-u1kQHS3cL1GCMI1hi4wNmFc168D1NrZNDx2PvOLwQ"/>
                    <span class="absolute top-3 left-3 bg-[#171B18]/80 backdrop-blur-sm text-[10px] uppercase font-mono font-bold tracking-wider text-brand-ivory px-2.5 py-1 rounded">Compute</span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="font-serif text-xl font-bold text-brand-charcoalDark mb-2">IT &amp; Cloud Centers</h3>
                        <p class="text-xs text-brand-textMuted leading-relaxed">
                            Route developer batch jobs, HPC workloads, and tensor GPU clusters with automated throttling and thermal safeguards.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t editorial-border text-[11px] font-mono text-brand-forest">
                        HPC_DISPATCH // CLUSTER_ONLINE
                    </div>
                </div>
            </div>

            <!-- Sector 3: Healthcare Facilities -->
            <div class="bg-brand-ivory border editorial-border rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                <div class="h-48 overflow-hidden relative">
                    <img alt="Modern clinical healthcare examination suite" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC03FxAymR9b43bjdAdKOo1TQBih7EwGToRbhjBU6u8KM6XRSMpO7S_Nw7-Zd7HackvGH47fgnBWnfiAwnqT8ZK-U5bUmaePf77CS4bfKkBJQKuOgomvr6g7ct_7MTog7Qu9vQk161bWHe8uD33VtREdQFrwcOLle45oSSeU--xgna7RfgUql8khA0Ti7C7um2dRwlsm9CnKCfc4VDaraAKSDm7aJqHIzJqdAkawtl85W1jCMh1X7UycQ"/>
                    <span class="absolute top-3 left-3 bg-[#171B18]/80 backdrop-blur-sm text-[10px] uppercase font-mono font-bold tracking-wider text-brand-ivory px-2.5 py-1 rounded">Clinical</span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="font-serif text-xl font-bold text-brand-charcoalDark mb-2">Healthcare Facilities</h3>
                        <p class="text-xs text-brand-textMuted leading-relaxed">
                            Coordinate medical specialists, diagnostic suites, and critical care emergency vehicles seamlessly.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t editorial-border text-[11px] font-mono text-brand-forest">
                        CLINICAL_ROSTER // SLA_PRIORITY
                    </div>
                </div>
            </div>

            <!-- Sector 4: Corporate Operations -->
            <div class="bg-brand-ivory border editorial-border rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                <div class="h-48 overflow-hidden relative">
                    <img alt="Corporate executive conference boardroom" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA_fKJkhrujS-FZmnZfVi7jTam44eNMkPY47aHZQHLDTKTOHD8_wuXMZG_nZ534pJwlUCpTHppRFZ9ejAu3EaHY2vkijgxf5cmgDFFRPzK0clgFPTRFjOPQy--Umuqhft4_YQqJro0_zsN-EgCbhxGPUdZa6WMZJdmM9edVZLV2eb1jAkQD4veLBNrIw6M68FKBZ15QQsg0o1L58d74p-_Q97uAs3NVLtZ9hEDyb8GpHjwWwvxNoSUs7A"/>
                    <span class="absolute top-3 left-3 bg-[#171B18]/80 backdrop-blur-sm text-[10px] uppercase font-mono font-bold tracking-wider text-brand-ivory px-2.5 py-1 rounded">Executive</span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="font-serif text-xl font-bold text-brand-charcoalDark mb-2">Corporate Operations</h3>
                        <p class="text-xs text-brand-textMuted leading-relaxed">
                            Automate executive boardroom bookings, Wings B–I conference suites, and catering dispatch.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t editorial-border text-[11px] font-mono text-brand-forest">
                        ENTERPRISE_CALENDAR // SYNCED
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- END: SectorsSection -->

<!-- BEGIN: DecisionModelSection -->
<section class="py-20" data-purpose="decision-algorithm-section" id="algorithm">
    <div class="max-w-7xl mx-auto px-6 sm:px-8">
        <!-- Refined Dark-Green (#171B18) Panel -->
        <div class="bg-[#171B18] text-brand-ivory rounded-3xl p-8 sm:p-14 border border-brand-forest/40 shadow-2xl relative overflow-hidden">
            <!-- Decorative background blur orb -->
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-brand-forest/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center relative z-10">
                <div class="lg:col-span-7 space-y-5">
                    <span class="inline-block px-3.5 py-1 rounded-full bg-brand-forest/40 border border-brand-sage/30 text-brand-sage text-xs font-semibold tracking-wider uppercase">
                        Algorithmic Core
                    </span>
                    <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-medium text-brand-cream leading-tight">
                        Suitability Score Formula
                    </h2>
                    <p class="text-sm sm:text-base text-brand-sage/90 leading-relaxed font-sans max-w-xl">
                        RTRAE evaluates prospective assets through an objective, multi-factor convex optimization function that calculates a normalized scalar index <code class="text-emerald-400 font-mono">S ∈ [0.00, 1.00]</code>.
                    </p>

                    <!-- Breakdown list -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                        <div class="bg-[#1F2521] border border-white/10 rounded-xl p-4">
                            <span class="text-brand-bronze font-mono font-bold text-lg block mb-1">0.50 × K</span>
                            <span class="font-semibold text-xs text-brand-cream block">Skill Match</span>
                            <p class="text-[11px] text-gray-400 mt-1">Attribute compatibility &amp; equipment capacity threshold.</p>
                        </div>
                        <div class="bg-[#1F2521] border border-white/10 rounded-xl p-4">
                            <span class="text-brand-bronze font-mono font-bold text-lg block mb-1">0.30 × A</span>
                            <span class="font-semibold text-xs text-brand-cream block">Availability</span>
                            <p class="text-[11px] text-gray-400 mt-1">Immediate slot latency and spatial proximity.</p>
                        </div>
                        <div class="bg-[#1F2521] border border-white/10 rounded-xl p-4">
                            <span class="text-brand-bronze font-mono font-bold text-lg block mb-1">0.20 × F</span>
                            <span class="font-semibold text-xs text-brand-cream block">Fairness</span>
                            <p class="text-[11px] text-gray-400 mt-1">Longitudinal equity and anti-fatigue load balancing.</p>
                        </div>
                    </div>
                </div>

                <!-- Formula Display Box -->
                <div class="lg:col-span-5 flex justify-center lg:justify-end">
                    <div class="w-full max-w-md bg-[#131714] text-brand-ivory p-8 rounded-2xl border-2 border-brand-forest/50 shadow-2xl">
                        <div class="flex items-center justify-between text-xs font-mono text-brand-sage uppercase tracking-widest pb-3 border-b border-white/10">
                            <span>Kernel Specification</span>
                            <span class="text-brand-bronze">STABLE V2.5</span>
                        </div>
                        <div class="my-6 text-center">
                            <div class="font-mono text-2xl sm:text-3xl font-bold text-emerald-400 tracking-wider">
                                S = 0.50K + 0.30A + 0.20F
                            </div>
                            <p class="text-xs text-gray-400 font-mono mt-2">Where K, A, F ∈ [0, 1]</p>
                        </div>
                        <div class="pt-3 border-t border-white/10 text-xs font-mono flex items-center justify-between text-gray-400">
                            <span>Deliberation: &lt; 0.04ms</span>
                            <span class="text-emerald-400 font-semibold">TARGET: MAX(S)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- END: DecisionModelSection -->

<!-- BEGIN: WorkflowSection (Execution Protocol) -->
<section class="py-24 bg-brand-charcoalDark text-brand-ivory relative" data-purpose="step-by-step-guide" id="guide">
    <div class="max-w-7xl mx-auto px-6 sm:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-sage block mb-2">Execution Protocol</span>
            <h2 class="font-serif text-3xl sm:text-4xl font-normal text-brand-cream">
                Four-Step Execution Workflow
            </h2>
            <p class="mt-3 text-sm sm:text-base text-brand-sage/80">
                How RTRAE processes incoming allocations with millisecond determinism.
            </p>
        </div>

        <!-- 4 Process Cards (2x2 Grid) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Step 01 -->
            <div class="bg-[#1F2521] border editorial-border-light rounded-2xl p-7 flex items-start gap-5 hover:border-brand-sage/40 hover:-translate-y-1 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl bg-brand-forest/40 border border-brand-sage/30 flex items-center justify-center shrink-0 text-brand-cream font-mono font-bold text-base shadow-sm">
                    01
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <h3 class="font-serif text-xl font-medium text-brand-cream">Receive</h3>
                        <span class="text-[10px] font-mono text-brand-bronze uppercase px-2 py-0.5 rounded bg-brand-bronze/15 border border-brand-bronze/30">Ingestion</span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                        Ingest natural language intent via Neural Command Prompt or scheduled backlog requests from ERP, SIS, or cloud dispatch queues.
                    </p>
                </div>
            </div>

            <!-- Step 02 -->
            <div class="bg-[#1F2521] border editorial-border-light rounded-2xl p-7 flex items-start gap-5 hover:border-brand-sage/40 hover:-translate-y-1 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl bg-brand-forest/40 border border-brand-sage/30 flex items-center justify-center shrink-0 text-brand-cream font-mono font-bold text-base shadow-sm">
                    02
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <h3 class="font-serif text-xl font-medium text-brand-cream">Evaluate</h3>
                        <span class="text-[10px] font-mono text-brand-bronze uppercase px-2 py-0.5 rounded bg-brand-bronze/15 border border-brand-bronze/30">Scoring</span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                        Compute normalized multi-variable convex score across all eligible candidates in the target pool, filtering disqualified states.
                    </p>
                </div>
            </div>

            <!-- Step 03 -->
            <div class="bg-[#1F2521] border editorial-border-light rounded-2xl p-7 flex items-start gap-5 hover:border-brand-sage/40 hover:-translate-y-1 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl bg-brand-forest/40 border border-brand-sage/30 flex items-center justify-center shrink-0 text-brand-cream font-mono font-bold text-base shadow-sm">
                    03
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <h3 class="font-serif text-xl font-medium text-brand-cream">Allocate</h3>
                        <span class="text-[10px] font-mono text-emerald-400 uppercase px-2 py-0.5 rounded bg-emerald-950/40 border border-emerald-500/30">ACID Commit</span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                        Acquire ACID row lock (<code class="font-mono text-xs text-brand-sage bg-black/40 px-1 py-0.5 rounded">SELECT ... FOR UPDATE</code>) on highest-ranked asset and atomically commit the assignment.
                    </p>
                </div>
            </div>

            <!-- Step 04 -->
            <div class="bg-brand-forest/30 border border-brand-sage/50 rounded-2xl p-7 flex items-start gap-5 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                <div class="w-12 h-12 rounded-xl bg-brand-forest border border-brand-sage flex items-center justify-center shrink-0 text-brand-cream font-mono font-bold text-base shadow">
                    04
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <h3 class="font-serif text-xl font-medium text-brand-cream">Review</h3>
                        <span class="px-2 py-0.5 rounded text-[10px] bg-brand-bronze text-brand-cream font-mono uppercase font-bold tracking-wider">Live Sync</span>
                    </div>
                    <p class="text-xs sm:text-sm text-brand-sage/90 leading-relaxed">
                        Immutable cryptographic ledger entry with SHA256 verification and immediate live telemetry update across all administrative monitors.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- END: WorkflowSection -->

<!-- BEGIN: FAQSection -->
<section class="py-24" data-purpose="faq-section" id="faq">
    <div class="max-w-4xl mx-auto px-6 sm:px-8">
        <div class="text-center mb-16">
            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-forest block mb-2">Knowledge Base</span>
            <h2 class="font-serif text-3xl sm:text-4xl font-medium text-brand-charcoalDark">
                Frequently Asked Questions
            </h2>
            <p class="mt-3 text-sm sm:text-base text-brand-textMuted">
                Everything you need to know about RTRAE operations, concurrency, and architecture.
            </p>
        </div>

        <!-- FAQ Accordion List -->
        <div class="space-y-4" data-purpose="accordion-group">
            <!-- FAQ Item 1 -->
            <div class="bg-brand-cream border editorial-border rounded-2xl p-6 shadow-xs hover:shadow-md transition-shadow">
                <details class="group cursor-pointer" open>
                    <summary class="flex items-center justify-between font-serif text-xl font-semibold text-brand-forest list-none">
                        <span>How does RTRAE prevent double-booking?</span>
                        <span class="text-brand-forest font-mono text-xl font-bold group-open:rotate-45 transition-transform">+</span>
                    </summary>
                    <div class="mt-3 pt-3 border-t editorial-border text-sm text-brand-textMuted leading-relaxed">
                        RTRAE uses strict MySQL InnoDB transactions with explicit row-level locking (<code class="font-mono text-xs bg-brand-ivory px-1.5 py-0.5 rounded text-brand-charcoalDark font-semibold">SELECT ... FOR UPDATE</code>). When a task is evaluated, the engine locks candidate records so simultaneous requests cannot exceed capacity or assign the same resource twice. Even under thousands of concurrent requests, no two threads can lease the same room, server cluster, or personnel simultaneously.
                    </div>
                </details>
            </div>

            <!-- FAQ Item 2 -->
            <div class="bg-brand-cream border editorial-border rounded-2xl p-6 shadow-xs hover:border-brand-forest/40 transition-colors">
                <details class="group cursor-pointer">
                    <summary class="flex items-center justify-between font-serif text-xl font-medium text-brand-charcoalDark list-none">
                        <span>Can I allocate resources across different sectors?</span>
                        <span class="text-brand-textMuted font-mono text-xl group-open:rotate-45 transition-transform">+</span>
                    </summary>
                    <div class="mt-3 pt-3 border-t editorial-border text-sm text-brand-textMuted leading-relaxed">
                        Yes. The platform's relational schema is sector-agnostic. You can register classrooms (B01–B13), computer labs (101–104), GPU cloud nodes, clinical examination suites, and corporate boardrooms under a unified tenant or configure partitioned domain pools with role-based access control.
                    </div>
                </details>
            </div>

            <!-- FAQ Item 3 -->
            <div class="bg-brand-cream border editorial-border rounded-2xl p-6 shadow-xs hover:border-brand-forest/40 transition-colors">
                <details class="group cursor-pointer">
                    <summary class="flex items-center justify-between font-serif text-xl font-medium text-brand-charcoalDark list-none">
                        <span>What happens when an allocated task is completed or released?</span>
                        <span class="text-brand-textMuted font-mono text-xl group-open:rotate-45 transition-transform">+</span>
                    </summary>
                    <div class="mt-3 pt-3 border-t editorial-border text-sm text-brand-textMuted leading-relaxed">
                        When a lease expires or is manually freed, an asynchronous trigger recalculates asset availability instantly. The queue listener notifies any queued tasks and triggers the decision algorithm to allocate waiting workloads without delay.
                    </div>
                </details>
            </div>
        </div>
    </div>
</section>
<!-- END: FAQSection -->

<!-- BEGIN: ContactSupportDesk -->
<section class="pb-24 pt-6" data-purpose="contact-section" id="contact">
    <div class="max-w-7xl mx-auto px-6 sm:px-8">
        <div class="bg-brand-cream border-2 editorial-border rounded-3xl p-8 sm:p-12 shadow-md">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7 space-y-4">
                    <span class="inline-block px-3.5 py-1 rounded-full bg-brand-bronze/15 text-brand-bronze text-xs font-semibold tracking-wider uppercase">
                        Support Desk
                    </span>
                    <h2 class="font-serif text-3xl sm:text-4xl font-medium text-brand-charcoalDark">
                        Contact Support &amp; Integration
                    </h2>
                    <p class="text-sm sm:text-base text-brand-textMuted max-w-xl">
                        For technical onboarding, cluster integrations, and API credentials for the RTRAE platform, reach our systems desk:
                    </p>

                    <!-- Contact Channels Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3">
                        <div class="bg-brand-ivory border editorial-border rounded-xl p-4 hover:shadow-xs transition-shadow">
                            <span class="text-[11px] uppercase tracking-wider text-brand-textMuted font-medium block mb-1">Official Helpline</span>
                            <a class="font-mono text-base font-bold text-brand-forest hover:underline" href="tel:+917048700224">
                                +91 7048700224
                            </a>
                        </div>
                        <div class="bg-brand-ivory border editorial-border rounded-xl p-4 hover:shadow-xs transition-shadow">
                            <span class="text-[11px] uppercase tracking-wider text-brand-textMuted font-medium block mb-1">Official Email</span>
                            <a class="font-mono text-base font-bold text-brand-forest hover:underline" href="mailto:25amtics@gmail.com">
                                25amtics@gmail.com
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Direct Action -->
                <div class="lg:col-span-5 flex justify-center lg:justify-end">
                    <a class="inline-flex items-center gap-3 bg-brand-forest hover:bg-brand-forestHover text-brand-cream text-lg font-medium px-8 py-4 rounded-full shadow-md hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300" href="<?= $isLoggedIn ? 'dashboard.php' : 'login.php' ?>">
                        <span>Open RTRAE Dashboard</span>
                        <svg class="w-5 h-5 text-brand-sage" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- END: ContactSupportDesk -->

<!-- BEGIN: Footer -->
<footer class="border-t editorial-border bg-[#F6F3E9] py-12 text-sm text-brand-textMuted" data-purpose="page-footer">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-brand-forest text-brand-cream flex items-center justify-center font-serif text-sm font-bold">
                R
            </div>
            <div>
                <span class="font-bold text-brand-charcoalDark tracking-wide">RTRAE</span>
                <span class="text-xs text-brand-textMuted ml-1.5">— Real-Time Resource Allocation Engine</span>
            </div>
        </div>
        <div class="text-center md:text-right text-xs space-y-1">
            <p>Campus, Lab &amp; Computational Infrastructure Allocation Engine</p>
            <p class="text-brand-textMuted/70">© 2026 RTRAE Systems. All rights reserved.</p>
        </div>
    </div>
</footer>
<!-- END: Footer -->

<!-- Floating Intelligent Webpage Knowledge Assistant on index.php -->
<div id="aiChatWidget" style="position:fixed;bottom:24px;right:24px;z-index:999999;">
    <button id="aiToggleBtn" type="button" onclick="openIndexAiChat(event)" class="shadow-xl flex items-center justify-center cursor-pointer" 
            style="width:58px;height:58px;border-radius:50%;background:#315F43;color:#FFFDF7;box-shadow:0 10px 25px rgba(49,95,67,0.4);border:2px solid rgba(255,255,255,0.4);font-size:1.45rem;transition:transform 0.25s ease;" 
            onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'"
            aria-label="Open RTRAE Assistant">
        <i class="bi bi-robot" id="aiBtnIcon"></i>
    </button>

    <div id="aiChatBox" class="shadow-2xl" 
         style="width:385px;max-width:calc(100vw - 32px);height:530px;max-height:calc(100vh - 48px);border-radius:22px;background:#FFFDF7;border:1px solid rgba(49,95,67,0.25);box-shadow:0 25px 60px -15px rgba(23,27,24,0.4);display:none;flex-direction:column;overflow:hidden;animation:chatSlideUp 0.35s cubic-bezier(0.16,1,0.3,1) both;">
        
        <div class="flex justify-between items-center py-3.5 px-4 bg-[#171B18] text-[#F6F3E9] border-b border-brand-forest/30">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-brand-forest text-brand-cream flex items-center justify-center font-serif font-bold text-sm">
                    R
                </div>
                <div>
                    <h6 class="font-serif font-bold text-sm tracking-wide text-brand-cream leading-none">RTRAE Assistant</h6>
                    <small class="text-brand-sage text-[11px]"><span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1"></span>Webpage Knowledge</small>
                </div>
            </div>
            <!-- Guaranteed Functional Controls: Minimize & Close ("Cut") -->
            <div class="flex items-center gap-1.5">
                <button type="button" id="indexChatMinBtn" onclick="closeIndexAiChat(event)" title="Minimize Chatbot" aria-label="Minimize Assistant" 
                        class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs font-bold cursor-pointer transition-all border border-white/20">
                    —
                </button>
                <button type="button" id="indexChatCloseBtn" onclick="closeIndexAiChat(event)" title="Close / Cut Chatbot (Esc)" aria-label="Close Assistant" 
                        class="w-7 h-7 rounded-lg bg-red-600/30 hover:bg-red-600 text-white flex items-center justify-center text-xs font-bold cursor-pointer transition-all border border-red-500/40">
                    ✕
                </button>
            </div>
        </div>

        <div id="indexChatMessages" class="p-4 flex-grow overflow-y-auto space-y-3 text-xs leading-relaxed" style="scroll-behavior:smooth;">
            <!-- Seed welcome message -->
            <div class="flex items-start gap-2 text-left">
                <div class="w-6 h-6 rounded-md bg-brand-forest text-white flex items-center justify-center shrink-0 text-xs">
                    <i class="bi bi-robot"></i>
                </div>
                <div class="bg-[#F6F3E9] text-[#252B25] p-3 rounded-xl border border-brand-sage/40 max-w-[88%]">
                    Hello! I have complete knowledge of this platform and webpage. Ask me anything about classrooms (B01–B13), computing labs, the suitability formula, or how to allocate tasks!
                    <div class="mt-2.5 pt-2 border-t border-brand-forest/15 flex flex-wrap gap-1">
                        <button class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-brand-forest/10 text-brand-forest hover:bg-brand-forest hover:text-white transition-colors" onclick="askIndexAi('Classroom Details')">Classrooms B01–B13</button>
                        <button class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-brand-forest/10 text-brand-forest hover:bg-brand-forest hover:text-white transition-colors" onclick="askIndexAi('Computer Lab Specs')">Computing Labs</button>
                        <button class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-brand-forest/10 text-brand-forest hover:bg-brand-forest hover:text-white transition-colors" onclick="askIndexAi('Scoring Formula')">Scoring Formula</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-2.5 bg-[#F6F3E9] border-t border-brand-sage/30">
            <div class="flex items-center gap-1.5 bg-white border border-brand-sage/50 rounded-xl px-2 py-1 shadow-inner">
                <input type="text" id="indexChatInput" class="w-full text-xs bg-transparent border-0 focus:ring-0 text-[#252B25] px-1 py-1" placeholder="Ask anything about this webpage…">
                <button onclick="sendIndexAiChat()" class="bg-brand-forest text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-brand-forestHover transition-colors cursor-pointer">
                    Send
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const INDEX_KB = [
    {
        keywords: ['hello', 'hi', 'hey', 'start', 'help'],
        reply: "Hello! Welcome to <strong>RTRAE</strong>. I can answer anything about our classrooms (B01–B13), computer labs (101–104), our 3-variable scoring algorithm, and real-time scheduling.",
        chips: ['Classrooms', 'Computing Labs', 'Formula', 'How to Allocate']
    },
    {
        keywords: ['classroom', 'classrooms', 'b01', 'b02', 'b03', 'b11', 'b12', 'b13', 'lecture', 'halls'],
        reply: "<strong>🏫 Classrooms B01–B03 &amp; B11–B13:</strong><br>• Capacity: 60 to 120 students per lecture hall.<br>• Equipment: Dual 86\" 4K interactive smartboards, ceiling digital projectors, motorized podiums, centralized AC.<br>• Managed under Wings B through I with automatic double-booking prevention.",
        chips: ['Computing Labs', 'How to Allocate', 'Formula']
    },
    {
        keywords: ['lab', 'labs', 'computer', 'gpu', 'workstation', 'specs'],
        reply: "<strong>💻 Advanced Computing Labs (101–104):</strong><br>• Workstations: 64 high-tier rigs per lab with Intel i9 CPUs, 32GB RAM, and NVIDIA RTX graphics.<br>• Cluster: GPU Cloud Node Cluster-A1 for AI batch training and compute.<br>• Network: 10 Gbps low-latency fiber with 4-hr industrial UPS backup.",
        chips: ['Classrooms', 'Formula', 'How to Allocate']
    },
    {
        keywords: ['formula', 'score', 'scoring', 'suitability', 'algorithm', 'math'],
        reply: "<strong>📐 Suitability Score Formula:</strong><br><code>S = 0.50K + 0.30A + 0.20F</code><br>• <strong>K (Skill Match)</strong>: 1.0 if asset has required capabilities, 0 otherwise.<br>• <strong>A (Availability)</strong>: Free Hours / Total Capacity.<br>• <strong>F (Fairness)</strong>: 1 - (Active Load / Total Capacity) to prevent burnout.",
        chips: ['Classrooms', 'Computing Labs', 'How to Allocate']
    },
    {
        keywords: ['allocate', 'assign', 'how to allocate', 'task'],
        reply: "<strong>⚡ How to Allocate Tasks:</strong><br>1. Log in and open the <strong>Control Center</strong>.<br>2. In the Task Queue, click <em>Allocate</em> on any pending task, or type requirements into the Natural Language Allocator bar.<br>3. The engine evaluates candidates and acquires a row lock (<code class='font-mono text-[10px] bg-gray-200 px-1 rounded'>SELECT ... FOR UPDATE</code>) to commit the assignment.",
        chips: ['Formula', 'Contact Support']
    },
    {
        keywords: ['contact', 'phone', 'email', 'support'],
        reply: "<strong>📞 RTRAE Support Desk:</strong><br>• Helpline: <a href='tel:7048700224' class='text-brand-forest font-bold underline'>+91 7048700224</a><br>• Email: <a href='mailto:25amtics@gmail.com' class='text-brand-forest font-bold underline'>25amtics@gmail.com</a>",
        chips: ['Classrooms', 'Computing Labs']
    }
];

function openIndexAiChat(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    const box = document.getElementById('aiChatBox');
    const btn = document.getElementById('aiToggleBtn');
    if (box) {
        box.style.setProperty('display', 'flex', 'important');
        const inp = document.getElementById('indexChatInput');
        if (inp) inp.focus();
    }
    if (btn) {
        btn.style.setProperty('display', 'none', 'important');
    }
}

function closeIndexAiChat(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    const box = document.getElementById('aiChatBox');
    const btn = document.getElementById('aiToggleBtn');
    if (box) {
        box.style.setProperty('display', 'none', 'important');
    }
    if (btn) {
        btn.style.setProperty('display', 'flex', 'important');
    }
}

function toggleIndexAiChat(e) {
    const box = document.getElementById('aiChatBox');
    if (box && box.style.display === 'flex') {
        closeIndexAiChat(e);
    } else {
        openIndexAiChat(e);
    }
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const box = document.getElementById('aiChatBox');
        if (box && box.style.display === 'flex') {
            closeIndexAiChat(e);
        }
    }
});

function askIndexAi(text) {
    const inp = document.getElementById('indexChatInput');
    if (inp) inp.value = text;
    sendIndexAiChat();
}

function sendIndexAiChat() {
    const inp = document.getElementById('indexChatInput');
    const msg = inp.value.trim();
    if (!msg) return;

    const box = document.getElementById('indexChatMessages');
    
    // User message
    const userWrap = document.createElement('div');
    userWrap.className = 'text-right';
    userWrap.innerHTML = `<div class="bg-brand-forest text-white p-2.5 rounded-xl inline-block text-xs max-w-[85%]">${escapeHtml(msg)}</div>`;
    box.appendChild(userWrap);
    inp.value = '';
    box.scrollTop = box.scrollHeight;

    // Bot reply
    setTimeout(() => {
        const lower = msg.toLowerCase();
        let matched = null;
        for (const item of INDEX_KB) {
            if (item.keywords.some(k => lower.includes(k))) {
                matched = item;
                break;
            }
        }

        const replyHtml = matched ? matched.reply : `I understand your question about "<strong>${escapeHtml(msg)}</strong>". Here are some core topics:`;
        const chips = matched ? (matched.chips || []) : ['Classrooms', 'Computing Labs', 'Formula', 'How to Allocate'];
        
        let chipsHtml = '';
        if (chips.length) {
            chipsHtml = `<div class="mt-2 pt-2 border-t border-brand-forest/15 flex flex-wrap gap-1">` + 
                chips.map(c => `<button class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-brand-forest/10 text-brand-forest hover:bg-brand-forest hover:text-white transition-colors" onclick="askIndexAi('${c}')">${c}</button>`).join('') + `</div>`;
        }

        const botWrap = document.createElement('div');
        botWrap.className = 'flex items-start gap-2 text-left';
        botWrap.innerHTML = `
            <div class="w-6 h-6 rounded-md bg-brand-forest text-white flex items-center justify-center shrink-0 text-xs">
                <i class="bi bi-robot"></i>
            </div>
            <div class="bg-[#F6F3E9] text-[#252B25] p-3 rounded-xl border border-brand-sage/40 max-w-[88%] text-xs">
                ${replyHtml}
                ${chipsHtml}
            </div>
        `;
        box.appendChild(botWrap);
        box.scrollTop = box.scrollHeight;
    }, 250);
}

function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
}

document.addEventListener('DOMContentLoaded', () => {
    const inp = document.getElementById('indexChatInput');
    if (inp) {
        inp.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendIndexAiChat();
            }
        });
    }

    const closeBtn = document.getElementById('indexChatCloseBtn');
    if (closeBtn) closeBtn.addEventListener('click', closeIndexAiChat);

    const minBtn = document.getElementById('indexChatMinBtn');
    if (minBtn) minBtn.addEventListener('click', closeIndexAiChat);
});

// Click outside to close ("cut") the chatbot
document.addEventListener('click', (e) => {
    const box = document.getElementById('aiChatBox');
    const btn = document.getElementById('aiToggleBtn');
    if (box && box.style.display === 'flex') {
        if (!box.contains(e.target) && !btn.contains(e.target)) {
            closeIndexAiChat(e);
        }
    }
});

window.closeAiChat = closeIndexAiChat;
window.closeIndexAiChat = closeIndexAiChat;
window.openAiChat = openIndexAiChat;
window.openIndexAiChat = openIndexAiChat;
</script>
</body>
</html>