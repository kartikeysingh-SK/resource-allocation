<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$registered = isset($_GET['registered']);
$prefillEmail = $_GET['email'] ?? '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Invalid institutional email or password. Please try again.";
        }
    } else {
        $error = "Please enter both email and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | RTRAE Engine</title>
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
            background-image: 
                radial-gradient(at 0% 0%, rgba(184, 203, 177, 0.25) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(189, 137, 87, 0.15) 0px, transparent 50%);
            color: var(--brand-text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
        }
        .editorial-card {
            background: var(--brand-cream);
            border: 1px solid var(--brand-border);
            border-radius: 24px;
            max-width: 460px;
            width: 100%;
            padding: 2.75rem 2.5rem;
            box-shadow: 0 25px 60px -15px rgba(23, 27, 24, 0.12), 0 0 0 1px rgba(255, 255, 255, 0.9);
            animation: cardReveal 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        @keyframes cardReveal {
            from { opacity: 0; transform: translateY(18px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .brand-badge {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: var(--brand-forest);
            color: var(--brand-cream);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.55rem;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(49, 95, 67, 0.25);
        }
        .form-label {
            color: var(--brand-charcoal);
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }
        .form-control {
            background-color: #FFFFFF !important;
            border: 1px solid var(--brand-border) !important;
            color: var(--brand-text-primary) !important;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.92rem;
            font-weight: 500;
            transition: all 0.25s ease;
        }
        .form-control:focus {
            border-color: var(--brand-forest) !important;
            box-shadow: 0 0 0 3px rgba(49, 95, 67, 0.16) !important;
        }
        .form-control::placeholder {
            color: #9AA096;
        }
        .btn-editorial {
            background: var(--brand-forest);
            color: var(--brand-cream);
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 0.85rem 1.5rem;
            box-shadow: 0 8px 20px rgba(49, 95, 67, 0.28);
            transition: all 0.25s ease;
        }
        .btn-editorial:hover {
            background: var(--brand-forest-hover);
            color: var(--brand-cream);
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(49, 95, 67, 0.38);
        }
    </style>
</head>
<body>

<div class="editorial-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="index.php" class="text-decoration-none small fw-semibold d-inline-flex align-items-center gap-1.5" style="color:var(--brand-forest);">
            <i class="bi bi-arrow-left"></i> Home
        </a>
        <span class="badge" style="background: rgba(49, 95, 67, 0.1); color: var(--brand-forest); border: 1px solid rgba(49, 95, 67, 0.25); font-family: 'JetBrains Mono', monospace; font-size: 0.72rem;">
            <i class="bi bi-shield-check me-1"></i> SSL 256-Bit
        </span>
    </div>

    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="brand-badge">
            R
        </div>
        <div>
            <h4 class="fw-bold mb-0" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal); letter-spacing:-0.02em;">Sign In</h4>
            <p class="small mb-0" style="color:var(--brand-text-muted);">RTRAE Control Center Access</p>
        </div>
    </div>

    <?php if ($registered): ?>
        <div class="alert py-2.5 small d-flex align-items-center gap-2 mb-3" style="background: rgba(49, 95, 67, 0.1); border: 1px solid rgba(49, 95, 67, 0.25); color: var(--brand-forest); border-radius: 12px;">
            <i class="bi bi-check-circle-fill"></i>
            <span>Account registered successfully! Please sign in.</span>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert py-2.5 small d-flex align-items-center gap-2 mb-3" style="background: rgba(220, 38, 38, 0.08); border: 1px solid rgba(220, 38, 38, 0.25); color: #B91C1C; border-radius: 12px;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label" for="email">Institutional Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($prefillEmail) ?>" placeholder="kartikey@campus.edu.in" required autocomplete="username">
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label mb-0" for="password">Password</label>
                <button type="button" class="btn btn-link p-0 text-decoration-none small" onclick="togglePassVisibility()" style="font-size: 0.78rem; color:var(--brand-forest);">
                    <span id="passToggleText"><i class="bi bi-eye"></i> Show</span>
                </button>
            </div>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
        </div>

        <button type="submit" class="btn btn-editorial w-100">
            Sign In to Control Center <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </form>

    <div class="text-center mt-4 pt-3 border-top small" style="border-color: var(--brand-border) !important;">
        <span style="color:var(--brand-text-muted);">Don't have an account yet?</span>
        <a href="register.php" class="fw-bold text-decoration-none ms-1" style="color: var(--brand-forest);">Register Account</a>
    </div>
</div>

<script>
function togglePassVisibility() {
    const input = document.getElementById('password');
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    document.getElementById('passToggleText').innerHTML = isPass ? '<i class="bi bi-eye-slash"></i> Hide' : '<i class="bi bi-eye"></i> Show';
}
</script>
</body>
</html>