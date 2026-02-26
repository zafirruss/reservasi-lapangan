<?php
require '../connection.php';
require '../auth.php';
requireLogin();
requireAdmin();

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';

// Stats
$totalLapangan  = $pdo->query("SELECT COUNT(*) FROM lapangan")->fetchColumn();
$totalReservasi = $pdo->query("SELECT COUNT(*) FROM reservasi")->fetchColumn();
$totalPending   = $pdo->query("SELECT COUNT(*) FROM reservasi WHERE status='pending'")->fetchColumn();
$totalUsers     = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$pendapatan     = $pdo->query("SELECT COALESCE(SUM(total_harga),0) FROM reservasi WHERE status IN ('konfirmasi','selesai')")->fetchColumn();

// Reservasi terbaru
$recentStmt = $pdo->query("
    SELECT r.*, u.nama as nama_user, l.nama as nama_lapangan
    FROM reservasi r
    JOIN users u ON r.user_id = u.id
    JOIN lapangan l ON r.lapangan_id = l.id
    ORDER BY r.created_at DESC LIMIT 8
");
$recentReservasi = $recentStmt->fetchAll();

include '_layout.php';
?>
<div class="stats-grid">
    <div class="stat-card purple">
        <div class="stat-icon">📅</div>
        <div class="stat-value"><?= $totalReservasi ?></div>
        <div class="stat-label">Total Reservasi</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon">⏳</div>
        <div class="stat-value"><?= $totalPending ?></div>
        <div class="stat-label">Menunggu Konfirmasi</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon">💰</div>
        <div class="stat-value" style="font-size:1.2rem;"><?= 'Rp ' . number_format($pendapatan,0,',','.') ?></div>
        <div class="stat-label">Total Pendapatan</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon">👥</div>
        <div class="stat-value"><?= $totalUsers ?></div>
        <div class="stat-label">Total Pengguna</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">Reservasi Terbaru</div>
        <a href="reservasi.php" class="btn btn-secondary btn-sm">Lihat Semua</a>
    </div>
    <div class="table-wrapper">
        <?php if (empty($recentReservasi)): ?>
            <div class="empty-state"><div class="icon">📭</div><p>Belum ada reservasi.</p></div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pelanggan</th>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($recentReservasi as $i => $r):
                $badge = badgeStatus($r['status']);
            ?>
                <tr>
                    <td style="color:var(--text-muted)"><?= $i+1 ?></td>
                    <td style="font-weight:500"><?= htmlspecialchars($r['nama_user']) ?></td>
                    <td><?= htmlspecialchars($r['nama_lapangan']) ?></td>
                    <td><?= formatTanggal($r['tanggal']) ?></td>
                    <td style="color:var(--text-muted)"><?= substr($r['jam_mulai'],0,5) ?>–<?= substr($r['jam_selesai'],0,5) ?></td>
                    <td style="font-weight:600;color:var(--success)"><?= formatRupiah($r['total_harga']) ?></td>
                    <td><span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span></td>
                    <td>
                        <a href="reservasi.php?detail=<?= $r['id'] ?>" class="btn btn-secondary btn-sm">Detail</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php include '_layout_end.php'; ?>
