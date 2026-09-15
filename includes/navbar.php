<?php
require_once __DIR__ . '/../api/config.php';
sessionStart();
$user = getCurrentUser();
$isLogged = isLoggedIn();
$basePath = $basePath ?? '';
?>
<nav class="navbar">
    <a href="<?= BASE_PATH ?>/home.php" class="logo">
        <i data-lucide="shield-plus"></i> MEDIXA
    </a>


    <div class="nav-links" id="navLinks">
        <a href="<?= BASE_PATH ?>/home.php" <?= basename($_SERVER['PHP_SELF']) === 'home.php' ? 'class="active"' : '' ?>>
            <i data-lucide="home"></i> Beranda
        </a>
        <a href="<?= BASE_PATH ?>/fitur/kalkulatorBMI.php" <?= basename($_SERVER['PHP_SELF']) === 'kalkulatorBMI.php' ? 'class="active"' : '' ?>>
            <i data-lucide="calculator"></i> Kalkulator BMI
        </a>
        <a href="<?= BASE_PATH ?>/fitur/pintarObat.php" <?= basename($_SERVER['PHP_SELF']) === 'pintarObat.php' ? 'class="active"' : '' ?>>
            <i data-lucide="pill"></i> Pintar Obat
        </a>
        <a href="<?= BASE_PATH ?>/fitur/rumahSakit.php" <?= basename($_SERVER['PHP_SELF']) === 'rumahSakit.php' ? 'class="active"' : '' ?>>
            <i data-lucide="hospital"></i> Rumah Sakit
        </a>
        <a href="<?= BASE_PATH ?>/fitur/donasi.php" <?= basename($_SERVER['PHP_SELF']) === 'donasi.php' ? 'class="active"' : '' ?>>
            <i data-lucide="heart"></i> Donasi
        </a>

        <?php if ($isLogged): ?>
            <div class="nav-user-menu" style="position:relative;">
                <button onclick="toggleUserMenu()" class="btn-login-nav" style="display:flex;align-items:center;gap:8px;cursor:pointer;border:none;">
                    <div style="width:26px;height:26px;background:rgba(255,255,255,0.25);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;">
                        <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                    </div>
                    Profil
                    <i data-lucide="chevron-down" style="width:14px;height:14px;opacity:0.7;"></i>
                </button>
                <div id="userDropdown" style="display:none;position:absolute;right:0;top:115%;background:white;border-radius:16px;box-shadow:0 15px 40px rgba(0,0,0,0.12);min-width:210px;z-index:2000;overflow:hidden;border:1px solid #f0f0f0;">
                    <div style="padding:16px 18px 12px;border-bottom:1px solid #f5f5f5;">
                        <div style="font-weight:700;font-size:14px;color:#1a1a1a;"><?= htmlspecialchars($user['nama']) ?></div>
                        <div style="font-size:12px;color:#aaa;margin-top:2px;"><?= htmlspecialchars($user['email']) ?></div>
                    </div>
                    <a href="<?= BASE_PATH ?>/profile.php" class="nav-drop-item">
                        <i data-lucide="user-circle" style="width:15px;height:15px;color:#007bff;"></i> Edit Profil
                    </a>
                    <a href="<?= BASE_PATH ?>/fitur/registerPenerimaDonasi.php" class="nav-drop-item">
                        <i data-lucide="hand-heart" style="width:15px;height:15px;color:#a55eea;"></i> Daftar Penerima Donasi
                    </a>
                    <?php if ($user['role'] === 'admin'): ?>
                    <a href="<?= BASE_PATH ?>/admin/dashboard.php" class="nav-drop-item">
                        <i data-lucide="layout-dashboard" style="width:15px;height:15px;color:#27ae60;"></i> Dashboard Admin
                    </a>
                    <?php endif; ?>
                    <div style="border-top:1px solid #f5f5f5;margin-top:4px;"></div>
                    <a href="<?= BASE_PATH ?>/api/auth_logout.php" class="nav-drop-item" style="color:#e74c3c !important;">
                        <i data-lucide="log-out" style="width:15px;height:15px;color:#e74c3c;"></i> Keluar
                    </a>
                </div>
            </div>
        <?php else: ?>
            <a href="<?= BASE_PATH ?>/login.php" class="btn-login-nav"><i data-lucide="log-in"></i> Login</a>
        <?php endif; ?>
    </div>


    <button class="nav-hamburger" onclick="toggleMobileNav()" id="navHamburger" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>
</nav>


