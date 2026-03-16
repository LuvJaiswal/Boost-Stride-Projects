<?php
require_once 'core/Config.php';
require_once 'core/Auth.php';

use Core\Auth;

session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'];

    $result = Auth::login($user, $pass, $ip);

    if (isset($result['success'])) {
        header("Location: index.php");
        exit();
    } else {
        $error = $result['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Boost Stride Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }
        body::before, body::after {
            content: ''; position: fixed; border-radius: 50%; opacity: 0.15; pointer-events: none;
        }
        body::before { width: 500px; height: 500px; background: radial-gradient(circle, #6366f1, transparent); top: -150px; left: -150px; }
        body::after  { width: 400px; height: 400px; background: radial-gradient(circle, #7c3aed, transparent); bottom: -100px; right: -100px; }

        .login-wrapper { width: 100%; max-width: 440px; position: relative; z-index: 10; }

        .login-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 28px; padding: 3rem 2.5rem;
            box-shadow: 0 32px 64px rgba(0,0,0,0.4);
        }

        .brand-icon {
            width: 64px; height: 64px; background: var(--primary-gradient);
            border-radius: 20px; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem; box-shadow: 0 12px 24px rgba(79,70,229,0.35);
        }
        .brand-icon i { color: white; font-size: 1.6rem; }

        .login-title { color: #fff; font-size: 1.75rem; font-weight: 800; text-align: center; letter-spacing: -0.5px; margin-bottom: 0.35rem; }
        .login-subtitle { color: rgba(255,255,255,0.5); text-align: center; font-size: 0.9rem; margin-bottom: 2.25rem; }

        .form-label { color: rgba(255,255,255,0.7); font-size: 0.82rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 0.4rem; display: block; }

        .input-group-text {
            background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);
            border-right: none; color: rgba(255,255,255,0.5); border-radius: 14px 0 0 14px; padding: 0.85rem 1.1rem;
        }
        .form-control {
            background: rgba(255,255,255,0.06) !important; border: 1px solid rgba(255,255,255,0.12);
            border-left: none; color: #fff !important; font-size: 0.95rem; padding: 0.85rem 1.1rem;
            border-radius: 0 14px 14px 0; transition: all 0.3s;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.3); }
        .form-control:focus {
            background: rgba(255,255,255,0.1) !important; border-color: rgba(99,102,241,0.7) !important;
            box-shadow: 0 0 0 4px rgba(99,102,241,0.15) !important; color: #fff !important;
        }
        .input-group:focus-within .input-group-text { border-color: rgba(99,102,241,0.7); }

        .btn-login {
            width: 100%; padding: 1rem; background: var(--primary-gradient); border: none;
            border-radius: 16px; color: #fff; font-family: 'Outfit', sans-serif;
            font-size: 1rem; font-weight: 700; letter-spacing: 0.3px; cursor: pointer;
            transition: all 0.3s; box-shadow: 0 8px 20px rgba(79,70,229,0.3); margin-top: 0.5rem;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(79,70,229,0.45); }
        .btn-login:active { transform: translateY(0); }

        .login-alert {
            background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5; border-radius: 14px; font-size: 0.88rem;
            padding: 0.85rem 1.1rem; margin-bottom: 1.5rem;
        }
        .footer-note { color: rgba(255,255,255,0.25); text-align: center; font-size: 0.75rem; margin-top: 2rem; }
        .mb-custom { margin-bottom: 1.25rem; }

        @media (max-width: 575.98px) {
            .login-card { padding: 2rem 1.5rem; border-radius: 22px; }
            .login-title { font-size: 1.45rem; }
            .form-control, .input-group-text { font-size: 16px !important; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="brand-icon">
                <i class="fas fa-rocket"></i>
            </div>
            <h1 class="login-title">Welcome Back</h1>
            <p class="login-subtitle">Sign in to your admin command center</p>

            <?php if ($error): ?>
                <div class="login-alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" autocomplete="on">
                <div class="mb-custom">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control"
                               placeholder="Enter your username"
                               autocomplete="username" required>
                    </div>
                </div>
                <div class="mb-custom">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control"
                               placeholder="Enter your password"
                               autocomplete="current-password" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-right-to-bracket me-2"></i>Sign In to Dashboard
                </button>
            </form>

            <p class="footer-note">Boost Stride Admin &copy; <?php echo date('Y'); ?> &mdash; Protected Access</p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
