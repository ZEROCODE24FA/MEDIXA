<?php
require_once __DIR__ . '/../api/config.php';
sessionStart();
requireAdmin();
$admin = getAdminUser();

$db = getDB();
$filter = $_GET['status'] ?? 'all';

if ($filter === 'active') {
    $stmt = $db->prepare("SELECT id,nama,email,phone,aktif,created_at FROM users WHERE role != 'admin' AND aktif=1 ORDER BY created_at DESC");
} elseif ($filter === 'inactive') {
    $stmt = $db->prepare("SELECT id,nama,email,phone,aktif,created_at FROM users WHERE role != 'admin' AND aktif=0 ORDER BY created_at DESC");
} else {
    $stmt = $db->prepare("SELECT id,nama,email,phone,aktif,created_at FROM users WHERE role != 'admin' ORDER BY created_at DESC");
}
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalAll     = $db->query("SELECT COUNT(*) FROM users WHERE role!='admin'")->fetchColumn();
$totalActive  = $db->query("SELECT COUNT(*) FROM users WHERE role!='admin' AND aktif=1")->fetchColumn();
$totalInactive= $db->query("SELECT COUNT(*) FROM users WHERE role!='admin' AND aktif=0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Admin MEDIXA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <?php include __DIR__ . '/admin_style.php'; ?>
</head>
<body>

<?php include __DIR__ . '/admin_sidebar.php'; ?>

<div class="admin-content">
    <div class="admin-topbar">
        <div>
            <h1 class="page-title">Kelola Pengguna</h1>
            <p class="page-sub">Aktifkan atau nonaktifkan akun pengguna</p>
        </div>
    </div>


    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:22px;">
        <div style="background:white;border-radius:14px;padding:16px 18px;border:1px solid #f0f4f8;box-shadow:0 3px 12px rgba(0,0,0,0.03);">
            <div style="font-size:22px;font-weight:800;color:#1a1a1a;"><?= $totalAll ?></div>
            <div style="font-size:12px;color:#999;margin-top:2px;">Total Pengguna</div>
        </div>
        <div style="background:white;border-radius:14px;padding:16px 18px;border:1px solid #f0f4f8;box-shadow:0 3px 12px rgba(0,0,0,0.03);">
            <div style="font-size:22px;font-weight:800;color:#27ae60;"><?= $totalActive ?></div>
            <div style="font-size:12px;color:#999;margin-top:2px;">Akun Aktif</div>
        </div>
        <div style="background:white;border-radius:14px;padding:16px 18px;border:1px solid #f0f4f8;box-shadow:0 3px 12px rgba(0,0,0,0.03);">
            <div style="font-size:22px;font-weight:800;color:#e74c3c;"><?= $totalInactive ?></div>
            <div style="font-size:12px;color:#999;margin-top:2px;">Akun Nonaktif</div>
        </div>
    </div>

    <!-- Filter + Search -->
    <div style="display:flex;gap:10px;margin-bottom:18px;align-items:center;flex-wrap:wrap;">
        <div style="display:flex;gap:8px;">
            <?php
            $tabs = ['all'=>'Semua','active'=>'Aktif','inactive'=>'Nonaktif'];
            foreach ($tabs as $k => $v):
                $active = $filter === $k ? 'style="background:#1a1f36;color:white;border-color:#1a1f36;"' : '';
            ?>
            <a href="?status=<?= $k ?>"
               style="padding:8px 16px;border-radius:10px;border:1.5px solid #e9ecef;background:white;color:#666;font-size:12px;font-weight:700;text-decoration:none;" <?= $active ?>>
                <?= $v ?>
            </a>
            <?php endforeach; ?>
        </div>
        <input type="text" id="searchInput" class="search-input"
               placeholder="Cari nama atau email..." oninput="filterTable()" style="margin-left:auto;">
    </div>

    <div class="table-card">
        <div class="table-card-head">
            <h3><i data-lucide="users" style="width:16px;height:16px;color:#007bff;"></i> Daftar Pengguna</h3>
            <span style="font-size:12px;color:#bbb;" id="countLabel"><?= count($users) ?> pengguna</span>
        </div>

        <?php if (empty($users)): ?>
        <div style="text-align:center;padding:60px 20px;color:#ccc;">
            <i data-lucide="users" style="width:48px;height:48px;opacity:0.25;"></i>
            <p style="margin-top:14px;font-size:14px;">Belum ada pengguna terdaftar</p>
        </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table class="admin-table" id="userTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Status</th>
                    <th>Terdaftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $i => $u): ?>
            <tr id="urow-<?= $u['id'] ?>" data-name="<?= strtolower($u['nama']) ?>" data-email="<?= strtolower($u['email']) ?>">
                <td style="color:#bbb;font-size:12px;"><?= $i+1 ?></td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#007bff,#00d2ff);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:white;flex-shrink:0;">
                            <?= strtoupper(substr($u['nama'],0,1)) ?>
                        </div>
                        <span style="font-weight:700;font-size:13px;"><?= htmlspecialchars($u['nama']) ?></span>
                    </div>
                </td>
                <td style="font-size:13px;color:#888;"><?= htmlspecialchars($u['email']) ?></td>
                <td style="font-size:13px;color:#888;"><?= htmlspecialchars($u['phone'] ?? '-') ?></td>
                <td>
                    <span class="badge-pill <?= $u['aktif'] ? 'pill-green' : 'pill-red' ?>" id="ubadge-<?= $u['id'] ?>">
                        <?= $u['aktif'] ? 'Aktif' : 'Nonaktif' ?>
                    </span>
                </td>
                <td style="font-size:12px;color:#bbb;white-space:nowrap;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                <td>
                    <button class="btn-action btn-toggle" id="utoggle-<?= $u['id'] ?>"
                            onclick="toggleUser(<?= $u['id'] ?>, <?= $u['aktif'] ?>)">
                        <?= $u['aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast">
    <i data-lucide="check-circle" style="width:16px;height:16px;"></i>
    <span id="toastMsg"></span>
</div>

<script>
lucide.createIcons();

async function toggleUser(id, currentStatus) {
    const newStatus = currentStatus ? 0 : 1;
    const fd = new FormData();
    fd.append('user_id', id);
    fd.append('aktif', newStatus);

    try {
        const res  = await fetch('../api/admin_toggle_user.php', { method:'POST', body:fd });
        const data = await res.json();

        if (!data.success) { showToast(data.message, false); return; }

        const badge  = document.getElementById('ubadge-' + id);
        const btn    = document.getElementById('utoggle-' + id);
        badge.className = 'badge-pill ' + (newStatus ? 'pill-green' : 'pill-red');
        badge.textContent = newStatus ? 'Aktif' : 'Nonaktif';
        btn.textContent   = newStatus ? 'Nonaktifkan' : 'Aktifkan';
        btn.setAttribute('onclick', `toggleUser(${id}, ${newStatus})`);

        showToast(newStatus ? 'Akun berhasil diaktifkan' : 'Akun berhasil dinonaktifkan', true);
    } catch (err) {
        console.error('Toggle gagal:', err);
        showToast('Terjadi kesalahan, coba lagi', false);
    }
}

function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    let count = 0;
    document.querySelectorAll('#userTable tbody tr').forEach(tr => {
        const name  = tr.dataset.name  || '';
        const email = tr.dataset.email || '';
        const show  = name.includes(q) || email.includes(q);
        tr.style.display = show ? '' : 'none';
        if (show) count++;
    });
    document.getElementById('countLabel').textContent = count + ' pengguna';
}

function showToast(msg, ok) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.className = 'toast ' + (ok ? 'toast-ok' : 'toast-err');
    setTimeout(() => t.classList.add('show'), 10);
    setTimeout(() => t.classList.remove('show'), 3000);
}
</script>
</body>
</html>
