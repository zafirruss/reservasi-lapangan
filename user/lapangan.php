<?php
require '../connection.php';
require '../auth.php';
requireLogin();

$pageTitle  = 'Pilih Lapangan';
$activePage = 'lapangan';
$u          = getCurrentUser();

$msg     = '';
$msgType = '';

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_lapangan'])) {
    $lapangan_id = (int)$_POST['lapangan_id'];
    $tanggal     = $_POST['tanggal'];
    $jam_mulai   = $_POST['jam_mulai'];
    $durasi      = (int)$_POST['durasi'];
    $catatan     = trim($_POST['catatan'] ?? '');

    if ($durasi < 1 || $durasi > 8) {
        $msg = 'Durasi harus antara 1–8 jam.'; $msgType = 'error';
    } else {
        // Calculate jam selesai
        $jam_selesai_ts = strtotime($jam_mulai) + ($durasi * 3600);
        $jam_selesai    = date('H:i', $jam_selesai_ts);

        // Check conflict
        $conflict = $pdo->prepare("
            SELECT id FROM reservasi 
            WHERE lapangan_id = ? AND tanggal = ? AND status NOT IN ('batal')
            AND (
                (jam_mulai < ? AND jam_selesai > ?) OR
                (jam_mulai >= ? AND jam_mulai < ?)
            )
        ");
        $conflict->execute([$lapangan_id, $tanggal, $jam_selesai, $jam_mulai, $jam_mulai, $jam_selesai]);
        
        if ($conflict->fetch()) {
            $msg = 'Jadwal yang kamu pilih sudah terpesan. Silakan pilih waktu lain.'; $msgType = 'error';
        } else {
            // Get harga
            $lp = $pdo->prepare("SELECT harga_per_jam FROM lapangan WHERE id=?");
            $lp->execute([$lapangan_id]);
            $harga       = (float)$lp->fetchColumn();
            $total_harga = $harga * $durasi;

            $stmt = $pdo->prepare("
                INSERT INTO reservasi (user_id, lapangan_id, tanggal, jam_mulai, jam_selesai, durasi, total_harga, catatan)
                VALUES (?,?,?,?,?,?,?,?)
            ");
            if ($stmt->execute([$u['id'], $lapangan_id, $tanggal, $jam_mulai, $jam_selesai, $durasi, $total_harga, $catatan])) {
                $msg = 'Reservasi berhasil dibuat! Menunggu konfirmasi admin.'; $msgType = 'success';
            }
        }
    }
}

// Filter jenis
$filterJenis = $_GET['jenis'] ?? '';
$sqlL = "SELECT * FROM lapangan WHERE status='tersedia'";
$paramsL = [];
if ($filterJenis) {
    $sqlL .= " AND jenis = ?";
    $paramsL[] = $filterJenis;
}
$sqlL .= " ORDER BY jenis, nama";
$stmtL = $pdo->prepare($sqlL);
$stmtL->execute($paramsL);
$lapangans = $stmtL->fetchAll();

// All jenis
$jenisAll = $pdo->query("SELECT DISTINCT jenis FROM lapangan ORDER BY jenis")->fetchAll(PDO::FETCH_COLUMN);

// Selected lapangan for booking form
$selectedLapangan = null;
if (isset($_GET['book'])) {
    $sl = $pdo->prepare("SELECT * FROM lapangan WHERE id=? AND status='tersedia'");
    $sl->execute([(int)$_GET['book']]);
    $selectedLapangan = $sl->fetch();
}

function lapanganThumb($jenis) {
    $map = [
        'futsal'    => ['futsal',    '⚽'],
        'badminton' => ['badminton', '🏸'],
        'basket'    => ['basket',    '🏀'],
        'tenis'     => ['tenis',     '🎾'],
        'voli'      => ['voli',      '🏐'],
    ];
    $k = strtolower($jenis);
    return $map[$k] ?? ['default', '🏟'];
}

include '_layout.php';
?>

<?php if ($msg): ?>
    <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<!-- Booking Form -->
<?php if ($selectedLapangan): ?>
<div class="card" style="margin-bottom: 28px; border: 2px solid var(--accent);">
    <div class="card-header">
        <div>
            <div class="card-title">🗓 Buat Reservasi</div>
            <div style="font-size:0.85rem;color:var(--text-muted);"><?= htmlspecialchars($selectedLapangan['nama']) ?></div>
        </div>
        <a href="lapangan.php" class="btn btn-secondary btn-sm">✕ Batal</a>
    </div>
    <form method="POST" action="">
        <input type="hidden" name="lapangan_id" value="<?= $selectedLapangan['id'] ?>">
        <input type="hidden" name="book_lapangan" value="1">
        <div class="form-row">
            <div class="form-group">
                <label>Tanggal Bermain</label>
                <input type="date" name="tanggal" min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Jam Mulai</label>
                <select name="jam_mulai" required>
                    <?php for ($h = 6; $h <= 22; $h++): ?>
                        <option value="<?= sprintf('%02d:00', $h) ?>"><?= sprintf('%02d:00', $h) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Durasi (jam)</label>
                <select name="durasi" required>
                    <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> jam — <?= formatRupiah($selectedLapangan['harga_per_jam'] * $i) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Catatan (opsional)</label>
                <input type="text" name="catatan" placeholder="cth: untuk latihan tim">
            </div>
        </div>
        <div style="background:var(--surface2);border-radius:12px;padding:16px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:0.78rem;color:var(--text-muted)">Harga per jam</div>
                <div style="font-weight:700;color:var(--accent)"><?= formatRupiah($selectedLapangan['harga_per_jam']) ?></div>
            </div>
            <div>
                <div style="font-size:0.78rem;color:var(--text-muted)">Status</div>
                <span class="badge badge-tersedia">Tersedia</span>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">🏟 Konfirmasi Reservasi</button>
    </form>
</div>
<?php endif; ?>

<!-- Filter Jenis -->
<div style="display:flex;align-items:center;gap:8px;margin-bottom:24px;flex-wrap:wrap;">
    <a href="lapangan.php" class="btn btn-sm <?= !$filterJenis ? 'btn-primary' : 'btn-secondary' ?>">Semua</a>
    <?php foreach ($jenisAll as $j): ?>
        <a href="?jenis=<?= urlencode($j) ?>" class="btn btn-sm <?= $filterJenis===$j ? 'btn-primary' : 'btn-secondary' ?>"><?= htmlspecialchars($j) ?></a>
    <?php endforeach; ?>
</div>

<!-- Lapangan Grid -->
<?php if (empty($lapangans)): ?>
    <div class="empty-state card">
        <div class="icon">🏟</div>
        <p>Tidak ada lapangan tersedia saat ini.</p>
    </div>
<?php else: ?>
<div class="lapangan-grid">
    <?php foreach ($lapangans as $l):
        [$thumbClass, $emoji] = lapanganThumb($l['jenis']);
    ?>
    <div class="lapangan-card">
        <div class="lapangan-thumb <?= $thumbClass ?>">
            <span><?= $emoji ?></span>
        </div>
        <div class="lapangan-body">
            <div class="lapangan-jenis"><?= htmlspecialchars($l['jenis']) ?></div>
            <div class="lapangan-name"><?= htmlspecialchars($l['nama']) ?></div>
            <div class="lapangan-desc"><?= htmlspecialchars($l['deskripsi'] ?? '') ?></div>
            <div class="lapangan-footer">
                <div>
                    <div class="lapangan-price"><?= formatRupiah($l['harga_per_jam']) ?></div>
                    <div class="lapangan-price-unit">per jam</div>
                </div>
                <a href="?book=<?= $l['id'] ?>#booking" class="btn btn-primary btn-sm">Pesan →</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include '_layout_end.php'; ?>
