<?php
require_once __DIR__ . '/../api/config.php';
sessionStart();
requireAdmin();
$admin = getAdminUser();

$db = getDB();
$filter = $_GET['status'] ?? 'all';

$q = "SELECT pd.*, u.nama AS nama_user, u.email AS email_user FROM penerima_donasi pd LEFT JOIN users u ON pd.user_id = u.id";
if ($filter !== 'all') {
    $stmt = $db->prepare($q . " WHERE pd.status = ? ORDER BY pd.created_at DESC");
    $stmt->execute([$filter]);
} else {
    $stmt = $db->prepare($q . " ORDER BY pd.created_at DESC");
    $stmt->execute();
}
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$counts = ['all'=>0,'pending'=>0,'approved'=>0,'rejected'=>0];
$allRows = $db->query("SELECT status FROM penerima_donasi")->fetchAll(PDO::FETCH_COLUMN);
foreach ($allRows as $s) { $counts['all']++; $counts[$s] = ($counts[$s] ?? 0) + 1; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penerima Donasi - Admin MEDIXA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <?php include __DIR__ . '/admin_style.php'; ?>
</head>
<body>

<?php include __DIR__ . '/admin_sidebar.php'; ?>

<div class="admin-content">
    <div class="admin-topbar">
        <div>
            <h1 class="page-title">Penerima Donasi</h1>
            <p class="page-sub">Kelola permohonan penerima donasi</p>
        </div>
    </div>


    <div style="display:flex;gap:8px;margin-bottom:22px;flex-wrap:wrap;">
        <?php
        $tabs = ['all'=>'Semua','pending'=>'Pending','approved'=>'Disetujui','rejected'=>'Ditolak'];
        foreach ($tabs as $k => $v):
            $active = $filter === $k ? 'style="background:#1a1f36;color:white;border-color:#1a1f36;"' : '';
        ?>
        <a href="?status=<?= $k ?>"
           style="padding:8px 18px;border-radius:10px;border:1.5px solid #e9ecef;background:white;color:#666;font-size:12px;font-weight:700;text-decoration:none;transition:0.2s;display:flex;align-items:center;gap:6px;" <?= $active ?>>
            <?= $v ?>
            <span style="background:rgba(0,0,0,0.08);border-radius:20px;padding:1px 8px;font-size:11px;"><?= $counts[$k] ?></span>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="table-card">
        <div class="table-card-head">
            <h3><i data-lucide="heart-handshake" style="width:16px;height:16px;color:#e91e8c;"></i> Daftar Permohonan</h3>
            <span style="font-size:12px;color:#bbb;"><?= count($rows) ?> data ditemukan</span>
        </div>

        <?php if (empty($rows)): ?>
        <div style="text-align:center;padding:60px 20px;color:#ccc;">
            <i data-lucide="inbox" style="width:48px;height:48px;opacity:0.3;"></i>
            <p style="margin-top:14px;font-size:14px;">Tidak ada permohonan<?= $filter !== 'all' ? ' dengan status ini' : '' ?></p>
        </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pemohon</th>
                    <th>Penyakit</th>
                    <th>Deskripsi</th>
                    <th>Target Dana</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="penerimaTable">
            <?php foreach ($rows as $i => $r):
                $cls = $r['status']==='approved' ? 'pill-green' : ($r['status']==='rejected' ? 'pill-red' : 'pill-orange');
                $lbl = $r['status']==='approved' ? 'Disetujui' : ($r['status']==='rejected' ? 'Ditolak' : 'Pending');
            ?>
            <tr id="row-<?= $r['id'] ?>">
                <td style="color:#bbb;font-size:12px;"><?= $i+1 ?></td>
                <td>
                    <div style="font-weight:700;font-size:13px;"><?= htmlspecialchars($r['nama']) ?></div>
                    <?php if ($r['email_user']): ?>
                    <div style="font-size:11px;color:#bbb;"><?= htmlspecialchars($r['email_user']) ?></div>
                    <?php endif; ?>
                </td>
                <td style="font-size:13px;"><?= htmlspecialchars($r['penyakit']) ?></td>
                <td style="max-width:200px;">
                    <div style="font-size:12px;color:#666;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                        <?= htmlspecialchars($r['deskripsi'] ?: '-') ?>
                    </div>
                </td>
                <td style="font-weight:700;color:#007bff;white-space:nowrap;">
                    Rp <?= number_format($r['target'], 0, ',', '.') ?>
                </td>
                <td>
                    <span class="badge-pill <?= $cls ?>" id="badge-<?= $r['id'] ?>"><?= $lbl ?></span>
                </td>
                <td style="font-size:12px;color:#bbb;white-space:nowrap;">
                    <?= date('d M Y', strtotime($r['created_at'])) ?>
                </td>
                <td>
                    <div style="display:flex;gap:6px;" id="actions-<?= $r['id'] ?>">
                    <?php if ($r['status'] === 'pending'): ?>
                        <button class="btn-action btn-approve" onclick="doAction(<?= $r['id'] ?>,'approve')">
                            <i data-lucide="check" style="width:11px;height:11px;display:inline;vertical-align:middle;"></i> Setujui
                        </button>
                        <button class="btn-action btn-reject" onclick="doAction(<?= $r['id'] ?>,'reject')">
                            <i data-lucide="x" style="width:11px;height:11px;display:inline;vertical-align:middle;"></i> Tolak
                        </button>
                    <?php elseif ($r['status'] === 'approved'): ?>
                        <button class="btn-action btn-reject" onclick="doAction(<?= $r['id'] ?>,'reject')">
                            <i data-lucide="x" style="width:11px;height:11px;display:inline;vertical-align:middle;"></i> Cabut
                        </button>
                    <?php elseif ($r['status'] === 'rejected'): ?>
                        <button class="btn-action btn-approve" onclick="doAction(<?= $r['id'] ?>,'approve')">
                            <i data-lucide="check" style="width:11px;height:11px;display:inline;vertical-align:middle;"></i> Setujui
                        </button>
                    <?php endif; ?>
                        <button class="btn-action btn-delete" onclick="doAction(<?= $r['id'] ?>,'delete')" title="Hapus">
                            <i data-lucide="trash-2" style="width:11px;height:11px;display:inline;vertical-align:middle;"></i>
                        </button>
                    </div>
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
    <i data-lucide="check-circle" style="width:16px;height:16px;" id="toastIcon"></i>
    <span id="toastMsg"></span>
</div>

<script>
lucide.createIcons();

async function doAction(id, action) {
    if (action === 'delete' && !confirm('Yakin hapus permohonan ini?')) return;

    const fd = new FormData();
    fd.append('id', id);
    fd.append('action', action);

    const res  = await fetch('../api/admin_penerima_action.php', { method:'POST', body:fd });
    const data = await res.json();

    if (!data.success) { showToast(data.message, false); return; }

    if (action === 'delete') {
        const row = document.getElementById('row-' + id);
        row.style.opacity = '0'; row.style.transition = '0.4s';
        setTimeout(() => row.remove(), 400);
        showToast('Permohonan dihapus', true);
        return;
    }

    const badge = document.getElementById('badge-' + id);
    const actDiv= document.getElementById('actions-' + id);

    if (action === 'approve') {
        badge.className = 'badge-pill pill-green'; badge.textContent = 'Disetujui';
        actDiv.innerHTML = `
            <button class="btn-action btn-reject" onclick="doAction(${id},'reject')">
                <i data-lucide="x" style="width:11px;height:11px;display:inline;vertical-align:middle;"></i> Cabut
            </button>
            <button class="btn-action btn-delete" onclick="doAction(${id},'delete')" title="Hapus">
                <i data-lucide="trash-2" style="width:11px;height:11px;display:inline;vertical-align:middle;"></i>
            </button>`;
        showToast('Permohonan disetujui!', true);
    } else {
        badge.className = 'badge-pill pill-red'; badge.textContent = 'Ditolak';
        actDiv.innerHTML = `
            <button class="btn-action btn-approve" onclick="doAction(${id},'approve')">
                <i data-lucide="check" style="width:11px;height:11px;display:inline;vertical-align:middle;"></i> Setujui
            </button>
            <button class="btn-action btn-delete" onclick="doAction(${id},'delete')" title="Hapus">
                <i data-lucide="trash-2" style="width:11px;height:11px;display:inline;vertical-align:middle;"></i>
            </button>`;
        showToast('Permohonan ditolak.', true);
    }
    lucide.createIcons();
}

function showToast(msg, ok) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.className = 'toast ' + (ok ? 'toast-ok' : 'toast-err');
    setTimeout(() => t.classList.add('show'), 10);
    setTimeout(() => t.classList.remove('show'), 3000);
    lucide.createIcons();
}
</script>
</body>
</html>
