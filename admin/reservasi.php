<?php
require '../connection.php';
require '../auth.php';
requireLogin();
requireAdmin();

$pageTitle  = 'Kelola Reservasi';
$activePage = 'reservasi';
$msg        = '';
$msgType    = '';

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id     = (int)$_POST['id'];
    $action = $_POST['action'];
    $statusMap = [
        'konfirmasi' => 'konfirmasi',
        'selesai'    => 'selesai',
        'batal'      => 'batal',
    ];
    if (isset($statusMap[$action])) {
        $stmt = $pdo->prepare("UPDATE reservasi SET status=? WHERE id=?");
        $stmt->execute([$statusMap[$action], $id]);
        $msg     = 'Status reservasi berhasil diperbarui.';
        $msgType = 'success';
    }
}

// Filter
$filterStatus = $_GET['status'] ?? '';
$sql = "SELECT r.*, u.nama as nama_user, u.email, u.no_hp, l.nama as nama_lapangan, l.jenis
        FROM reservasi r
        JOIN users u ON r.user_id = u.id
        JOIN lapangan l ON r.lapangan_id = l.id";
$params = [];
if ($filterStatus) {
    $sql .= " WHERE r.status = ?";
    $params[] = $filterStatus;
}
$sql .= " ORDER BY r.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reservasis = $stmt->fetchAll();

// Detail view
$detail = null;
if (isset($_GET['detail'])) {
    $ds = $pdo->prepare("SELECT r.*, u.nama as nama_user, u.email, u.no_hp, l.nama as nama_lapangan, l.jenis, l.harga_per_jam
                         FROM reservasi r
                         JOIN users u ON r.user_id = u.id
                         JOIN lapangan l ON r.lapangan_id = l.id
                         WHERE r.id = ?");
    $ds->execute([(int)$_GET['detail']]);
    $detail = $ds->fetch();
}

include '_layout.php';
?>

<?php if ($msg): ?>
    <div class="alert alert-<?= $msgType ?>"><?= $msg ?></div>
<?php endif; ?>

<?php if ($detail): ?>
<!-- Detail Modal Panel -->
<div class="card" style="margin-bottom:24px; border-color: var(--accent); border-left: 3px solid var(--accent);">
    <div class="card-header">
        <div class="card-title">Detail Reservasi #<?= $detail['id'] ?></div>
        <a href="reservasi.php" class="btn btn-secondary btn-sm">✕ Tutup</a>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
        <div>
            <div style="margin-bottom:16px;">
                <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-muted);margin-bottom:4px;">Pelanggan</div>
                <div style="font-weight:600;"><?= htmlspecialchars($detail['nama_user']) ?></div>
                <div style="font-size:0.82rem;color:var(--text-muted);"><?= htmlspecialchars($detail['email']) ?></div>
                <div style="font-size:0.82rem;color:var(--text-muted);"><?= htmlspecialchars($detail['no_hp'] ?? '-') ?></div>
            </div>
            <div style="margin-bottom:16px;">
                <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-muted);margin-bottom:4px;">Lapangan</div>
                <div style="font-weight:600;"><?= htmlspecialchars($detail['nama_lapangan']) ?></div>
                <div style="font-size:0.82rem;color:var(--text-muted);"><?= $detail['jenis'] ?></div>
            </div>
        </div>
        <div>
            <div style="margin-bottom:16px;">
                <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-muted);margin-bottom:4px;">Jadwal</div>
                <div style="font-weight:600;"><?= formatTanggal($detail['tanggal']) ?></div>
                <div style="font-size:0.82rem;color:var(--text-muted);"><?= substr($detail['jam_mulai'],0,5) ?> – <?= substr($detail['jam_selesai'],0,5) ?> (<?= $detail['durasi'] ?> jam)</div>
            </div>
            <div style="margin-bottom:16px;">
                <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-muted);margin-bottom:4px;">Total Bayar</div>
                <div style="font-weight:800;font-size:1.2rem;color:var(--success);"><?= formatRupiah($detail['total_harga']) ?></div>
            </div>
            <?php if ($detail['catatan']): ?>
            <div>
                <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-muted);margin-bottom:4px;">Catatan</div>
                <div style="font-size:0.875rem;"><?= htmlspecialchars($detail['catatan']) ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($detail['status'] === 'pending'): ?>
    <div style="display:flex;gap:10px;margin-top:20px;padding-top:20px;border-top:1px solid var(--border);">
        <form method="POST">
            <input type="hidden" name="id" value="<?= $detail['id'] ?>">
            <input type="hidden" name="action" value="konfirmasi">
            <button type="submit" class="btn btn-success">✓ Konfirmasi</button>
        </form>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $detail['id'] ?>">
            <input type="hidden" name="action" value="batal">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Batalkan reservasi ini?')">✕ Batalkan</button>
        </form>
    </div>
    <?php elseif ($detail['status'] === 'konfirmasi'): ?>
    <div style="display:flex;gap:10px;margin-top:20px;padding-top:20px;border-top:1px solid var(--border);">
        <form method="POST">
            <input type="hidden" name="id" value="<?= $detail['id'] ?>">
            <input type="hidden" name="action" value="selesai">
            <button type="submit" class="btn btn-primary">✓ Tandai Selesai</button>
        </form>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <div class="card-title">Semua Reservasi</div>
        <div style="display:flex;gap:8px;">
            <?php foreach ([''=>'Semua','pending'=>'Pending','konfirmasi'=>'Konfirmasi','selesai'=>'Selesai','batal'=>'Batal'] as $val=>$label): ?>
            <a href="?status=<?= $val ?>" class="btn btn-sm <?= $filterStatus===$val ? 'btn-primary' : 'btn-secondary' ?>"><?= $label ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="table-wrapper">
        <?php if (empty($reservasis)): ?>
            <div class="empty-state"><div class="icon">📭</div><p>Tidak ada data reservasi.</p></div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pelanggan</th>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Durasi</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($reservasis as $i => $r):
                $badge = badgeStatus($r['status']);
            ?>
                <tr>
                    <td style="color:var(--text-muted)"><?= $i+1 ?></td>
                    <td>
                        <div style="font-weight:500"><?= htmlspecialchars($r['nama_user']) ?></div>
                        <div style="font-size:0.78rem;color:var(--text-muted)"><?= htmlspecialchars($r['email']) ?></div>
                    </td>
                    <td>
                        <div><?= htmlspecialchars($r['nama_lapangan']) ?></div>
                        <div style="font-size:0.78rem;color:var(--text-muted)"><?= $r['jenis'] ?></div>
                    </td>
                    <td><?= formatTanggal($r['tanggal']) ?></td>
                    <td style="color:var(--text-muted)"><?= substr($r['jam_mulai'],0,5) ?>–<?= substr($r['jam_selesai'],0,5) ?></td>
                    <td><?= $r['durasi'] ?> jam</td>
                    <td style="font-weight:600;color:var(--success)"><?= formatRupiah($r['total_harga']) ?></td>
                    <td><span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span></td>
                    <td>
                        <a href="?detail=<?= $r['id'] ?><?= $filterStatus ? '&status='.$filterStatus : '' ?>" class="btn btn-secondary btn-sm">Detail</a>
                        <?php if ($r['status'] === 'pending'): ?>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <input type="hidden" name="action" value="konfirmasi">
                            <button type="submit" class="btn btn-success btn-sm">✓</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php include '_layout_end.php'; ?>
