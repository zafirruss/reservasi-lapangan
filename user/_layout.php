<?php
// user/_layout.php — shared layout for user pages
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'CourtSync' ?></title>
    <style>
        :root {
            --font-display: "Trebuchet MS", "Gill Sans MT", Calibri, sans-serif;
            --font-body: "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            --bg: #f2f2f8;
            --surface: #ffffff;
            --surface2: #ebebf5;
            --border-soft: rgba(0,0,0,0.07);
            --accent: #5448d4;
            --accent-hover: #4338c0;
            --accent-light: rgba(84,72,212,0.10);
            --accent2: #e8703a;
            --text: #1c1c2e;
            --text-soft: #4a4a6a;
            --text-muted: #8888aa;
            --success: #0f9e60;
            --success-bg: rgba(15,158,96,0.10);
            --warning: #c47b00;
            --warning-bg: rgba(196,123,0,0.10);
            --danger: #d63a3a;
            --danger-bg: rgba(214,58,58,0.10);
            --info: #1668c7;
            --info-bg: rgba(22,104,199,0.10);
            --shadow-sm: 0 1px 4px rgba(0,0,0,0.06);
            --shadow: 0 4px 20px rgba(80,70,180,0.08), 0 1px 4px rgba(0,0,0,0.04);
            --shadow-lg: 0 12px 40px rgba(80,70,180,0.14);
            --radius: 16px;
            --radius-sm: 10px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: var(--font-body);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-size: 15px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* NAVBAR */
        .navbar {
            position: sticky; top: 0; z-index: 200;
            height: 66px;
            background: rgba(255,255,255,0.96);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-soft);
            box-shadow: 0 1px 16px rgba(80,70,180,0.07);
            display: flex; align-items: center;
            padding: 0 36px; gap: 32px;
        }
        .navbar-brand {
            font-family: var(--font-display);
            font-size: 1.5rem; font-weight: 700;
            color: var(--text); text-decoration: none;
            letter-spacing: -0.5px;
        }
        .navbar-brand span { color: var(--accent); }
        .navbar-nav { display: flex; align-items: center; gap: 2px; margin-left: 8px; }
        .nav-link {
            padding: 8px 18px; border-radius: 50px;
            color: var(--text-soft); font-size: 0.9rem; font-weight: 500;
            text-decoration: none; transition: all 0.2s;
        }
        .nav-link:hover { color: var(--accent); background: var(--accent-light); }
        .nav-link.active { color: var(--accent); background: var(--accent-light); font-weight: 600; }
        .navbar-right { margin-left: auto; display: flex; align-items: center; gap: 10px; }
        .user-chip {
            display: flex; align-items: center; gap: 10px;
            padding: 6px 16px 6px 6px;
            background: var(--surface2); border: 1px solid var(--border-soft);
            border-radius: 50px; text-decoration: none; color: var(--text); transition: all 0.2s;
        }
        .user-chip:hover { background: var(--accent-light); border-color: var(--accent); }
        .user-chip-avatar {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--accent), #7c6cf7);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.82rem; color: #fff; flex-shrink: 0;
        }
        .user-chip-name { font-size: 0.875rem; font-weight: 600; }
        .btn-logout {
            padding: 8px 18px; border-radius: 50px;
            background: transparent; border: 1.5px solid var(--border-soft);
            color: var(--text-muted); font-size: 0.875rem; font-weight: 500;
            text-decoration: none; transition: all 0.2s; cursor: pointer;
        }
        .btn-logout:hover { border-color: var(--danger); color: var(--danger); background: var(--danger-bg); }

        /* PAGE */
        .page-wrap { max-width: 1160px; margin: 0 auto; padding: 36px 28px 60px; }

        /* HERO */
        .hero-banner {
            background: linear-gradient(135deg, var(--accent) 0%, #3d2fa8 50%, #6c4de8 100%);
            border-radius: 20px; padding: 40px 44px; margin-bottom: 28px;
            color: #fff; position: relative; overflow: hidden;
            box-shadow: 0 8px 32px rgba(84,72,212,0.30);
        }
        .hero-banner::before {
            content: ''; position: absolute; top: -60px; right: -60px;
            width: 240px; height: 240px; background: rgba(255,255,255,0.08); border-radius: 50%;
        }
        .hero-banner::after {
            content: ''; position: absolute; bottom: -80px; right: 120px;
            width: 180px; height: 180px; background: rgba(255,255,255,0.05); border-radius: 50%;
        }
        .hero-content { position: relative; z-index: 1; }
        .hero-greeting { font-size: 0.875rem; opacity: 0.8; margin-bottom: 6px; }
        .hero-name { font-family: var(--font-display); font-size: 2rem; font-weight: 700; margin-bottom: 10px; letter-spacing: -0.5px; }
        .hero-sub { opacity: 0.8; font-size: 0.93rem; margin-bottom: 26px; max-width: 500px; line-height: 1.6; }
        .btn-hero {
            display: inline-flex; align-items: center; gap: 8px;
            background: #fff; color: var(--accent); padding: 12px 24px;
            border-radius: 50px; font-weight: 700; font-size: 0.9rem;
            text-decoration: none; transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        .btn-hero:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }

        /* CARDS */
        .card { background: var(--surface); border: 1px solid var(--border-soft); border-radius: var(--radius); padding: 28px; box-shadow: var(--shadow); }
        .card-sm { padding: 20px; }
        .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; padding-bottom: 16px; border-bottom: 1px solid var(--border-soft); }
        .card-title { font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; letter-spacing: -0.2px; }

        /* STATS */
        .stats-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 28px; }
        .stat-card {
            background: var(--surface); border: 1px solid var(--border-soft);
            border-radius: var(--radius); padding: 24px;
            box-shadow: var(--shadow-sm); transition: all 0.2s; position: relative; overflow: hidden;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow); }
        .stat-card::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; border-radius: 0 0 var(--radius) var(--radius); }
        .stat-card:nth-child(1)::after { background: var(--accent); }
        .stat-card:nth-child(2)::after { background: var(--info); }
        .stat-card:nth-child(3)::after { background: var(--success); }
        .stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 16px; }
        .stat-card:nth-child(1) .stat-icon { background: var(--accent-light); }
        .stat-card:nth-child(2) .stat-icon { background: var(--info-bg); }
        .stat-card:nth-child(3) .stat-icon { background: var(--success-bg); }
        .stat-value { font-family: var(--font-display); font-size: 2rem; font-weight: 700; color: var(--text); line-height: 1; margin-bottom: 6px; letter-spacing: -0.5px; }
        .stat-label { color: var(--text-muted); font-size: 0.82rem; font-weight: 500; }

        /* TABLE */
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 11px 16px; text-align: left;
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            color: var(--text-muted); background: var(--surface2);
        }
        thead th:first-child { border-radius: var(--radius-sm) 0 0 var(--radius-sm); }
        thead th:last-child  { border-radius: 0 var(--radius-sm) var(--radius-sm) 0; }
        tbody tr { border-bottom: 1px solid var(--border-soft); transition: background 0.15s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--surface2); }
        td { padding: 15px 16px; font-size: 0.875rem; vertical-align: middle; }

        /* BADGES */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; border-radius: 50px;
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px;
        }
        .badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
        .badge-pending    { background: var(--warning-bg); color: var(--warning); }
        .badge-konfirmasi { background: var(--info-bg);    color: var(--info); }
        .badge-selesai    { background: var(--success-bg); color: var(--success); }
        .badge-batal      { background: var(--danger-bg);  color: var(--danger); }

        /* BUTTONS */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 22px; border-radius: 50px;
            font-family: var(--font-body); font-size: 0.875rem; font-weight: 600;
            cursor: pointer; text-decoration: none; border: none; transition: all 0.2s;
        }
        .btn-primary { background: var(--accent); color: #fff; box-shadow: 0 4px 14px rgba(84,72,212,0.28); }
        .btn-primary:hover { background: var(--accent-hover); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(84,72,212,0.36); }
        .btn-secondary { background: var(--surface2); color: var(--text-soft); border: 1.5px solid var(--border-soft); }
        .btn-secondary:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
        .btn-danger { background: var(--danger-bg); color: var(--danger); border: 1.5px solid rgba(214,58,58,0.2); }
        .btn-danger:hover { background: rgba(214,58,58,0.18); }
        .btn-sm { padding: 7px 16px; font-size: 0.8rem; }

        /* FORM */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-soft); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.7px; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; background: var(--surface2); border: 1.5px solid var(--border-soft);
            color: var(--text); padding: 13px 16px; border-radius: var(--radius-sm);
            font-family: var(--font-body); font-size: 0.925rem; outline: none; transition: all 0.2s; appearance: none;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: var(--accent); background: var(--surface); box-shadow: 0 0 0 3px rgba(84,72,212,0.10);
        }
        .form-group input::placeholder, .form-group textarea::placeholder { color: var(--text-muted); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }

        /* ALERTS */
        .alert { padding: 14px 18px; border-radius: var(--radius-sm); font-size: 0.875rem; margin-bottom: 24px; font-weight: 500; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: var(--success-bg); border: 1.5px solid rgba(15,158,96,0.25); color: var(--success); }
        .alert-error   { background: var(--danger-bg);  border: 1.5px solid rgba(214,58,58,0.25);  color: var(--danger); }

        /* EMPTY STATE */
        .empty-state { text-align: center; padding: 64px 24px; }
        .empty-state .icon { font-size: 3.5rem; margin-bottom: 18px; display: block; opacity: 0.5; }
        .empty-state p { font-size: 0.95rem; margin-bottom: 24px; color: var(--text-soft); }

        /* LAPANGAN GRID */
        .lapangan-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px,1fr)); gap: 22px; }
        .lapangan-card { background: var(--surface); border: 1px solid var(--border-soft); border-radius: 20px; overflow: hidden; box-shadow: var(--shadow-sm); transition: all 0.25s; }
        .lapangan-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
        .lapangan-thumb { height: 150px; display: flex; align-items: center; justify-content: center; font-size: 4rem; }
        .lapangan-thumb.futsal    { background: linear-gradient(135deg,#1e3c72,#2a5298); }
        .lapangan-thumb.badminton { background: linear-gradient(135deg,#0f4c35,#1a8c6a); }
        .lapangan-thumb.basket    { background: linear-gradient(135deg,#8b1a1a,#c94b1a); }
        .lapangan-thumb.tenis     { background: linear-gradient(135deg,#2d5a1b,#5a9e2f); }
        .lapangan-thumb.voli      { background: linear-gradient(135deg,#7a4f00,#c98a00); }
        .lapangan-thumb.default   { background: linear-gradient(135deg,#3a2880,#6b4de8); }
        .lapangan-body { padding: 22px; }
        .lapangan-jenis { font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.2px; color: var(--accent); background: var(--accent-light); padding: 4px 12px; border-radius: 50px; display: inline-block; margin-bottom: 10px; }
        .lapangan-name { font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; margin-bottom: 8px; letter-spacing: -0.2px; }
        .lapangan-desc { font-size: 0.83rem; color: var(--text-muted); line-height: 1.55; margin-bottom: 18px; }
        .lapangan-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 18px; border-top: 1px solid var(--border-soft); }
        .lapangan-price { font-family: var(--font-display); font-weight: 700; color: var(--accent); font-size: 1.1rem; }
        .lapangan-price-unit { font-size: 0.75rem; color: var(--text-muted); font-weight: 400; }

        /* FILTER PILLS */
        .filter-bar { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px; }
        .filter-pill { padding: 8px 18px; border-radius: 50px; font-size: 0.84rem; font-weight: 600; text-decoration: none; border: 1.5px solid var(--border-soft); color: var(--text-soft); background: var(--surface); transition: all 0.18s; }
        .filter-pill:hover { border-color: var(--accent); color: var(--accent); }
        .filter-pill.active { background: var(--accent); color: #fff; border-color: var(--accent); box-shadow: 0 4px 12px rgba(84,72,212,0.25); }

        /* BOOKING CARD */
        .booking-card { background: var(--surface); border: 2px solid var(--accent); border-radius: 20px; padding: 28px; margin-bottom: 32px; box-shadow: 0 8px 32px rgba(84,72,212,0.12); }
        .price-summary { background: var(--accent-light); border-radius: var(--radius-sm); padding: 18px 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }

        /* SECTION HEADING */
        .section-heading { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; }
        .section-title { font-family: var(--font-display); font-size: 1.35rem; font-weight: 700; letter-spacing: -0.3px; }

        @media (max-width: 768px) {
            .navbar { padding: 0 18px; gap: 16px; }
            .navbar-nav { display: none; }
            .page-wrap { padding: 22px 16px 48px; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .form-row { grid-template-columns: 1fr; }
            .hero-banner { padding: 28px 24px; }
            .hero-name { font-size: 1.55rem; }
        }
    </style>
</head>
<body>
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">Court<span>Sync</span></a>
    <div class="navbar-nav">
        <a href="dashboard.php" class="nav-link <?= ($activePage??'')=='dashboard' ? 'active' : '' ?>">Beranda</a>
        <a href="lapangan.php"  class="nav-link <?= ($activePage??'')=='lapangan'  ? 'active' : '' ?>">Lapangan</a>
        <a href="reservasi.php" class="nav-link <?= ($activePage??'')=='reservasi'  ? 'active' : '' ?>">Reservasi Saya</a>
    </div>
    <?php $u = getCurrentUser(); ?>
    <div class="navbar-right">
        <div class="user-chip">
            <div class="user-chip-avatar"><?= strtoupper(substr($u['nama'], 0, 1)) ?></div>
            <span class="user-chip-name"><?= htmlspecialchars(explode(' ', $u['nama'])[0]) ?></span>
        </div>
        <a href="../logout.php" class="btn-logout">Keluar</a>
    </div>
</nav>
<div class="page-wrap">
