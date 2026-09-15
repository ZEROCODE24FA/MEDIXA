<?php
$currentPage = basename($_SERVER['PHP_SELF']);
function navLink($href, $icon, $label, $current) {
    $active = ($current === basename($href)) ? ' active' : '';
    return "<a href=\"$href\" class=\"nav-link$active\">
        <i data-lucide=\"$icon\" class=\"nav-icon\"></i> $label
    </a>";
}
?>
<!-- Mobile topbar -->
<div class="mobile-topbar">
    <a href="/home.php" class="mobile-logo">MEDI<span>XA</span></a>
    <button class="hamburger-btn" onclick="toggleMobileSidebar()" id="hamburgerBtn">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-logo">
        <div class="logo-text">MEDI<span>XA</span></div>
        <p>Panel Administrator</p>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Utama</div>
        <?= navLink('dashboard.php', 'layout-dashboard', 'Dashboard', $currentPage) ?>
        <?= navLink('penerima.php', 'heart-handshake', 'Penerima Donasi', $currentPage) ?>
        <?= navLink('pengguna.php', 'users', 'Kelola Pengguna', $currentPage) ?>
        <?= navLink('kelola_donasi.php', 'bar-chart-2', 'Kelola Donasi', $currentPage) ?>

        <div class="nav-section">Lainnya</div>
        <?= navLink('../home.php', 'globe', 'Lihat Website', $currentPage) ?>
    </nav>

    <div class="sidebar-footer">
        <a href="../api/auth_logout.php" class="btn-logout">
            <i data-lucide="log-out" style="width:16px;height:16px;"></i> Keluar
        </a>
    </div>
</aside>
<script>
function toggleMobileSidebar() {
    const sb = document.getElementById('adminSidebar');
    const ov = document.getElementById('sidebarOverlay');
    sb.classList.toggle('open');
    ov.classList.toggle('open');
}
function closeMobileSidebar() {
    document.getElementById('adminSidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('open');
}
</script>
