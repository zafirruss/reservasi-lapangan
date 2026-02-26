<?php
// admin/_layout.php — shared layout for admin pages
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin' ?> — CourtSync</title>
    <style>
        :root {
            --font-display: "Trebuchet MS", "Gill Sans MT", Calibri, sans-serif;
            --font-body: "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            --bg: #07070d;
            --surface: #10101a;
            --surface2: #17172a;
            --surface3: #1e1e32;
            --border: rgba(255,255,255,0.08);
            --border-soft: rgba(255,255,255,0.05);
            --accent: #7060f0;
            --accent-hover: #5e4fd4;
            --accent-light: rgba(112,96,240,0.14);
            --accent2: #f0a060;
            --text: #e8e8f4;
            --text-soft: rgba(232,232,244,0.7);
            --text-muted: rgba(232,232,244,0.38);
            --success: #3dd68c;
            --success-bg: rgba(61,214,140,0.12);
            --warning: #f0c060;
            --warning-bg: rgba(240,192,96,0.12);
            --danger: #f06060;
            --danger-bg: rgba(240,96,96,0.12);
            --info: #60a8f0;
            --info-bg: rgba(96,168,240,0.12);
            --shadow: 0 4px 24px rgba(0,0,0,0.4);
            --radius: 14px;
            --radius-sm: 9px;
            --sidebar-w: 255px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: var(--font-body);
            background: var(--bg);
            color: var(--text);
            display: flex; min-height: 100vh;
            font-size: 15px; line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex; flex-direction: column;
            position: fixed; top: 0; bottom: 0; left: 0;
            z-index: 100; overflow-y: auto;
        }
        .sidebar-brand {
            padding: 26px 22px 22px;
            border-bottom: 1px solid var(--border);
        }
        .brand-name { font-family: var(--font-display); font-size: 1.4rem; font-weight: 700; letter-spacing: -0.3px; }
        .brand-name span { color: var(--accent); }
        .brand-badge {
            display: inline-block;
            background: var(--accent-light); color: var(--accent);
            font-size: 0.62rem; font-weight: 700; letter-spacing: 1.2px;
            text-transform: uppercase; padding: 3px 10px;
            border-radius: 20px; margin-top: 5px;
        }

        .sidebar-nav { padding: 14px 10px; flex: 1; }
        .nav-section-label {
            font-size: 0.62rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.8px;
            color: var(--text-muted);
            padding: 10px 12px 5px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 11px;
            padding: 10px 14px; border-radius: 10px;
            color: var(--text-muted); text-decoration: none;
            font-size: 0.9rem; font-weight: 500;
            transition: all 0.18s; margin-bottom: 2px;
        }
        .nav-item:hover { color: var(--text); background: var(--surface2); }
        .nav-item.active { color: var(--accent); background: var(--accent-light); font-weight: 600; }
        .nav-icon { width: 20px; text-align: center; font-size: 1rem; flex-shrink: 0; }

        .sidebar-user {
            padding: 14px;
            border-top: 1px solid var(--border);
            display: flex; align-items: center; gap: 11px;
        }
        .user-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent), #a080ff);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-display); font-weight: 700; font-size: 0.9rem; color: #fff; flex-shrink: 0;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: 0.84rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 0.7rem; color: var(--text-muted); }
        .logout-btn { color: var(--text-muted); text-decoration: none; font-size: 1.1rem; transition: color 0.18s; padding: 4px; }
        .logout-btn:hover { color: var(--danger); }

        /* MAIN */
        .main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .topbar {
            position: sticky; top: 0; z-index: 50;
            background: rgba(7,7,13,0.88);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
            padding: 16px 32px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .topbar-title { font-family: var(--font-display); font-size: 1.2rem; font-weight: 700; letter-spacing: -0.3px; }
        .topbar-right { display: flex; align-items: center; gap: 14px; color: var(--text-muted); font-size: 0.84rem; }
        .content { padding: 30px 32px; flex: 1; }

        /* STAT CARDS */
        .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 22px; position: relative; overflow: hidden;
        }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
        .stat-card.purple::before { background: var(--accent); }
        .stat-card.orange::before { background: var(--accent2); }
        .stat-card.green::before  { background: var(--success); }
        .stat-card.blue::before   { background: var(--info); }
        .stat-icon {
            width: 42px; height: 42px; border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; margin-bottom: 14px;
        }
        .stat-card.purple .stat-icon { background: var(--accent-light); }
        .stat-card.orange .stat-icon { background: rgba(240,160,96,0.14); }
        .stat-card.green  .stat-icon { background: var(--success-bg); }
        .stat-card.blue   .stat-icon { background: var(--info-bg); }
        .stat-value { font-family: var(--font-display); font-size: 1.8rem; font-weight: 700; line-height: 1; letter-spacing: -0.5px; }
        .stat-label { color: var(--text-muted); font-size: 0.79rem; margin-top: 5px; }

        /* CARD */
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; }
        .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--border-soft); }
        .card-title { font-family: var(--font-display); font-size: 1rem; font-weight: 700; }

        /* TABLE */
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 10px 14px; text-align: left;
            font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            color: var(--text-muted); background: var(--surface2);
        }
        thead th:first-child { border-radius: var(--radius-sm) 0 0 var(--radius-sm); }
        thead th:last-child  { border-radius: 0 var(--radius-sm) var(--radius-sm) 0; }
        tbody tr { border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.15s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--surface2); }
        td { padding: 14px; font-size: 0.875rem; vertical-align: middle; }

        /* BADGES */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 11px; border-radius: 20px;
            font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
        .badge-pending    { background: var(--warning-bg); color: var(--warning); }
        .badge-konfirmasi { background: var(--info-bg);    color: var(--info); }
        .badge-selesai    { background: var(--success-bg); color: var(--success); }
        .badge-batal      { background: var(--danger-bg);  color: var(--danger); }
        .badge-tersedia   { background: var(--success-bg); color: var(--success); }
        .badge-tidak      { background: var(--danger-bg);  color: var(--danger); }
        .badge-admin      { background: var(--accent-light); color: var(--accent); }
        .badge-user       { background: var(--border); color: var(--text-muted); }

        /* BUTTONS */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; border-radius: 50px;
            font-family: var(--font-body); font-size: 0.84rem; font-weight: 600;
            cursor: pointer; text-decoration: none; border: none; transition: all 0.18s;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-hover); box-shadow: 0 5px 18px rgba(112,96,240,0.35); }
        .btn-secondary { background: var(--surface2); color: var(--text); border: 1px solid var(--border); }
        .btn-secondary:hover { border-color: var(--accent); color: var(--accent); }
        .btn-success { background: var(--success-bg); color: var(--success); border: 1px solid rgba(61,214,140,0.3); }
        .btn-success:hover { background: rgba(61,214,140,0.2); }
        .btn-danger  { background: var(--danger-bg);  color: var(--danger);  border: 1px solid rgba(240,96,96,0.3); }
        .btn-danger:hover  { background: rgba(240,96,96,0.22); }
        .btn-warning { background: var(--warning-bg); color: var(--warning); border: 1px solid rgba(240,192,96,0.3); }
        .btn-warning:hover { background: rgba(240,192,96,0.22); }
        .btn-sm { padding: 6px 14px; font-size: 0.78rem; }

        /* FORM */
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 0.73rem; font-weight: 700; color: var(--text-muted); margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.7px; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; background: var(--surface2); border: 1px solid var(--border);
            color: var(--text); padding: 11px 14px; border-radius: var(--radius-sm);
            font-family: var(--font-body); font-size: 0.9rem; outline: none; transition: border-color 0.2s;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: var(--accent); box-shadow: 0 0 0 3px rgba(112,96,240,0.12);
        }
        .form-group input::placeholder, .form-group textarea::placeholder { color: rgba(232,232,244,0.22); }
        .form-group select option { background: var(--surface2); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }

        /* ALERTS */
        .alert { padding: 12px 16px; border-radius: var(--radius-sm); font-size: 0.875rem; margin-bottom: 22px; display: flex; align-items: center; gap: 10px; font-weight: 500; }
        .alert-success { background: var(--success-bg); border: 1px solid rgba(61,214,140,0.3); color: var(--success); }
        .alert-error   { background: var(--danger-bg);  border: 1px solid rgba(240,96,96,0.3);   color: var(--danger); }

        /* EMPTY STATE */
        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
        .empty-state .icon { font-size: 3rem; margin-bottom: 14px; opacity: 0.4; }
        .empty-state p { font-size: 0.9rem; }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2,1fr); }
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; }
            .content { padding: 20px; }
        }
    </style>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-name">Court<span>Sync</span></div>
        <div class="brand-badge">Admin Panel</div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Menu Utama</div>
        <a href="dashboard.php" class="nav-item <?= ($activePage??'')=='dashboard' ? 'active' : '' ?>"><span class="nav-icon">⊞</span> Dashboard</a>
        <a href="reservasi.php" class="nav-item <?= ($activePage??'')=='reservasi'  ? 'active' : '' ?>"><span class="nav-icon">📅</span> Reservasi</a>
        <a href="lapangan.php"  class="nav-item <?= ($activePage??'')=='lapangan'   ? 'active' : '' ?>"><span class="nav-icon">🏟</span> Kelola Lapangan</a>
        <div class="nav-section-label" style="margin-top:10px;">Manajemen</div>
        <a href="users.php" class="nav-item <?= ($activePage??'')=='users' ? 'active' : '' ?>"><span class="nav-icon">👥</span> Data Pengguna</a>
    </nav>
    <?php $u = getCurrentUser(); ?>
    <div class="sidebar-user">
        <div class="user-avatar"><?= strtoupper(substr($u['nama'], 0, 1)) ?></div>
        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars($u['nama']) ?></div>
            <div class="user-role">Administrator</div>
        </div>
        <a href="../logout.php" class="logout-btn" title="Logout">⏻</a>
    </div>
</aside>
<main class="main">
    <div class="topbar">
        <div class="topbar-title"><?= $pageTitle ?? 'Dashboard' ?></div>
        <div class="topbar-right"><span>📅 <?= date('d M Y') ?></span></div>
    </div>
    <div class="content">
