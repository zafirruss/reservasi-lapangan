<?php
require '../connection.php';
require '../auth.php';
requireLogin();
requireAdmin();

$pageTitle  = 'Kelola Lapangan';
$activePage = 'lapangan';
$msg        = '';
$msgType    = '';
$editData   = null;

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_create'])) {
    $nama       = trim($_POST['nama']);
    $jenis      = trim($_POST['jenis']);
    $deskripsi  = trim($_POST['deskripsi']);
    $harga      = (float)str_replace(['.', ','], ['', '.'], $_POST['harga_per_jam']);
    $status     = $_POST['status'];
    $stmt = $pdo->prepare("INSERT INTO lapangan (nama, jenis, deskripsi, harga_per_jam, status) VALUES (?,?,?,?,?)");
    if ($stmt->execute([$nama, $jenis, $deskripsi, $harga, $status])) {
        $msg = 'Lapangan berhasil ditambahkan.'; $msgType = 'success';
    }
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_update'])) {
    $id        = (int)$_POST['id'];
    $nama      = trim($_POST['nama']);
    $jenis     = trim($_POST['jenis']);
    $deskripsi = trim($_POST['deskripsi']);
    $harga     = (float)str_replace(['.', ','], ['', '.'], $_POST['harga_per_jam']);
    $status    = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE lapangan SET nama=?, jenis=?, deskripsi=?, harga_per_jam=?, status=? WHERE id=?");
    if ($stmt->execute([$nama, $jenis, $deskripsi, $harga, $status, $id])) {
        $msg = 'Lapangan berhasil diperbarui.'; $msgType = 'success';
    }
}

// DELETE
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $pdo->prepare("DELETE FROM lapangan WHERE id=?")->execute([$id]);
    $msg = 'Lapangan berhasil dihapus.'; $msgType = 'success';
}

// Edit form
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM lapangan WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $editData = $stmt->fetch();
}

$lapangans = $pdo->query("SELECT * FROM lapangan ORDER BY id DESC")->fetchAll();

include '_layout.php';
?>

<?php if ($msg): ?>
    <div class="alert alert-<?= $msgType ?>"><?= $msg ?></div>
<?php endif; ?>

<div style="display:grid; grid-template-columns: 1fr 360px; gap:24px; align-items:start;">

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Daftar Lapangan</div>
            <span style="color:var(--text-muted);font-size:0.82rem;"><?= count($lapangans) ?> lapangan</span>
        </div>
        <div class="table-wrapper">
            <?php if (empty($lapangans)): ?>
                <div class="empty-state"><div class="icon">🏟</div><p>Belum ada lapangan.</p></div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Lapangan</th>
                        <th>Jenis</th>
                        <th>Harga/Jam</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($lapangans as $i => $l): ?>
                    <tr>
                        <td style="color:var(--text-muted)"><?= $i+1 ?></td>
                        <td>
                            <div style="font-weight:600;"><?= htmlspecialchars($l['nama']) ?></div>
                            <div style="font-size:0.78rem;color:var(--text-muted);"><?= htmlspecialchars(substr($l['deskripsi'],0,50)) ?>...</div>
                        </td>
                        <td><?= htmlspecialchars($l['jenis']) ?></td>
                        <td style="font-weight:600;color:var(--success)"><?= formatRupiah($l['harga_per_jam']) ?></td>
                        <td>
                            <span class="badge <?= $l['status']==='tersedia' ? 'badge-tersedia' : 'badge-tidak' ?>">
                                <?= $l['status'] === 'tersedia' ? 'Tersedia' : 'Tidak Tersedia' ?>
                            </span>
                        </td>
                        <td>
                            <a href="?edit=<?= $l['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="?hapus=<?= $l['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus lapangan ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Form -->
    <div class="card" style="position:sticky;top:80px;">
        <div class="card-header">
            <div class="card-title"><?= $editData ? 'Edit Lapangan' : 'Tambah Lapangan' ?></div>
            <?php if ($editData): ?>
                <a href="lapangan.php" class="btn btn-secondary btn-sm">✕ Batal</a>
            <?php endif; ?>
        </div>
        <form method="POST" action="">
            <?php if ($editData): ?>
                <input type="hidden" name="id" value="<?= $editData['id'] ?>">
            <?php endif; ?>
            <div class="form-group">
                <label>Nama Lapangan</label>
                <input type="text" name="nama" placeholder="cth: Lapangan Futsal A" value="<?= htmlspecialchars($editData['nama'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Jenis / Olahraga</label>
                <select name="jenis" required>
                    <option value="">— Pilih Jenis —</option>
                    <?php foreach (['Futsal','Badminton','Basket','Tenis','Voli','Renang','Lainnya'] as $j): ?>
                        <option value="<?= $j ?>" <?= ($editData['jenis'] ?? '') === $j ? 'selected' : '' ?>><?= $j ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Harga per Jam (Rp)</label>
                <input type="number" name="harga_per_jam" placeholder="150000" value="<?= $editData['harga_per_jam'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="tersedia" <?= ($editData['status'] ?? '') === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                    <option value="tidak_tersedia" <?= ($editData['status'] ?? '') === 'tidak_tersedia' ? 'selected' : '' ?>>Tidak Tersedia</option>
                </select>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="3" placeholder="Deskripsi fasilitas lapangan..."><?= htmlspecialchars($editData['deskripsi'] ?? '') ?></textarea>
            </div>
            <button type="submit" name="<?= $editData ? 'action_update' : 'action_create' ?>" class="btn btn-primary" style="width:100%">
                <?= $editData ? '💾 Simpan Perubahan' : '＋ Tambah Lapangan' ?>
            </button>
        </form>
    </div>
</div>

<?php include '_layout_end.php'; ?>
