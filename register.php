<?php
require 'connection.php';
require 'auth.php';
startSession();

if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $no_hp    = trim($_POST['no_hp'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (empty($nama) || empty($email) || empty($password)) {
        $error = 'Nama, email, dan password wajib diisi.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'Email sudah terdaftar. Silakan gunakan email lain.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt   = $pdo->prepare("INSERT INTO users (nama, email, password, no_hp) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nama, $email, $hashed, $no_hp])) {
                $success = 'Akun berhasil dibuat! Silakan login.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — CourtSync</title>
    
    
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #0a0a0f; --surface: #13131a; --surface2: #1c1c27;
            --border: rgba(255,255,255,0.08); --accent: #7c6af7; --accent2: #f7a26a;
            --text: #f0f0f5; --text-muted: rgba(240,240,245,0.45);
            --success: #5ef0b0; --danger: #f06b6b;
        }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg); color: var(--text);
            min-height: 100vh; display: flex; align-items: center;
            justify-content: center; padding: 40px 20px;
        }
        .bg-orbs { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
        .orb { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.2; }
        .orb-1 { width: 400px; height: 400px; background: var(--accent); top: 10%; right: -100px; }
        .orb-2 { width: 350px; height: 350px; background: var(--accent2); bottom: 10%; left: -80px; }

        .register-card {
            position: relative; z-index: 1;
            width: 500px; max-width: 100%;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
        }
        .brand { font-family: 'Trebuchet MS', 'Gill Sans MT', sans-serif; margin-bottom: 32px; }
        .brand-name { font-size: 1.5rem; font-weight: 800; }
        .brand-name span { color: var(--accent); }

        h3 { font-family: 'Trebuchet MS', 'Gill Sans MT', sans-serif; font-size: 1.6rem; font-weight: 700; margin-bottom: 6px; }
        .subtitle { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 32px; }
        .subtitle a { color: var(--accent); text-decoration: none; }

        .alert { padding: 12px 16px; border-radius: 10px; font-size: 0.875rem; margin-bottom: 24px; }
        .alert-error { background: rgba(240,107,107,0.12); border: 1px solid rgba(240,107,107,0.3); color: var(--danger); }
        .alert-success { background: rgba(94,240,176,0.12); border: 1px solid rgba(94,240,176,0.3); color: var(--success); }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block; font-size: 0.78rem; font-weight: 500;
            color: var(--text-muted); margin-bottom: 7px;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .form-group input {
            width: 100%; background: var(--surface2); border: 1px solid var(--border);
            color: var(--text); padding: 13px 15px; border-radius: 10px;
            font-family: 'Segoe UI', system-ui, sans-serif; font-size: 0.92rem; outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(124,106,247,0.15); }
        .form-group input::placeholder { color: rgba(240,240,245,0.25); }

        .btn-submit {
            width: 100%; background: var(--accent); color: #fff; border: none;
            padding: 15px; border-radius: 10px; font-family: 'Trebuchet MS', 'Gill Sans MT', sans-serif;
            font-size: 1rem; font-weight: 700; cursor: pointer; letter-spacing: 0.5px;
            transition: all 0.2s; margin-top: 8px;
        }
        .btn-submit:hover { background: #6b5ce7; transform: translateY(-1px); box-shadow: 0 8px 25px rgba(124,106,247,0.35); }

        @media (max-width: 500px) {
            .register-card { padding: 32px 24px; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="bg-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
</div>

<div class="register-card">
    <div class="brand">
        <div class="brand-name">Court<span>Sync</span></div>
    </div>
    <h3>Buat Akun</h3>
    <p class="subtitle">Sudah punya akun? <a href="login.php">Login di sini</a></p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?> <a href="login.php" style="color:inherit;font-weight:600;">Login →</a></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST" action="">
        <div class="form-row">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Nama Anda" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>No. HP</label>
                <input type="text" name="no_hp" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Alamat Email</label>
            <input type="email" name="email" placeholder="nama@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="min. 6 karakter" required>
            </div>
            <div class="form-group">
                <label>Konfirmasi</label>
                <input type="password" name="confirm_password" placeholder="ulangi password" required>
            </div>
        </div>
        <button type="submit" class="btn-submit">Daftar Sekarang →</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
