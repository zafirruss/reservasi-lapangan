<?php
require 'connection.php';
require 'auth.php';
startSession();

// Redirect jika sudah login
if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];

            header('Location: ' . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CourtSync</title>
    
    
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0a0a0f;
            --surface: #13131a;
            --surface2: #1c1c27;
            --border: rgba(255,255,255,0.08);
            --accent: #7c6af7;
            --accent2: #f7a26a;
            --text: #f0f0f5;
            --text-muted: rgba(240,240,245,0.45);
            --success: #5ef0b0;
            --danger: #f06b6b;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .bg-orbs {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.25;
            animation: float 8s ease-in-out infinite;
        }
        .orb-1 { width: 500px; height: 500px; background: var(--accent); top: -100px; left: -100px; animation-delay: 0s; }
        .orb-2 { width: 400px; height: 400px; background: var(--accent2); bottom: -100px; right: -100px; animation-delay: -4s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, 20px); }
        }

        .login-wrapper {
            position: relative; z-index: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 900px;
            max-width: 95vw;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
        }

        .login-left {
            background: linear-gradient(135deg, #1a1640 0%, #0d0d1a 100%);
            padding: 60px 48px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            background: var(--accent);
            border-radius: 50%;
            opacity: 0.1;
            bottom: -80px; left: -80px;
            filter: blur(60px);
        }

        .brand { font-family: 'Trebuchet MS', 'Gill Sans MT', sans-serif; }
        .brand-name { font-size: 2rem; font-weight: 800; letter-spacing: -0.5px; }
        .brand-name span { color: var(--accent); }
        .brand-tagline { font-size: 0.85rem; color: var(--text-muted); margin-top: 4px; }

        .left-hero { z-index: 1; }
        .left-hero h2 {
            font-family: 'Trebuchet MS', 'Gill Sans MT', sans-serif;
            font-size: 2.4rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1px;
            margin-bottom: 16px;
        }
        .left-hero h2 em { color: var(--accent); font-style: normal; }
        .left-hero p { color: var(--text-muted); font-size: 0.95rem; line-height: 1.7; }

        .feature-list { list-style: none; margin-top: 32px; display: flex; flex-direction: column; gap: 12px; }
        .feature-list li {
            display: flex; align-items: center; gap: 10px;
            font-size: 0.875rem; color: var(--text-muted);
        }
        .feature-list li::before {
            content: '✓';
            width: 20px; height: 20px;
            background: var(--accent);
            color: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem;
            flex-shrink: 0;
        }

        .login-right {
            padding: 60px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-right h3 {
            font-family: 'Trebuchet MS', 'Gill Sans MT', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .login-right .subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 36px;
        }
        .login-right .subtitle a {
            color: var(--accent);
            text-decoration: none;
        }

        .alert-error {
            background: rgba(240,107,107,0.12);
            border: 1px solid rgba(240,107,107,0.3);
            color: var(--danger);
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.875rem;
            margin-bottom: 24px;
        }

        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-group input {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 14px 16px;
            border-radius: 10px;
            font-family: 'Segoe UI', system-ui, sans-serif;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(124,106,247,0.15);
        }
        .form-group input::placeholder { color: rgba(240,240,245,0.25); }

        .btn-login {
            width: 100%;
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-family: 'Trebuchet MS', 'Gill Sans MT', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.5px;
            transition: all 0.2s;
            margin-top: 8px;
        }
        .btn-login:hover {
            background: #6b5ce7;
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(124,106,247,0.35);
        }

        .divider {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.8rem;
            margin: 20px 0;
            position: relative;
        }
        .divider::before, .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: calc(50% - 30px);
            height: 1px;
            background: var(--border);
        }
        .divider::before { left: 0; }
        .divider::after { right: 0; }

        .btn-register {
            width: 100%;
            background: transparent;
            color: var(--text);
            border: 1px solid var(--border);
            padding: 14px;
            border-radius: 10px;
            font-family: 'Segoe UI', system-ui, sans-serif;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .btn-register:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .demo-hint {
            margin-top: 24px;
            padding: 14px;
            background: rgba(124,106,247,0.08);
            border: 1px solid rgba(124,106,247,0.2);
            border-radius: 10px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        .demo-hint strong { color: var(--accent); }

        @media (max-width: 640px) {
            .login-wrapper { grid-template-columns: 1fr; }
            .login-left { display: none; }
            .login-right { padding: 40px 28px; }
        }
    </style>
</head>
<body>
<div class="bg-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
</div>

<div class="login-wrapper">
    <div class="login-left">
        <div class="brand">
            <div class="brand-name">Court<span>Sync</span></div>
            <div class="brand-tagline">Sistem Reservasi Lapangan</div>
        </div>
        <div class="left-hero">
            <h2>Booking <em>lapangan</em> jadi lebih mudah.</h2>
            <p>Kelola jadwal, konfirmasi reservasi, dan pantau semua aktivitas lapangan dalam satu platform terpadu.</p>
            <ul class="feature-list">
                <li>Reservasi real-time tanpa antrian</li>
                <li>Konfirmasi instan dari admin</li>
                <li>Riwayat booking lengkap</li>
                <li>Pilihan berbagai jenis lapangan</li>
            </ul>
        </div>
        <div style="color: var(--text-muted); font-size: 0.75rem;">© 2025 CourtSync. All rights reserved.</div>
    </div>

    <div class="login-right">
        <h3>Selamat Datang</h3>
        <p class="subtitle">Belum punya akun? <a href="register.php">Daftar di sini</a></p>

        <?php if ($error): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="nama@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">Masuk →</button>
        </form>

        <div class="divider">atau</div>
        <a href="register.php" class="btn-register">Buat Akun Baru</a>

        <div class="demo-hint">
            <strong>Demo:</strong> Admin → admin@reservasi.com / password<br>
            <strong>Demo:</strong> User → budi@gmail.com / password
        </div>
    </div>
</div>
</body>
</html>
