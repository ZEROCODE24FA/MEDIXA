<?php
require_once __DIR__ . '/api/config.php';
sessionStart();
requireLogin();
$user = getCurrentUser();

$db = getDB();
$wpRow = $db->prepare("SELECT wallpaper FROM users WHERE id = ?");
$wpRow->execute([$user['id']]);
$wallpaper = $wpRow->fetchColumn() ?: 'gradient-1';

$obatHistory = $db->prepare("SELECT nama_obat, kategori, created_at FROM obat_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 20");
$obatHistory->execute([$user['id']]);
$obatRiwayat = $obatHistory->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - MEDIXA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>

        .wp-gradient-1 { background: linear-gradient(135deg, #007bff 0%, #00d2ff 100%); }
        .wp-gradient-2 { background: linear-gradient(135deg, #a55eea 0%, #6c3fc0 100%); }
        .wp-gradient-3 { background: linear-gradient(135deg, #ff4757 0%, #ff6b81 100%); }
        .wp-gradient-4 { background: linear-gradient(135deg, #27ae60 0%, #1abc9c 100%); }
        .wp-gradient-5 { background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%); }
        .wp-gradient-6 { background: linear-gradient(135deg, #2c3e50 0%, #4a6fa5 100%); }
        .wp-gradient-7 { background: linear-gradient(135deg, #e91e8c 0%, #a55eea 100%); }
        .wp-gradient-8 { background: linear-gradient(135deg, #16a085 0%, #007bff 100%); }

        * { box-sizing: border-box; }

        .profile-page {
            max-width: 960px;
            margin: 0 auto;
            padding: 40px 20px 80px;
        }


        .profile-header-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid #f0f4f8;
            box-shadow: 0 8px 30px rgba(0,0,0,0.04);
            margin-bottom: 24px;
        }


        .profile-banner {
            width: 100%;
            height: 150px;
            position: relative;
        }
        .profile-banner #bannerInner {
            width: 100%; height: 100%;
        }
        .btn-change-wp {
            position: absolute;
            top: 14px; right: 16px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(8px);
            border: 1.5px solid rgba(255,255,255,0.4);
            color: white;
            border-radius: 10px;
            padding: 7px 14px;
            font-family: 'Poppins', sans-serif;
            font-size: 12px; font-weight: 700;
            cursor: pointer;
            display: flex; align-items: center; gap: 6px;
            transition: 0.2s;
        }
        .btn-change-wp:hover { background: rgba(255,255,255,0.32); transform: translateY(-1px); }


        .profile-info-row {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px 28px 24px;
        }
        .avatar-circle-lg {
            width: 80px; height: 80px;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 4px 18px rgba(0,0,0,0.13);
            display: flex; align-items: center; justify-content: center;
            font-size: 30px; font-weight: 800; color: white;
            flex-shrink: 0;
            margin-top: -52px;
            position: relative; z-index: 2;
        }
        .profile-meta { flex: 1; }
        .profile-meta h2 { font-size: 20px; font-weight: 800; color: #1a1a1a; margin-bottom: 2px; }
        .profile-meta p  { font-size: 13px; color: #888; }
        .role-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
            color: white; margin-top: 6px;
        }


        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 28px;
            border: 1px solid #f0f4f8;
            box-shadow: 0 6px 24px rgba(0,0,0,0.025);
        }
        .card-title {
            display: flex; align-items: center; gap: 10px;
            font-size: 15px; font-weight: 700; color: #1a1a1a;
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f5f5f5;
        }
        .ct-icon {
            width: 34px; height: 34px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }


        .form-field { margin-bottom: 16px; }
        .form-field label {
            display: block; font-size: 11px; font-weight: 700;
            color: #aaa; margin-bottom: 7px;
            text-transform: uppercase; letter-spacing: 0.6px;
        }
        .form-field input {
            width: 100%; padding: 11px 14px;
            border: 1px solid #e9ecef; border-radius: 11px;
            font-size: 14px; outline: none;
            font-family: 'Poppins', sans-serif;
            background: #f8fbff; transition: 0.3s; color: #333;
        }
        .form-field input:focus { border-color: #007bff; background: white; box-shadow: 0 0 0 3px rgba(0,123,255,0.08); }
        .form-field input:disabled { opacity: 0.45; cursor: not-allowed; background: #f0f0f0; }
        .btn-save {
            background: linear-gradient(90deg, #007bff, #00d2ff);
            color: white; border: none; padding: 12px 24px;
            border-radius: 11px; font-weight: 700; cursor: pointer;
            font-size: 14px; font-family: 'Poppins', sans-serif;
            transition: 0.3s; display: flex; align-items: center; gap: 7px;
            margin-top: 4px;
        }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,123,255,0.3); }


        .obat-history-list { display: flex; flex-direction: column; gap: 8px; max-height: 320px; overflow-y: auto; padding-right: 2px; }
        .obat-history-list::-webkit-scrollbar { width: 3px; }
        .obat-history-list::-webkit-scrollbar-thumb { background: #e0e0e0; border-radius: 10px; }
        .oh-item {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 14px; background: #f8fbff;
            border-radius: 12px; border: 1px solid #f0f4f8;
            text-decoration: none; color: inherit; transition: 0.2s;
        }
        .oh-item:hover { background: #f0f7ff; border-color: #d0e8ff; }
        .oh-icon {
            width: 34px; height: 34px; border-radius: 9px;
            background: #e0f9f8; display: flex; align-items: center;
            justify-content: center; flex-shrink: 0;
        }
        .oh-name { font-size: 13px; font-weight: 700; color: #1a1a1a; }
        .oh-cat  { font-size: 11px; color: #999; margin-top: 1px; }
        .oh-time { font-size: 11px; color: #ccc; margin-left: auto; white-space: nowrap; flex-shrink: 0; }

        .empty-state { text-align: center; padding: 36px 20px; color: #ccc; }
        .empty-state p { font-size: 13px; margin-top: 10px; line-height: 1.7; }


        .wp-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.4); z-index: 9000;
            align-items: center; justify-content: center;
        }
        .wp-overlay.open { display: flex; }
        .wp-modal {
            background: white; border-radius: 22px; padding: 30px;
            width: 400px; max-width: 95vw;
            box-shadow: 0 25px 60px rgba(0,0,0,0.15);
        }
        .wp-modal h3 { font-size: 17px; font-weight: 800; margin-bottom: 4px; }
        .wp-modal p  { font-size: 13px; color: #888; margin-bottom: 20px; }
        .wp-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 10px; margin-bottom: 20px; }
        .wp-swatch {
            height: 56px; border-radius: 12px; cursor: pointer;
            border: 3px solid transparent; transition: 0.2s; position: relative;
        }
        .wp-swatch:hover { transform: scale(1.06); }
        .wp-swatch.selected { border-color: #222; box-shadow: 0 0 0 2px white inset; }
        .wp-check {
            position: absolute; inset: 0; display: flex;
            align-items: center; justify-content: center; opacity: 0; transition: 0.15s;
        }
        .wp-swatch.selected .wp-check { opacity: 1; }
        .wp-actions { display: flex; gap: 10px; }
        .btn-wp-save {
            flex: 1; padding: 12px; background: linear-gradient(90deg,#007bff,#00d2ff);
            color: white; border: none; border-radius: 11px;
            font-family: 'Poppins',sans-serif; font-weight: 700;
            font-size: 14px; cursor: pointer; transition: 0.25s;
        }
        .btn-wp-save:hover { box-shadow: 0 6px 20px rgba(0,123,255,0.3); transform: translateY(-1px); }
        .btn-wp-cancel {
            padding: 12px 18px; border: 1.5px solid #e9ecef;
            border-radius: 11px; background: white;
            font-family: 'Poppins',sans-serif; font-weight: 600;
            font-size: 14px; cursor: pointer; color: #666; transition: 0.2s;
        }
        .btn-wp-cancel:hover { background: #f5f5f5; }

        @media(max-width:768px){
            .profile-page { padding: 20px 14px 60px; }
            .profile-grid { grid-template-columns: 1fr; }
            .profile-info-row { gap: 12px; padding: 14px 16px 18px; }
            .avatar-circle-lg { width: 64px; height: 64px; font-size: 22px; margin-top: -38px; }
            .profile-banner { height: 120px; }
            .profile-meta h2 { font-size: 17px; }
            .profile-meta p { font-size: 12px; }
            .profile-card { padding: 20px 18px; }
            .wp-modal { padding: 22px 18px; }
            .wp-grid { grid-template-columns: repeat(4,1fr); gap: 8px; }
            .wp-swatch { height: 44px; }
        }
        @media(max-width:480px){
            .profile-info-row { flex-direction: row; }
            .profile-banner { height: 100px; }
            .avatar-circle-lg { width: 56px; height: 56px; font-size: 19px; margin-top: -32px; }
            .btn-change-wp { font-size: 11px; padding: 5px 10px; }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/includes/navbar.php'; ?>

<div class="profile-page">


    <div class="profile-header-card">

        <div class="profile-banner">
            <div class="wp-<?= htmlspecialchars($wallpaper) ?>" id="bannerInner" style="width:100%;height:100%;position:relative;">
                <button class="btn-change-wp" onclick="openWpPicker()">
                    <i data-lucide="image" style="width:13px;height:13px;"></i> Ganti Wallpaper
                </button>
            </div>
        </div>


        <div class="profile-info-row">
            <div class="avatar-circle-lg wp-<?= htmlspecialchars($wallpaper) ?>" id="avatarCircle">
                <?= strtoupper(substr($user['nama'], 0, 1)) ?>
            </div>
            <div class="profile-meta">
                <h2><?= htmlspecialchars($user['nama']) ?></h2>
                <p><?= htmlspecialchars($user['email']) ?></p>
                <span class="role-badge wp-<?= htmlspecialchars($wallpaper) ?>" id="roleBadge">
                    <i data-lucide="shield-check" style="width:11px;height:11px;"></i>
                    <?= ucfirst($user['role']) ?>
                </span>
            </div>
        </div>
    </div>


    <div class="profile-grid">


        <div class="profile-card">
            <div class="card-title">
                <div class="ct-icon" style="background:#e8f4ff;">
                    <i data-lucide="user-cog" style="color:#007bff;width:16px;height:16px;"></i>
                </div>
                Edit Profil
            </div>

            <div id="profile-alert" style="display:none;padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:14px;font-weight:500;"></div>

            <form id="profileForm">
                <div class="form-field">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                </div>
                <div class="form-field">
                    <label>Email</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                </div>
                <div class="form-field">
                    <label>Nomor Telepon</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="08xxxxxxxxxx">
                </div>
                <button type="submit" class="btn-save">
                    <i data-lucide="save" style="width:15px;height:15px;"></i> Simpan Perubahan
                </button>
            </form>
        </div>


        <div class="profile-card">
            <div class="card-title">
                <div class="ct-icon" style="background:#e0f9f8;">
                    <i data-lucide="pill" style="color:#20e2d7;width:16px;height:16px;"></i>
                </div>
                Riwayat Pencarian Obat
            </div>

            <?php if (count($obatRiwayat) > 0): ?>
            <div class="obat-history-list">
                <?php foreach ($obatRiwayat as $r):
                    $diff = time() - strtotime($r['created_at']);
                    if ($diff < 60) $timeStr = 'Baru saja';
                    elseif ($diff < 3600) $timeStr = floor($diff/60) . ' mnt lalu';
                    elseif ($diff < 86400) $timeStr = floor($diff/3600) . ' jam lalu';
                    else $timeStr = date('d M Y', strtotime($r['created_at']));
                ?>
                <a href="<?= BASE_PATH ?>/fitur/pintarObat.php" class="oh-item">
                    <div class="oh-icon">
                        <i data-lucide="pill" style="color:#20e2d7;width:16px;height:16px;"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="oh-name"><?= htmlspecialchars($r['nama_obat']) ?></div>
                        <div class="oh-cat"><?= htmlspecialchars($r['kategori']) ?></div>
                    </div>
                    <span class="oh-time"><?= $timeStr ?></span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i data-lucide="search" style="width:40px;height:40px;opacity:0.18;"></i>
                <p>Belum ada riwayat pencarian obat.<br>
                <a href="<?= BASE_PATH ?>/fitur/pintarObat.php" style="color:#20e2d7;font-weight:700;text-decoration:none;">Buka Pintar Obat →</a></p>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>


<div class="wp-overlay" id="wpOverlay" onclick="if(event.target===this) closeWpPicker()">
    <div class="wp-modal">
        <h3>Ganti Wallpaper</h3>
        <p>Pilih tema warna untuk profil Anda</p>

        <div class="wp-grid">
            <?php
            $wpList = [
                'gradient-1'=>'Biru Langit','gradient-2'=>'Ungu Mistis',
                'gradient-3'=>'Merah Coral','gradient-4'=>'Hijau Segar',
                'gradient-5'=>'Kuning Emas','gradient-6'=>'Biru Gelap',
                'gradient-7'=>'Pink Aurora','gradient-8'=>'Tosca Laut',
            ];
            foreach ($wpList as $key => $label):
            ?>
            <div class="wp-swatch wp-<?= $key ?> <?= $wallpaper === $key ? 'selected' : '' ?>"
                 data-wp="<?= $key ?>"
                 onclick="pickWp('<?= $key ?>', this)"
                 title="<?= $label ?>">
                <div class="wp-check">
                    <i data-lucide="check" style="color:white;width:20px;height:20px;filter:drop-shadow(0 1px 3px rgba(0,0,0,0.4));"></i>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div id="wp-msg" style="display:none;margin-bottom:14px;padding:10px 14px;border-radius:10px;font-size:13px;font-weight:500;"></div>

        <div class="wp-actions">
            <button class="btn-wp-save" id="btnWpSave" onclick="saveWp()">Terapkan</button>
            <button class="btn-wp-cancel" onclick="closeWpPicker()">Batal</button>
        </div>
    </div>
</div>

<script>
lucide.createIcons();

let pickedWp = '<?= $wallpaper ?>';
const ALL_WP  = ['gradient-1','gradient-2','gradient-3','gradient-4','gradient-5','gradient-6','gradient-7','gradient-8'];

function openWpPicker()  { document.getElementById('wpOverlay').classList.add('open'); }
function closeWpPicker() { document.getElementById('wpOverlay').classList.remove('open'); }

function applyWpToPage(wp) {
    const targets = [
        document.getElementById('bannerInner'),
        document.getElementById('avatarCircle'),
        document.getElementById('roleBadge'),
    ];
    targets.forEach(el => {
        if (!el) return;
        ALL_WP.forEach(w => el.classList.remove('wp-' + w));
        el.classList.add('wp-' + wp);
    });
    lucide.createIcons();
}

function pickWp(wp, el) {
    pickedWp = wp;
    document.querySelectorAll('.wp-swatch').forEach(s => s.classList.remove('selected'));
    el.classList.add('selected');
    applyWpToPage(wp); // live preview
}

async function saveWp() {
    const btn = document.getElementById('btnWpSave');
    btn.textContent = 'Menyimpan...';
    btn.disabled = true;

    try {
        const fd = new FormData();
        fd.append('wallpaper', pickedWp);
        const res  = await fetch('api/profile_wallpaper.php', { method:'POST', body:fd });
        const data = await res.json();

        if (data.success) {
            closeWpPicker();
        } else {
            const msg = document.getElementById('wp-msg');
            msg.style.display = 'block';
            msg.textContent = data.message;
            msg.style.background = '#fef0f0';
            msg.style.color = '#e74c3c';
        }
    } catch (err) {
        console.error('Gagal simpan wallpaper:', err);
        const msg = document.getElementById('wp-msg');
        msg.style.display = 'block';
        msg.textContent = 'Terjadi kesalahan, coba lagi';
        msg.style.background = '#fef0f0';
        msg.style.color = '#e74c3c';
    }

    btn.textContent = 'Terapkan';
    btn.disabled = false;
}


document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = this.querySelector('.btn-save');
    btn.innerHTML = '<div style="width:15px;height:15px;border:2px solid rgba(255,255,255,0.4);border-top-color:white;border-radius:50%;animation:spin .8s linear infinite;display:inline-block;"></div> Menyimpan...';
    btn.disabled = true;

    const res  = await fetch('api/profile_update.php', { method:'POST', body:new FormData(this) });
    const data = await res.json();

    const al = document.getElementById('profile-alert');
    al.style.display  = 'block';
    al.textContent    = data.message;
    al.style.background = data.success ? '#e8f9f0' : '#fef0f0';
    al.style.color      = data.success ? '#27ae60' : '#e74c3c';
    al.style.border     = '1px solid ' + (data.success ? '#c3f0d8' : '#fdd');

    btn.innerHTML = '<i data-lucide="save" style="width:15px;height:15px;"></i> Simpan Perubahan';
    btn.disabled  = false;
    lucide.createIcons();
});
</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>
</body>
</html>
