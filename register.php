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

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $full_name        = trim($_POST['full_name'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $raw_phone        = trim($_POST['phone_number'] ?? '');
    $role             = $_POST['role'] ?? 'Admin';
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Automatically prepend +91 to the 10-digit number entered by the user
    $phone = '+91' . preg_replace('/[^0-9]/', '', $raw_phone);

    $phoneRegex = '/^\+91[6-9]\d{9}$/';
    $passRegex  = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

    if (!$full_name || !$email || !$raw_phone || !$password || !$confirm_password) {
        $error = "All fields, including password confirmation, are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match. Please ensure both password fields are identical.";
    } elseif (!preg_match($phoneRegex, $phone)) {
        $error = "Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.";
    } elseif (!preg_match($passRegex, $password)) {
        $error = "Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.";
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone_number, password_hash, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $email, $phone, $hash, $role]);
            header('Location: login.php?registered=1&email=' . urlencode($email));
            exit;
        } catch (PDOException $e) {
            $error = "This email address is already registered. Please sign in.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Enterprise Account | RTRAE Engine</title>
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
            padding: 2.5rem 1rem;
            position: relative;
        }
        .editorial-card {
            background: var(--brand-cream);
            border: 1px solid var(--brand-border);
            border-radius: 24px;
            max-width: 520px;
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
        .form-control, .form-select {
            background-color: #FFFFFF !important;
            border: 1px solid var(--brand-border) !important;
            color: var(--brand-text-primary) !important;
            border-radius: 12px;
            padding: 0.72rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.25s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand-forest) !important;
            box-shadow: 0 0 0 3px rgba(49, 95, 67, 0.16) !important;
        }
        .form-control::placeholder {
            color: #9AA096;
        }
        .input-group-text {
            background-color: rgba(49, 95, 67, 0.08);
            border: 1px solid var(--brand-border);
            color: var(--brand-forest);
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.88rem;
            border-radius: 12px 0 0 12px;
        }
        .requirement-list {
            font-size: 0.76rem;
            background: rgba(49, 95, 67, 0.05);
            border: 1px solid var(--brand-border);
            border-radius: 12px;
            padding: 0.75rem 1rem;
        }
        .requirement-list li {
            color: var(--brand-text-muted);
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s ease;
        }
        .requirement-list li.valid {
            color: var(--brand-forest);
            font-weight: 600;
        }
        .match-badge {
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.25s ease;
        }
        .match-success {
            background: rgba(49, 95, 67, 0.12);
            color: var(--brand-forest);
            border: 1px solid rgba(49, 95, 67, 0.3);
        }
        .match-error {
            background: rgba(220, 38, 38, 0.1);
            color: #DC2626;
            border: 1px solid rgba(220, 38, 38, 0.3);
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
        .btn-editorial:hover:not(:disabled) {
            background: var(--brand-forest-hover);
            color: var(--brand-cream);
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(49, 95, 67, 0.38);
        }
        .btn-editorial:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            transform: none;
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
            <i class="bi bi-shield-check me-1"></i> RTRAE Security
        </span>
    </div>

    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="brand-badge">
            R
        </div>
        <div>
            <h4 class="fw-bold mb-0" style="font-family:'Playfair Display',serif; color:var(--brand-charcoal); letter-spacing:-0.02em;">Create Account</h4>
            <p class="small mb-0" style="color:var(--brand-text-muted);">Join RTRAE Campus Resource Engine</p>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert py-2.5 small d-flex align-items-center gap-2 mb-3" style="background: rgba(220, 38, 38, 0.08); border: 1px solid rgba(220, 38, 38, 0.25); color: #B91C1C; border-radius: 12px;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" id="registerForm" onsubmit="return validateBeforeSubmit(event)">
        <div class="mb-3">
            <label class="form-label" for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" class="form-control" placeholder="e.g. Kartikey Singh" required autocomplete="name">
        </div>

        <div class="mb-3">
            <label class="form-label" for="email">Institutional Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="kartikey@campus.edu.in" required autocomplete="email">
        </div>

        <div class="mb-3">
            <label class="form-label" for="phone_number">Mobile Phone</label>
            <div class="input-group">
                <span class="input-group-text fw-bold">+91</span>
                <input type="tel" id="phone_number" name="phone_number" class="form-control" placeholder="9876543210" maxlength="10" pattern="[6-9][0-9]{9}" required autocomplete="tel">
            </div>
            <div class="text-muted mt-1" style="font-size: 0.72rem;">Enter 10-digit Indian mobile number (begins with 6, 7, 8, or 9).</div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="role">Role / Designation</label>
            <select id="role" name="role" class="form-select">
                <option value="Admin">System Administrator</option>
                <option value="Manager">Resource &amp; Facility Manager</option>
                <option value="Coordinator">Academic Operations Coordinator</option>
            </select>
        </div>

        <!-- Password Field -->
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label mb-0" for="password">Create Password</label>
                <button type="button" class="btn btn-link p-0 text-decoration-none small" onclick="togglePasswordVisibility('password', this)" style="font-size: 0.78rem; color:var(--brand-forest);">
                    <i class="bi bi-eye"></i> Show
                </button>
            </div>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" oninput="onPasswordInput()" required autocomplete="new-password">
            
            <ul class="requirement-list list-unstyled mt-2 mb-0">
                <li id="req-length"><i class="bi bi-circle"></i> At least 8 characters long</li>
                <li id="req-upper"><i class="bi bi-circle"></i> At least one uppercase letter (A-Z)</li>
                <li id="req-lower"><i class="bi bi-circle"></i> At least one lowercase letter (a-z)</li>
                <li id="req-number"><i class="bi bi-circle"></i> At least one number (0-9)</li>
                <li id="req-special"><i class="bi bi-circle"></i> At least one special symbol (!@#$%^&*)</li>
            </ul>
        </div>

        <!-- Password Confirmation Field with Real-Time Matching -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label mb-0" for="confirm_password">Confirm Password</label>
                <button type="button" class="btn btn-link p-0 text-decoration-none small" onclick="togglePasswordVisibility('confirm_password', this)" style="font-size: 0.78rem; color:var(--brand-forest);">
                    <i class="bi bi-eye"></i> Show
                </button>
            </div>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="••••••••" oninput="checkPasswordMatch()" required autocomplete="new-password">
            <div id="match-feedback" class="mt-2" style="min-height: 24px;"></div>
        </div>

        <button type="submit" id="submitBtn" class="btn btn-editorial w-100">
            Create Secure Account <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </form>

    <div class="text-center mt-4 pt-3 border-top small" style="border-color: var(--brand-border) !important;">
        <span style="color:var(--brand-text-muted);">Already registered?</span>
        <a href="login.php" class="fw-bold text-decoration-none ms-1" style="color: var(--brand-forest);">Sign in to Control Center</a>
    </div>
</div>

<script>
function togglePasswordVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    if (!input) return;
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    btn.innerHTML = isPass ? '<i class="bi bi-eye-slash"></i> Hide' : '<i class="bi bi-eye"></i> Show';
}

function onPasswordInput() {
    const p = document.getElementById('password').value;
    const len = p.length >= 8;
    const upper = /[A-Z]/.test(p);
    const lower = /[a-z]/.test(p);
    const num = /\d/.test(p);
    const special = /[\W_]/.test(p);

    updateRule('req-length', len);
    updateRule('req-upper', upper);
    updateRule('req-lower', lower);
    updateRule('req-number', num);
    updateRule('req-special', special);

    checkPasswordMatch();
}

function updateRule(elementId, isValid) {
    const el = document.getElementById(elementId);
    if (!el) return;
    el.className = isValid ? 'valid' : '';
    const icon = el.querySelector('i');
    if (icon) {
        icon.className = isValid ? 'bi bi-check-circle-fill' : 'bi bi-circle';
    }
}

function checkPasswordMatch() {
    const p = document.getElementById('password').value;
    const cp = document.getElementById('confirm_password').value;
    const fb = document.getElementById('match-feedback');
    const input = document.getElementById('confirm_password');

    if (!cp) {
        fb.innerHTML = '<span class="text-muted" style="font-size: 0.74rem;">Re-enter the password above to verify matching.</span>';
        input.style.borderColor = 'var(--brand-border)';
        return;
    }

    if (p === cp) {
        fb.innerHTML = '<span class="match-badge match-success"><i class="bi bi-check-lg"></i> Passwords match perfectly</span>';
        input.style.borderColor = '#315F43';
    } else {
        fb.innerHTML = '<span class="match-badge match-error"><i class="bi bi-x-lg"></i> Passwords do not match</span>';
        input.style.borderColor = '#DC2626';
    }
}

function validateBeforeSubmit(e) {
    const p = document.getElementById('password').value;
    const cp = document.getElementById('confirm_password').value;
    if (p !== cp) {
        e.preventDefault();
        alert('Passwords do not match. Please verify that both password fields are identical.');
        document.getElementById('confirm_password').focus();
        return false;
    }
    return true;
}
</script>
</body>
</html>