<?php
require_once __DIR__ . '/../api/config.php';
sessionStart();
requireAdmin();
$admin = getAdminUser();

$db = getDB();
$totalUser       = $db->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn();
$activeUser      = $db->query("SELECT COUNT(*) FROM users WHERE role != 'admin' AND aktif = 1")->fetchColumn();
$inactiveUser    = $db->query("SELECT COUNT(*) FROM users WHERE role != 'admin' AND aktif = 0")->fetchColumn();
$pendingPenerima = $db->query("SELECT COUNT(*) FROM penerima_donasi WHERE status = 'pending'")->fetchColumn();
$approvedPenerima= $db->query("SELECT COUNT(*) FROM penerima_donasi WHERE status = 'approved'")->fetchColumn();
$totalDonasi     = $db->query("SELECT COUNT(*) FROM donasi")->fetchColumn();
$totalNominal    = $db->query("SELECT COALESCE(SUM(jumlah),0) FROM donasi")->fetchColumn();

$recentUsers    = $db->query("SELECT nama,email,aktif,created_at FROM users WHERE role!='admin' ORDER BY created_at DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
$recentPenerima = $db->query("SELECT nama,penyakit,status,created_at FROM penerima_donasi ORDER BY created_at DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - MEDIXA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <?php include __DIR__ . '/admin_style.php'; ?>
</head>
<body>

<?php include __DIR__ . '/admin_sidebar.php'; ?>

<div class="admin-content">
    <div class="admin-topbar">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-sub">Selamat datang kembali, <?= htmlspecialchars($admin['nama']) ?></p>
        </div>
        <div class="topbar-right">
            <span class="admin-badge">
                <i data-lucide="shield-check" style="width:13px;height:13px;color:#27ae60;"></i>
                <?= htmlspecialchars($admin['email']) ?>
            </span>
        </div>
    </div>


    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f4ff;">
                <i data-lucide="users" style="color:#007bff;width:22px;height:22px;"></i>
            </div>
            <div class="stat-val"><?= $totalUser ?></div>
            <div class="stat-lbl">Total Pengguna</div>
            <div class="stat-sub"><?= $activeUser ?> aktif · <?= $inactiveUser ?> nonaktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff8e1;">
                <i data-lucide="clock" style="color:#f39c12;width:22px;height:22px;"></i>
            </div>
            <div class="stat-val"><?= $pendingPenerima ?></div>
            <div class="stat-lbl">Permohonan Pending</div>
            <div class="stat-sub">Menunggu persetujuan</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f9f0;">
                <i data-lucide="heart-handshake" style="color:#27ae60;width:22px;height:22px;"></i>
            </div>
            <div class="stat-val"><?= $approvedPenerima ?></div>
            <div class="stat-lbl">Penerima Aktif</div>
            <div class="stat-sub">Sudah disetujui</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fce8f5;">
                <i data-lucide="gift" style="color:#e91e8c;width:22px;height:22px;"></i>
            </div>
            <div class="stat-val"><?= $totalDonasi ?></div>
            <div class="stat-lbl">Total Donasi</div>
            <div class="stat-sub">Rp <?= number_format($totalNominal, 0, ',', '.') ?></div>
        </div>
    </div>

    <?php if ($pendingPenerima > 0): ?>
    <div style="background:linear-gradient(90deg,#fff8e1,#fffde7);border:1.5px solid #ffe082;border-radius:14px;padding:14px 20px;margin-bottom:22px;display:flex;align-items:center;gap:12px;">
        <i data-lucide="alert-circle" style="color:#f39c12;width:20px;height:20px;flex-shrink:0;"></i>
        <div style="flex:1;">
            <strong style="font-size:13px;color:#b45309;"><?= $pendingPenerima ?> permohonan penerima donasi</strong>
            <span style="font-size:13px;color:#92400e;"> menunggu persetujuan Anda.</span>
        </div>
        <a href="/admin/penerima.php" style="font-size:12px;font-weight:700;color:#d97706;text-decoration:none;white-space:nowrap;">Tinjau Sekarang →</a>
    </div>
    <?php endif; ?>


    <div class="dash-grid">
        <div class="dash-card">
            <div class="dash-card-head">
                <h3><i data-lucide="users" style="width:15px;height:15px;color:#007bff;"></i> Pengguna Terbaru</h3>
                <a href="pengguna.php" class="link-more">Lihat Semua →</a>
            </div>
            <table class="mini-table">
                <thead><tr><th>Nama</th><th>Status</th><th>Terdaftar</th></tr></thead>
                <tbody>
                <?php foreach ($recentUsers as $u): ?>
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:13px;color:#1a1a1a;"><?= htmlspecialchars($u['nama']) ?></div>
                        <div style="font-size:11px;color:#bbb;"><?= htmlspecialchars($u['email']) ?></div>
                    </td>
                    <td><span class="badge-pill <?= $u['aktif'] ? 'pill-green' : 'pill-red' ?>"><?= $u['aktif'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                    <td style="font-size:11px;color:#bbb;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recentUsers)): ?><tr><td colspan="3" class="empty-td">Belum ada pengguna</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="dash-card">
            <div class="dash-card-head">
                <h3><i data-lucide="heart-handshake" style="width:15px;height:15px;color:#e91e8c;"></i> Permohonan Terbaru</h3>
                <a href="/admin/penerima.php" class="link-more">Lihat Semua →</a>
            </div>
            <table class="mini-table">
                <thead><tr><th>Nama</th><th>Penyakit</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($recentPenerima as $p):
                    $cls = $p['status']==='approved' ? 'pill-green' : ($p['status']==='rejected' ? 'pill-red' : 'pill-orange');
                    $lbl = $p['status']==='approved' ? 'Disetujui' : ($p['status']==='rejected' ? 'Ditolak' : 'Pending');
                ?>
                <tr>
                    <td style="font-weight:600;"><?= htmlspecialchars($p['nama']) ?></td>
                    <td style="font-size:12px;color:#888;"><?= htmlspecialchars($p['penyakit']) ?></td>
                    <td><span class="badge-pill <?= $cls ?>"><?= $lbl ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recentPenerima)): ?><tr><td colspan="3" class="empty-td">Belum ada permohonan</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>lucide.createIcons();</script>
</body>
</html>
