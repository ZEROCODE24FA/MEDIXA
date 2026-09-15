<?php
require_once __DIR__ . '/../api/config.php';
sessionStart();
requireAdmin();
$admin = getAdminUser();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $donasiId = $_POST['donasi_id'] ?? null;

    if ($action === 'delete' && $donasiId) {
        //Menghapus record permanen pada tabel donasi
        $stmt = $db->prepare("DELETE FROM donasi WHERE id = ?");
        $stmt->execute([$donasiId]);
        
        //halaman dengan parameter GET yang sama
        header("Location: " . $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET));
        exit;
    } elseif ($action === 'update_status' && $donasiId) {
        //Mengubah status transaksi donasi
        $newStatus = $_POST['status'] ?? 'pending';
        $stmt = $db->prepare("UPDATE donasi SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $donasiId]);
        
        header("Location: " . $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET));
        exit;
    }
}

$filter  = $_GET['status'] ?? 'all';
$search  = trim($_GET['q'] ?? '');

$where = [];
$params = [];

if ($filter !== 'all') {
    $where[] = "d.status = ?";
    $params[] = $filter;
}
if ($search !== '') {
    $where[] = "(d.nama_donatur LIKE ? OR d.email_donatur LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$stmt = $db->prepare("SELECT * FROM donasi d $whereSQL ORDER BY d.created_at DESC");
$stmt->execute($params);
$donasi = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalNominal  = $db->query("SELECT COALESCE(SUM(jumlah),0) FROM donasi")->fetchColumn();
$totalCount    = $db->query("SELECT COUNT(*) FROM donasi")->fetchColumn();
$bulanIni      = $db->query("SELECT COALESCE(SUM(jumlah),0) FROM donasi WHERE DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->fetchColumn();
$rerata        = $totalCount > 0 ? $totalNominal / $totalCount : 0;

$byMonth = $db->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as bln,
           COUNT(*) as cnt,
           SUM(jumlah) as total
    FROM donasi
    GROUP BY bln
    ORDER BY bln DESC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

// Email log (last 30)
try {
    $emailLogs = $db->query("
        SELECT el.*, d.nama_donatur, d.jumlah
        FROM email_log el
        LEFT JOIN donasi d ON d.id = el.donasi_id
        ORDER BY el.created_at DESC
        LIMIT 30
    ")->fetchAll(PDO::FETCH_ASSOC);
    $emailSent   = $db->query("SELECT COUNT(*) FROM email_log WHERE status='sent'")->fetchColumn();
    $emailFailed = $db->query("SELECT COUNT(*) FROM email_log WHERE status='failed'")->fetchColumn();
} catch (Exception $e) {
    $emailLogs = []; $emailSent = 0; $emailFailed = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Donasi - Admin MEDIXA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <?php include __DIR__ . '/admin_style.php'; ?>
    <style>
        .export-btn {
            display:inline-flex;align-items:center;gap:7px;
            padding:9px 18px;border-radius:10px;border:1.5px solid #e9ecef;
            background:white;color:#555;font-size:12px;font-weight:700;
            cursor:pointer;font-family:'Poppins',sans-serif;text-decoration:none;
            transition:0.2s;
        }
        .export-btn:hover { border-color:#007bff;color:#007bff; }
        .bar-wrap { display:flex;flex-direction:column;gap:10px; }
        .bar-row { display:flex;align-items:center;gap:12px;font-size:12px; }
        .bar-label { width:60px;color:#888;text-align:right;flex-shrink:0; }
        .bar-track { flex:1;height:10px;background:#f0f4f8;border-radius:20px;overflow:hidden; }
        .bar-fill  { height:100%;border-radius:20px;background:linear-gradient(90deg,#007bff,#00d2ff);transition:width 0.8s cubic-bezier(.34,1.56,.64,1); }
        .bar-val   { width:80px;text-align:right;font-weight:700;color:#1a1a1a;flex-shrink:0; }
        .summary-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px; }
        
        /* CSS Tambahan untuk Action Buttons */
        .action-btn { padding:6px 10px; border-radius:6px; border:none; cursor:pointer; font-size:12px; font-weight:600; display:inline-flex; align-items:center; gap:4px; transition:0.2s; }
        .btn-edit { background:#e8f4ff; color:#007bff; }
        .btn-edit:hover { background:#d0e8ff; }
        .btn-delete { background:#fef0f0; color:#e74c3c; }
        .btn-delete:hover { background:#fad4d4; }
        
        @media(max-width:900px){ .summary-grid{grid-template-columns:repeat(2,1fr);} }
        @media(max-width:500px){ .summary-grid{grid-template-columns:1fr 1fr;} }
    </style>
</head>
<body>

<?php include __DIR__ . '/admin_sidebar.php'; ?>

<div class="admin-content">
    <div class="admin-topbar">
        <div>
            <h1 class="page-title">Kelola Donasi</h1>
            <p class="page-sub">Rincian seluruh transaksi donasi masuk</p>
        </div>
        <button class="export-btn" onclick="exportCSV()">
            <i data-lucide="download" style="width:14px;height:14px;"></i> Export CSV
        </button>
    </div>

    <div class="summary-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f4ff;">
                <i data-lucide="piggy-bank" style="color:#007bff;width:22px;height:22px;"></i>
            </div>
            <div class="stat-val" style="font-size:18px;">Rp <?= number_format($totalNominal,0,',','.') ?></div>
            <div class="stat-lbl">Total Terkumpul</div>
            <div class="stat-sub"><?= $totalCount ?> transaksi</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f9f0;">
                <i data-lucide="calendar" style="color:#27ae60;width:22px;height:22px;"></i>
            </div>
            <div class="stat-val" style="font-size:18px;">Rp <?= number_format($bulanIni,0,',','.') ?></div>
            <div class="stat-lbl">Bulan Ini</div>
            <div class="stat-sub"><?= date('F Y') ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fce8f5;">
                <i data-lucide="trending-up" style="color:#e91e8c;width:22px;height:22px;"></i>
            </div>
            <div class="stat-val" style="font-size:18px;">Rp <?= number_format($rerata,0,',','.') ?></div>
            <div class="stat-lbl">Rata-rata Donasi</div>
            <div class="stat-sub">Per transaksi</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff8e1;">
                <i data-lucide="users" style="color:#f39c12;width:22px;height:22px;"></i>
            </div>
            <div class="stat-val" style="font-size:18px;"><?= $totalCount ?></div>
            <div class="stat-lbl">Total Donatur</div>
            <div class="stat-sub">Semua waktu</div>
        </div>
    </div>

    <?php if (!empty($byMonth)): ?>
    <div class="table-card" style="margin-bottom:22px;">
        <div class="table-card-head">
            <h3><i data-lucide="bar-chart-2" style="width:16px;height:16px;color:#007bff;"></i> Donasi per Bulan</h3>
        </div>
        <?php $maxMonth = max(array_column($byMonth, 'total')) ?: 1; ?>
        <div class="bar-wrap">
            <?php foreach ($byMonth as $m):
                $pct = round(($m['total'] / $maxMonth) * 100);
                $bulan = date('M Y', strtotime($m['bln'] . '-01'));
            ?>
            <div class="bar-row">
                <div class="bar-label"><?= $bulan ?></div>
                <div class="bar-track">
                    <div class="bar-fill" style="width:<?= $pct ?>%;"></div>
                </div>
                <div class="bar-val">Rp <?= number_format($m['total'],0,',','.') ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;align-items:center;">
        <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;flex:1;align-items:center;">
            <input type="hidden" name="status" value="<?= htmlspecialchars($filter) ?>">
            <div style="display:flex;gap:8px;">
                <?php
                $statusTabs = ['all'=>'Semua','pending'=>'Pending','sukses'=>'Sukses','gagal'=>'Gagal'];
                foreach ($statusTabs as $k => $v):
                    $active = $filter === $k ? 'style="background:#1a1f36;color:white;border-color:#1a1f36;"' : '';
                ?>
                <a href="?status=<?= $k ?>&q=<?= urlencode($search) ?>"
                   style="padding:8px 16px;border-radius:10px;border:1.5px solid #e9ecef;background:white;color:#666;font-size:12px;font-weight:700;text-decoration:none;" <?= $active ?>>
                    <?= $v ?>
                </a>
                <?php endforeach; ?>
            </div>
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                   class="search-input" placeholder="Cari nama atau email donatur..."
                   style="margin-left:auto;min-width:220px;">
            <button type="submit" style="padding:9px 18px;background:#007bff;color:white;border:none;border-radius:10px;font-family:'Poppins',sans-serif;font-size:13px;font-weight:700;cursor:pointer;">Cari</button>
        </form>
    </div>

    <div class="table-card">
        <div class="table-card-head">
            <h3><i data-lucide="list" style="width:16px;height:16px;color:#007bff;"></i> Rincian Donasi</h3>
            <span style="font-size:12px;color:#bbb;"><?= count($donasi) ?> data</span>
        </div>

        <?php if (empty($donasi)): ?>
        <div style="text-align:center;padding:60px 20px;color:#ccc;">
            <i data-lucide="inbox" style="width:48px;height:48px;opacity:0.25;"></i>
            <p style="margin-top:14px;font-size:14px;">Tidak ada data donasi ditemukan</p>
        </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table class="admin-table" id="donasiTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Donatur</th>
                    <th>Email</th>
                    <th>Jumlah</th>
                    <th>Pesan</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($donasi as $i => $d):
                $cls = $d['status']==='sukses' ? 'pill-green' : ($d['status']==='gagal' ? 'pill-red' : 'pill-orange');
                $lbl = ucfirst($d['status']);
            ?>
            <tr>
                <td style="color:#bbb;font-size:12px;"><?= $i+1 ?></td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#e91e8c,#a55eea);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:white;flex-shrink:0;">
                            <?= strtoupper(substr($d['nama_donatur']??'D',0,1)) ?>
                        </div>
                        <span style="font-weight:700;font-size:13px;"><?= htmlspecialchars($d['nama_donatur']??'-') ?></span>
                    </div>
                </td>
                <td style="font-size:13px;color:#888;"><?= htmlspecialchars($d['email_donatur']??'-') ?></td>
                <td style="font-weight:800;color:#007bff;white-space:nowrap;font-size:14px;">
                    Rp <?= number_format($d['jumlah'],0,',','.') ?>
                </td>
                <td style="max-width:180px;">
                    <div style="font-size:12px;color:#666;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;max-width:180px;" title="<?= htmlspecialchars($d['pesan']??'') ?>">
                        <?= htmlspecialchars($d['pesan'] ?: '-') ?>
                    </div>
                </td>
                <td><span class="badge-pill <?= $cls ?>"><?= $lbl ?></span></td>
                <td style="font-size:12px;color:#bbb;white-space:nowrap;">
                    <?= date('d M Y, H:i', strtotime($d['created_at'])) ?>
                </td>
                
                <td>
                    <div style="display:flex;gap:6px;">
                        <button type="button" class="action-btn btn-edit" onclick="openUpdateModal(<?= $d['id'] ?>, '<?= htmlspecialchars($d['status']) ?>')" title="Update Status">
                            <i data-lucide="edit-2" style="width:14px;height:14px;"></i>
                        </button>
                        <button type="button" class="action-btn btn-delete" onclick="openDeleteModal(<?= $d['id'] ?>)" title="Hapus Data">
                            <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                        </button>
                    </div>
                </td>
                
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <div style="margin-top:16px;padding:16px 20px;background:#f8fbff;border-radius:12px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;border:1px solid #f0f4f8;">
            <span style="font-size:13px;color:#888;">Total dari <?= count($donasi) ?> transaksi yang ditampilkan</span>
            <?php $filteredTotal = array_sum(array_column($donasi,'jumlah')); ?>
            <span style="font-size:16px;font-weight:800;color:#007bff;">Rp <?= number_format($filteredTotal,0,',','.') ?></span>
        </div>
        <?php endif; ?>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <div>
            <h2 style="font-size:16px;font-weight:800;color:#1a1a1a;display:flex;align-items:center;gap:8px;">
                <i data-lucide="mail" style="width:18px;height:18px;color:#007bff;"></i> Log Notifikasi Email
            </h2>
            <p style="font-size:12px;color:#aaa;margin-top:3px;">Email konfirmasi yang dikirim otomatis ke donatur</p>
        </div>
        <div style="display:flex;gap:10px;">
            <div style="display:flex;align-items:center;gap:6px;padding:7px 14px;background:#e8f9f0;border-radius:20px;">
                <i data-lucide="check-circle" style="width:13px;height:13px;color:#27ae60;"></i>
                <span style="font-size:12px;font-weight:700;color:#27ae60;"><?= $emailSent ?> Terkirim</span>
            </div>
            <div style="display:flex;align-items:center;gap:6px;padding:7px 14px;background:#fef0f0;border-radius:20px;">
                <i data-lucide="x-circle" style="width:13px;height:13px;color:#e74c3c;"></i>
                <span style="font-size:12px;font-weight:700;color:#e74c3c;"><?= $emailFailed ?> Gagal</span>
            </div>
            <a href="/admin/preview_email.php" target="_blank" style="display:flex;align-items:center;gap:6px;padding:7px 14px;background:#f0f4f8;border-radius:20px;text-decoration:none;">
                <i data-lucide="eye" style="width:13px;height:13px;color:#888;"></i>
                <span style="font-size:12px;font-weight:700;color:#888;">Preview Template</span>
            </a>
        </div>
    </div>

    <div class="table-card">
        <div class="table-card-head">
            <h3><i data-lucide="inbox" style="width:16px;height:16px;color:#007bff;"></i> Riwayat Email</h3>
            <span style="font-size:12px;color:#bbb;"><?= count($emailLogs) ?> terbaru</span>
        </div>

        <?php if (empty($emailLogs)): ?>
        <div style="text-align:center;padding:50px 20px;color:#ccc;">
            <i data-lucide="mail" style="width:44px;height:44px;opacity:0.2;"></i>
            <p style="margin-top:12px;font-size:13px;">Belum ada log email. Email akan tercatat saat donasi pertama masuk.</p>
        </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Penerima</th>
                    <th>Subjek</th>
                    <th>Kode Donasi</th>
                    <th>Status</th>
                    <th>Waktu Kirim</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($emailLogs as $i => $el):
                $isSent  = $el['status'] === 'sent';
                $kode    = $el['donasi_id'] ? 'MDX-' . str_pad($el['donasi_id'], 6, '0', STR_PAD_LEFT) : '-';
            ?>
            <tr>
                <td style="color:#bbb;font-size:12px;"><?= $i+1 ?></td>
                <td>
                    <div style="display:flex;align-items:center;gap:9px;">
                        <div style="width:30px;height:30px;border-radius:50%;background:<?= $isSent ? 'linear-gradient(135deg,#007bff,#00d2ff)' : 'linear-gradient(135deg,#e74c3c,#ff6b81)' ?>;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:white;flex-shrink:0;">
                            <?= strtoupper(substr($el['to_name']??$el['to_email'],0,1)) ?>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:13px;"><?= htmlspecialchars($el['to_name']??'-') ?></div>
                            <div style="font-size:11px;color:#aaa;"><?= htmlspecialchars($el['to_email']) ?></div>
                        </div>
                    </div>
                </td>
                <td style="font-size:12px;color:#666;max-width:180px;">
                    <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;" title="<?= htmlspecialchars($el['subject']??'') ?>">
                        <?= htmlspecialchars($el['subject']??'-') ?>
                    </div>
                </td>
                <td>
                    <code style="background:#f0f4f8;padding:2px 8px;border-radius:6px;font-size:12px;color:#555;"><?= $kode ?></code>
                </td>
                <td>
                    <?php if ($isSent): ?>
                        <span class="badge-pill pill-green" style="display:inline-flex;align-items:center;gap:4px;">
                            <i data-lucide="check" style="width:10px;height:10px;"></i> Terkirim
                        </span>
                    <?php else: ?>
                        <span class="badge-pill pill-red" style="display:inline-flex;align-items:center;gap:4px;" title="<?= htmlspecialchars($el['error_msg']??'') ?>">
                            <i data-lucide="x" style="width:10px;height:10px;"></i> Gagal
                        </span>
                    <?php endif; ?>
                </td>
                <td style="font-size:12px;color:#bbb;white-space:nowrap;">
                    <?= date('d M Y, H:i', strtotime($el['created_at'])) ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<div id="updateModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; padding:24px; border-radius:12px; width:100%; max-width:350px; box-shadow:0 10px 25px rgba(0,0,0,0.15);">
        <h3 style="margin-top:0; margin-bottom:16px; font-size:16px; color:#1a1a1a; display:flex; align-items:center; gap:8px;">
            <i data-lucide="edit" style="width:18px;height:18px;color:#007bff;"></i> Update Status Donasi
        </h3>
        <form method="POST">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="donasi_id" id="modal_donasi_id">
            
            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:12px; font-weight:700; color:#555; margin-bottom:8px;">Pilih Status Baru</label>
                <select name="status" id="modal_status" style="width:100%; padding:10px 14px; border-radius:8px; border:1.5px solid #e9ecef; font-family:'Poppins',sans-serif; font-size:13px; outline:none;">
                    <option value="pending">Pending</option>
                    <option value="sukses">Selesai</option>
                </select>
            </div>
            
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeUpdateModal()" style="padding:9px 16px; border-radius:8px; border:none; background:#f0f4f8; color:#555; font-weight:700; font-family:'Poppins',sans-serif; font-size:12px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:9px 16px; border-radius:8px; border:none; background:#007bff; color:white; font-weight:700; font-family:'Poppins',sans-serif; font-size:12px; cursor:pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; padding:24px; border-radius:12px; width:100%; max-width:350px; box-shadow:0 10px 25px rgba(0,0,0,0.15); text-align:center;">
        <div style="width:50px; height:50px; border-radius:50%; background:#fef0f0; color:#e74c3c; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
            <i data-lucide="alert-triangle" style="width:24px; height:24px;"></i>
        </div>
        <h3 style="margin-top:0; margin-bottom:8px; font-size:16px; color:#1a1a1a;">Hapus Data Donasi?</h3>
        <p style="font-size:13px; color:#666; margin-bottom:20px; line-height:1.5;">Data yang dihapus tidak dapat dikembalikan lagi. Yakin ingin melanjutkan?</p>
        
        <form method="POST">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="donasi_id" id="modal_delete_donasi_id">
            
            <div style="display:flex; justify-content:center; gap:10px;">
                <button type="button" onclick="closeDeleteModal()" style="padding:9px 16px; border-radius:8px; border:none; background:#f0f4f8; color:#555; font-weight:700; font-family:'Poppins',sans-serif; font-size:12px; cursor:pointer; flex:1;">Batal</button>
                <button type="submit" style="padding:9px 16px; border-radius:8px; border:none; background:#e74c3c; color:white; font-weight:700; font-family:'Poppins',sans-serif; font-size:12px; cursor:pointer; flex:1;">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
lucide.createIcons();

// Update Status
function openUpdateModal(id, currentStatus) {
    document.getElementById('modal_donasi_id').value = id;
    document.getElementById('modal_status').value = currentStatus;
    document.getElementById('updateModal').style.display = 'flex';
}

function closeUpdateModal() {
    document.getElementById('updateModal').style.display = 'none';
}

// Hapus
function openDeleteModal(id) {
    document.getElementById('modal_delete_donasi_id').value = id;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// --- Export CSV ---
function exportCSV() {
    const rows = [['No','Nama Donatur','Email','Jumlah','Pesan','Status','Tanggal']];
    document.querySelectorAll('#donasiTable tbody tr').forEach((tr, i) => {
        const cells = tr.querySelectorAll('td');
        rows.push([
            i+1,
            cells[1]?.querySelector('span')?.textContent?.trim() || '',
            cells[2]?.textContent?.trim() || '',
            cells[3]?.textContent?.trim() || '',
            cells[4]?.textContent?.trim() || '',
            cells[5]?.textContent?.trim() || '',
            cells[6]?.textContent?.trim() || '',
        ]);
    });
    const csv = rows.map(r => r.map(v => '"'+String(v).replace(/"/g,'""')+'"').join(',')).join('\n');
    const a = document.createElement('a');
    a.href = 'data:text/csv;charset=utf-8,\uFEFF' + encodeURIComponent(csv);
    a.download = 'laporan_donasi_<?= date('Ymd') ?>.csv';
    a.click();
}
</script>
</body>
</html>