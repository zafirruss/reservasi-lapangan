<?php
require '../connection.php';
require '../auth.php';
requireLogin();

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
$u          = getCurrentUser();

// Stats
$totalReservasi = $pdo->prepare("SELECT COUNT(*) FROM reservasi WHERE user_id=?");
$totalReservasi->execute([$u['id']]);
$totalReservasi = $totalReservasi->fetchColumn();

$totalKonfirmasi = $pdo->prepare("SELECT COUNT(*) FROM reservasi WHERE user_id=? AND status='konfirmasi'");
$totalKonfirmasi->execute([$u['id']]);
$totalKonfirmasi = $totalKonfirmasi->fetchColumn();

$totalSpend = $pdo->prepare("SELECT COALESCE(SUM(total_harga),0) FROM reservasi WHERE user_id=? AND status IN ('konfirmasi','selesai')");
$totalSpend->execute([$u['id']]);
$totalSpend = $totalSpend->fetchColumn();

// Recent reservasi
$stmt = $pdo->prepare("
    SELECT r.*, l.nama as nama_lapangan, l.jenis
    FROM reservasi r
    JOIN lapangan l ON r.lapangan_id = l.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC LIMIT 5
");
$stmt->execute([$u['id']]);
$recentReservasi = $stmt->fetchAll();

include '_layout.php';
?>

<!-- Hero Greeting -->
<div style="background: linear-gradient(135deg, var(--accent) 0%, #4a3ab8 100%); border-radius: 20px; padding: 36px 40px; margin-bottom: 28px; color: #fff; position:relative; overflow:hidden;">
    <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;background:rgba(255,255,255,0.07);border-radius:50%;"></div>
    <div style="position:absolute;bottom:-60px;right:80px;width:150px;height:150px;background:rgba(255,255,255,0.05);border-radius:50%;"></div>
    <div style="position:relative;z-index:1;">
        <div style="font-size:0.85rem;opacity:0.7;margin-bottom:6px;">Selamat datang kembali 👋</div>
        <h1 style="font-family:'Syne',sans-serif;font-size:1.75rem;font-weight:800;margin-bottom:12px;"><?= htmlspecialchars($u['nama']) ?></h1>
        <p style="opacity:0.75;font-size:0.9rem;margin-bottom:24px;">Pesan lapangan favoritmu sekarang dan nikmati pengalaman bermain terbaik.</p>
        <a href="lapangan.php" class="btn" style="background:#fff;color:var(--accent);font-weight:700;">🏟 Lihat Lapangan</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-value"><?= $totalReservasi ?></div>
        <div class="stat-label">Total Reservasi</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(26,127,224,0.1);">✓</div>
        <div class="stat-value"><?= $totalKonfirmasi ?></div>
        <div class="stat-label">Dikonfirmasi</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(22,184,106,0.1);">💰</div>
        <div class="stat-value" style="font-size:1.15rem;"><?= 'Rp '.number_format($totalSpend,0,',','.') ?></div>
        <div class="stat-label">Total Pengeluaran</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">Reservasi Terbaru</div>
        <a href="reservasi.php" class="btn btn-secondary btn-sm">Lihat Semua</a>
    </div>
    <?php if (empty($recentReservasi)): ?>
        <div class="empty-state">
            <div class="icon">📭</div>
            <p>Belum ada reservasi. Yuk booking lapangan sekarang!</p>
            <a href="lapangan.php" class="btn btn-primary">🏟 Pilih Lapangan</a>
        </div>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($recentReservasi as $r):
                $badge = badgeStatus($r['status']);
            ?>
                <tr>
                    <td>
                        <div style="font-weight:600;"><?= htmlspecialchars($r['nama_lapangan']) ?></div>
                        <div style="font-size:0.78rem;color:var(--text-muted)"><?= $r['jenis'] ?></div>
                    </td>
                    <td><?= formatTanggal($r['tanggal']) ?></td>
                    <td style="color:var(--text-muted)"><?= substr($r['jam_mulai'],0,5) ?>–<?= substr($r['jam_selesai'],0,5) ?></td>
                    <td style="font-weight:700;color:var(--accent)"><?= formatRupiah($r['total_harga']) ?></td>
                    <td><span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include '_layout_end.php'; ?>