<div class="nav-mobile-overlay" id="navOverlay" onclick="closeMobileNav()"></div>
<div class="nav-mobile-drawer" id="navDrawer">
    <div class="drawer-header">
        <span class="logo">MEDIXA</span>
        <button onclick="closeMobileNav()" class="drawer-close">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
    <div class="drawer-links">
        <a href="<?= BASE_PATH ?>/home.php" class="drawer-link"><i data-lucide="home"></i> Beranda</a>
        <a href="<?= BASE_PATH ?>/fitur/kalkulatorBMI.php" class="drawer-link"><i data-lucide="calculator"></i> Kalkulator BMI</a>
        <a href="<?= BASE_PATH ?>/fitur/pintarObat.php" class="drawer-link"><i data-lucide="pill"></i> Pintar Obat</a>
        <a href="<?= BASE_PATH ?>/fitur/rumahSakit.php" class="drawer-link"><i data-lucide="hospital"></i> Rumah Sakit</a>
        <a href="<?= BASE_PATH ?>/fitur/donasi.php" class="drawer-link"><i data-lucide="heart"></i> Donasi</a>
        <div class="drawer-divider"></div>
        <?php if ($isLogged): ?>
            <div class="drawer-user">
                <div class="drawer-avatar"><?= strtoupper(substr($user['nama'],0,1)) ?></div>
                <div>
                    <div style="font-weight:700;font-size:14px;"><?= htmlspecialchars($user['nama']) ?></div>
                    <div style="font-size:11px;color:#aaa;"><?= htmlspecialchars($user['email']) ?></div>
                </div>
            </div>
            <a href="<?= BASE_PATH ?>/profile.php" class="drawer-link"><i data-lucide="user-circle"></i> Edit Profil</a>
            <a href="<?= BASE_PATH ?>/fitur/registerPenerimaDonasi.php" class="drawer-link"><i data-lucide="hand-heart"></i> Daftar Penerima Donasi</a>
            <?php if ($user['role'] === 'admin'): ?>
            <a href="<?= BASE_PATH ?>/admin/dashboard.php" class="drawer-link"><i data-lucide="layout-dashboard"></i> Dashboard Admin</a>
            <?php endif; ?>
            <a href="<?= BASE_PATH ?>/api/auth_logout.php" class="drawer-link" style="color:#e74c3c;"><i data-lucide="log-out"></i> Keluar</a>
        <?php else: ?>
            <a href="<?= BASE_PATH ?>/login.php" class="drawer-link-cta">
                <i data-lucide="log-in"></i> Login / Daftar
            </a>
        <?php endif; ?>
    </div>
</div>

<style>

.nav-drop-item {
    display:flex;align-items:center;gap:10px;padding:12px 18px;
    color:#333;font-size:13px;font-weight:500;text-decoration:none;
    transition:background 0.15s;
}
.nav-drop-item:hover { background:#f8fbff; }


.nav-hamburger {
    display: none;
    flex-direction: column; gap: 5px;
    background: none; border: none; cursor: pointer; padding: 6px;
}
.nav-hamburger span {
    display: block; width: 22px; height: 2.5px;
    background: #333; border-radius: 4px; transition: 0.3s;
}


.nav-mobile-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.4); z-index: 1500;
    backdrop-filter: blur(2px);
}
.nav-mobile-overlay.open { display: block; }


.nav-mobile-drawer {
    position: fixed; top: 0; right: -290px; bottom: 0;
    width: 280px; background: white; z-index: 1600;
    transition: right 0.32s cubic-bezier(.4,0,.2,1);
    display: flex; flex-direction: column;
    box-shadow: -8px 0 40px rgba(0,0,0,0.12);
}
.nav-mobile-drawer.open { right: 0; }

.drawer-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 20px 16px;
    border-bottom: 1px solid #f0f0f0;
}
.drawer-header .logo { color: #007bff; font-size: 20px; font-weight: 800; }
.drawer-close {
    background: #f5f5f5; border: none; border-radius: 8px;
    padding: 6px; cursor: pointer; color: #666; display: flex;
    align-items: center; transition: 0.2s;
}
.drawer-close:hover { background: #eee; }

.drawer-links { flex: 1; padding: 12px 14px; overflow-y: auto; }
.drawer-link {
    display: flex; align-items: center; gap: 12px;
    padding: 13px 12px; border-radius: 12px;
    color: #444; font-size: 14px; font-weight: 500;
    text-decoration: none; transition: 0.15s; margin-bottom: 2px;
}
.drawer-link svg { width: 18px; height: 18px; flex-shrink: 0; }
.drawer-link:hover { background: #f0f7ff; color: #007bff; }

.drawer-divider { height: 1px; background: #f5f5f5; margin: 10px 0; }

.drawer-user {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 12px; background: #f8fbff; border-radius: 12px;
    margin-bottom: 10px;
}
.drawer-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, #007bff, #00d2ff);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 800; color: white; flex-shrink: 0;
}

.drawer-link-cta {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 13px; border-radius: 12px;
    background: linear-gradient(90deg, #007bff, #00d2ff);
    color: white; font-size: 14px; font-weight: 700;
    text-decoration: none; margin-top: 6px; transition: 0.2s;
}
.drawer-link-cta:hover { opacity: 0.9; transform: translateY(-1px); }


@media (max-width: 860px) {
    .navbar { padding: 14px 5%; }
    .nav-links { display: none !important; }
    .nav-hamburger { display: flex; }
}
</style>
<script>
function toggleUserMenu() {
    const d = document.getElementById('userDropdown');
    if (!d) return;
    d.style.display = d.style.display !== 'none' ? 'none' : 'block';
}
document.addEventListener('click', function(e) {
    const menu = document.querySelector('.nav-user-menu');
    if (menu && !menu.contains(e.target)) {
        const d = document.getElementById('userDropdown');
        if (d) d.style.display = 'none';
    }
});

function toggleMobileNav() {
    document.getElementById('navDrawer').classList.toggle('open');
    document.getElementById('navOverlay').classList.toggle('open');
}
function closeMobileNav() {
    document.getElementById('navDrawer').classList.remove('open');
    document.getElementById('navOverlay').classList.remove('open');
}
</script>
