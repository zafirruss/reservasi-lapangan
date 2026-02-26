<?php
// =============================================
// AUTH HELPER
// =============================================

function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    startSession();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

function getCurrentUser() {
    startSession();
    return [
        'id'   => $_SESSION['user_id'] ?? null,
        'nama' => $_SESSION['nama'] ?? null,
        'role' => $_SESSION['role'] ?? null,
        'email'=> $_SESSION['email'] ?? null,
    ];
}

function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function formatTanggal($date) {
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $d = date('j', strtotime($date));
    $m = $bulan[(int)date('n', strtotime($date))];
    $y = date('Y', strtotime($date));
    return "$d $m $y";
}

function badgeStatus($status) {
    $map = [
        'pending'    => ['label' => 'Menunggu',   'class' => 'badge-pending'],
        'konfirmasi' => ['label' => 'Dikonfirmasi','class' => 'badge-konfirmasi'],
        'selesai'    => ['label' => 'Selesai',     'class' => 'badge-selesai'],
        'batal'      => ['label' => 'Dibatalkan',  'class' => 'badge-batal'],
    ];
    return $map[$status] ?? ['label' => $status, 'class' => ''];
}
?>
