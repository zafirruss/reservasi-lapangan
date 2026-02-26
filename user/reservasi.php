<?php
require '../connection.php';
require '../auth.php';
requireLogin();

$pageTitle  = 'Reservasi Saya';
$activePage = 'reservasi';
$u          = getCurrentUser();
$msg        = '';
$msgType    = '';

// Cancel reservasi
if (isset($_GET['batal'])) {
    $id = (int)$_GET['batal'];
    $stmt = $pdo->prepare("UPDATE reservasi SET status='batal' WHERE id=? AND user_id=? AND status='pending'");
    $stmt->execute([$id, $u['id']]);
    if ($stmt->rowCount()) {
        $msg = 'Reservasi berhasil dibatalkan.'; $msgType = 'success';
    } else {
        $msg = 'Reservasi tidak dapat dibatalkan (hanya status pending yang bisa dibatalkan).'; $msgType = 'error';
    }
}

$filterStatus = $_GET['status'] ?? '';
$sql = "SELECT r.*, l.nama as nama_lapangan, l.jenis, l.harga_per_jam
        FROM reservasi r
        JOIN lapangan l ON r.lapangan_id = l.id
        WHERE r.user_id = ?";
$params = [$u['id']];
if ($filterStatus) {
    $sql .= " AND r.status = ?";
    $params[] = $filterStatus;
}
$sql .= " ORDER BY r.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reservasis = $stmt->fetchAll();

include '_layout.php';
?>

<?php if ($msg): ?>
    <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <h2 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;">Riwayat Reservasi</h2>
    <a href="lapangan.php" class="btn btn-primary">＋ Booking Baru</a>
</div>

<!-- Filter -->
<div style="display:flex;gap:8px;margin-bottom:24px;flex-wrap:wrap;">
    <?php foreach (['' => 'Semua', 'pending' => 'Pending', 'konfirmasi' => 'Dikonfirmasi', 'selesai' => 'Selesai', 'batal' => 'Dibatalkan'] as $val => $label): ?>
    <a href="?status=<?= $val ?>" class="btn btn-sm <?= $filterStatus === $val ? 'btn-primary' : 'btn-secondary' ?>"><?= $label ?></a>
    <?php endforeach; ?>
</div>

<?php if (empty($reservasis)): ?>
<div class="empty-state card">
    <div class="icon">📭</div>
    <p>Belum ada reservasi<?= $filterStatus ? ' dengan status ini' : '' ?>.</p>
    <a href="lapangan.php" class="btn btn-primary">🏟 Pesan Lapangan</a>
</div>
<?php else: ?>
<div style="display:flex;flex-direction:column;gap:16px;">
    <?php foreach ($reservasis as $r):
        $badge = badgeStatus($r['status']);
    ?>
    <div class="card card-sm" style="display:grid;grid-template-columns:auto 1fr auto;gap:20px;align-items:center;">
        <!-- Icon -->
        <div style="width:54px;height:54px;border-radius:14px;background:var(--accent-light);display:flex;align-items:center;justify-content:center;font-size:1.6rem;">
            <?php
            $icons = ['Futsal'=>'⚽','Badminton'=>'🏸','Basket'=>'🏀','Tenis'=>'🎾','Voli'=>'🏐'];
            echo $icons[$r['jenis']] ?? '🏟';
            ?>
        </div>
        <!-- Info -->
        <div>
            <div style="font-weight:700;font-size:1rem;margin-bottom:3px;"><?= htmlspecialchars($r['nama_lapangan']) ?></div>
            <div style="display:flex;gap:16px;font-size:0.82rem;color:var(--text-muted);flex-wrap:wrap;">
                <span>📅 <?= formatTanggal($r['tanggal']) ?></span>
                <span>⏰ <?= substr($r['jam_mulai'],0,5) ?> – <?= substr($r['jam_selesai'],0,5) ?></span>
                <span>⏱ <?= $r['durasi'] ?> jam</span>
                <?php if ($r['catatan']): ?>
                <span>📝 <?= htmlspecialchars($r['catatan']) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <!-- Status + Price + Action -->
        <div style="text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
            <span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
            <div style="font-family:'Syne',sans-serif;font-weight:800;color:var(--accent);"><?= formatRupiah($r['total_harga']) ?></div>
            <?php if ($r['status'] === 'pending'): ?>
            <a href="?batal=<?= $r['id'] ?><?= $filterStatus ? '&status='.$filterStatus : '' ?>"
               class="btn btn-danger btn-sm"
               onclick="return confirm('Yakin ingin membatalkan reservasi ini?')">✕ Batalkan</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include '_layout_end.php'; ?>
