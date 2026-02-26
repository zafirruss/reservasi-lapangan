<?php
require '../connection.php';
require '../auth.php';
requireLogin();
requireAdmin();

$pageTitle  = 'Data Pengguna';
$activePage = 'users';
$msg = ''; $msgType = '';

// DELETE user
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id !== (int)getCurrentUser()['id']) {
        $pdo->prepare("DELETE FROM users WHERE id=? AND role='user'")->execute([$id]);
        $msg = 'Pengguna berhasil dihapus.'; $msgType = 'success';
    }
}

$users = $pdo->query("
    SELECT u.*, 
        (SELECT COUNT(*) FROM reservasi r WHERE r.user_id = u.id) as total_reservasi,
        (SELECT COALESCE(SUM(total_harga),0) FROM reservasi r WHERE r.user_id = u.id AND r.status IN ('konfirmasi','selesai')) as total_spend
    FROM users u ORDER BY u.created_at DESC
")->fetchAll();

include '_layout.php';
?>

<?php if ($msg): ?>
    <div class="alert alert-<?= $msgType ?>"><?= $msg ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <div class="card-title">Semua Pengguna</div>
        <span style="color:var(--text-muted);font-size:0.82rem;"><?= count($users) ?> akun terdaftar</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pengguna</th>
                    <th>No. HP</th>
                    <th>Role</th>
                    <th>Total Reservasi</th>
                    <th>Total Spend</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $i => $u): ?>
                <tr>
                    <td style="color:var(--text-muted)"><?= $i+1 ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;background:var(--accent);border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;color:#fff;flex-shrink:0;">
                                <?= strtoupper(substr($u['nama'],0,1)) ?>
                            </div>
                            <div>
                                <div style="font-weight:600;"><?= htmlspecialchars($u['nama']) ?></div>
                                <div style="font-size:0.78rem;color:var(--text-muted);"><?= htmlspecialchars($u['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="color:var(--text-muted)"><?= htmlspecialchars($u['no_hp'] ?? '-') ?></td>
                    <td>
                        <span class="badge <?= $u['role']==='admin' ? 'badge-admin' : 'badge-user' ?>"><?= $u['role'] ?></span>
                    </td>
                    <td><?= $u['total_reservasi'] ?> kali</td>
                    <td style="font-weight:600;color:var(--success)"><?= formatRupiah($u['total_spend']) ?></td>
                    <td style="color:var(--text-muted);font-size:0.82rem;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <?php if ($u['role'] !== 'admin'): ?>
                        <a href="?hapus=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus pengguna ini?')">Hapus</a>
                        <?php else: ?>
                        <span style="color:var(--text-muted);font-size:0.78rem;">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '_layout_end.php'; ?>
