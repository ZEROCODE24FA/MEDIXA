<style>
*, *::before, *::after { box-sizing: border-box; }
body { margin: 0; font-family: 'Poppins', sans-serif; background: #f4f7fb; display: flex; min-height: 100vh; }


.admin-sidebar {
    width: 240px; flex-shrink: 0;
    background: #1a1f36;
    min-height: 100vh; position: fixed; top: 0; left: 0;
    display: flex; flex-direction: column;
    z-index: 100;
}
.sidebar-logo {
    padding: 24px 22px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.07);
}
.sidebar-logo .logo-text { font-size: 22px; font-weight: 800; color: white; }
.sidebar-logo .logo-text span { color: #4fc3f7; }
.sidebar-logo p { font-size: 10px; color: rgba(255,255,255,0.35); margin-top: 2px; letter-spacing: 0.5px; text-transform: uppercase; }

.sidebar-nav { flex: 1; padding: 16px 12px; }
.nav-section { font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.25); letter-spacing: 1px; text-transform: uppercase; padding: 0 10px; margin: 18px 0 8px; }
.nav-link {
    display: flex; align-items: center; gap: 11px;
    padding: 10px 12px; border-radius: 10px;
    color: rgba(255,255,255,0.55); text-decoration: none;
    font-size: 13px; font-weight: 600;
    transition: 0.2s; margin-bottom: 2px;
}
.nav-link:hover { background: rgba(255,255,255,0.07); color: white; }
.nav-link.active { background: linear-gradient(90deg, #4fc3f7, #0288d1); color: white; box-shadow: 0 4px 16px rgba(79,195,247,0.25); }
.nav-link .nav-icon { width: 18px; height: 18px; flex-shrink: 0; }

.sidebar-footer {
    padding: 16px 12px;
    border-top: 1px solid rgba(255,255,255,0.07);
}
.btn-logout {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px; border-radius: 10px;
    color: rgba(255,255,255,0.4); text-decoration: none;
    font-size: 13px; font-weight: 600; transition: 0.2s; width: 100%;
    background: none; border: none; cursor: pointer; font-family: 'Poppins', sans-serif;
}
.btn-logout:hover { background: rgba(239,83,80,0.12); color: #ef5350; }


.admin-content {
    margin-left: 240px; flex: 1; padding: 32px 32px 60px;
    max-width: calc(100vw - 240px);
}
.admin-topbar {
    display: flex; align-items: flex-start; justify-content: space-between;
    margin-bottom: 28px; gap: 16px;
}
.page-title { font-size: 24px; font-weight: 800; color: #1a1a1a; margin: 0 0 2px; }
.page-sub   { font-size: 13px; color: #999; margin: 0; }
.admin-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: white; border: 1px solid #e9ecef;
    border-radius: 50px; padding: 7px 14px;
    font-size: 12px; color: #555; font-weight: 600;
}


.stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 26px; }
.stat-card {
    background: white; border-radius: 18px;
    padding: 22px 20px; border: 1px solid #f0f4f8;
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
}
.stat-icon { width: 46px; height: 46px; border-radius: 13px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
.stat-val  { font-size: 26px; font-weight: 800; color: #1a1a1a; line-height: 1; margin-bottom: 4px; }
.stat-lbl  { font-size: 12px; font-weight: 600; color: #888; }
.stat-sub  { font-size: 11px; color: #bbb; margin-top: 4px; }


.dash-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.dash-card {
    background: white; border-radius: 18px;
    padding: 22px 22px; border: 1px solid #f0f4f8;
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
}
.dash-card-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.dash-card-head h3 { font-size: 14px; font-weight: 700; color: #1a1a1a; display: flex; align-items: center; gap: 8px; margin: 0; }
.link-more { font-size: 12px; color: #007bff; text-decoration: none; font-weight: 600; }
.link-more:hover { text-decoration: underline; }

/* Full table card */
.table-card {
    background: white; border-radius: 18px;
    padding: 24px; border: 1px solid #f0f4f8;
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
}
.table-card-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
.table-card-head h3 { font-size: 15px; font-weight: 700; color: #1a1a1a; display: flex; align-items: center; gap: 8px; margin: 0; }

/* Tables */
.admin-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.admin-table th { text-align: left; padding: 10px 14px; background: #f8fbff; color: #aaa; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 0.6px; }
.admin-table th:first-child { border-radius: 8px 0 0 8px; }
.admin-table th:last-child  { border-radius: 0 8px 8px 0; }
.admin-table td { padding: 13px 14px; border-bottom: 1px solid #f8f8f8; color: #444; vertical-align: middle; }
.admin-table tr:last-child td { border-bottom: none; }
.admin-table tr:hover td { background: #fafcff; }

.mini-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.mini-table th { text-align: left; padding: 8px 10px; color: #bbb; font-weight: 700; font-size: 10px; text-transform: uppercase; }
.mini-table td { padding: 10px 10px; border-bottom: 1px solid #f5f5f5; color: #444; }
.mini-table tr:last-child td { border-bottom: none; }
.empty-td { text-align: center; color: #ddd; padding: 24px !important; }

/* Badges */
.badge-pill { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.pill-green  { background: #e8f9f0; color: #27ae60; }
.pill-red    { background: #fef0f0; color: #e74c3c; }
.pill-orange { background: #fff8e1; color: #f39c12; }
.pill-blue   { background: #e8f4ff; color: #007bff; }

/* Buttons */
.btn-action {
    padding: 5px 12px; border-radius: 8px; border: 1.5px solid;
    font-family: 'Poppins', sans-serif; font-size: 11px; font-weight: 700;
    cursor: pointer; transition: 0.2s; white-space: nowrap;
}
.btn-approve { border-color: #c3f0d8; background: #e8f9f0; color: #27ae60; }
.btn-approve:hover { background: #27ae60; color: white; }
.btn-reject  { border-color: #fdd; background: #fef0f0; color: #e74c3c; }
.btn-reject:hover  { background: #e74c3c; color: white; }
.btn-delete  { border-color: #f5f5f5; background: #f8f8f8; color: #aaa; }
.btn-delete:hover  { background: #eee; color: #555; }
.btn-toggle  { border-color: #e9ecef; background: white; color: #666; }
.btn-toggle:hover  { border-color: #007bff; color: #007bff; }

/* Filters */
.filter-bar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
.filter-select {
    padding: 8px 14px; border: 1px solid #e9ecef; border-radius: 10px;
    font-family: 'Poppins', sans-serif; font-size: 12px; font-weight: 600;
    background: white; color: #555; outline: none; cursor: pointer;
}
.search-input {
    padding: 8px 14px; border: 1px solid #e9ecef; border-radius: 10px;
    font-family: 'Poppins', sans-serif; font-size: 13px;
    background: white; color: #333; outline: none;
    transition: 0.2s; min-width: 200px;
}
.search-input:focus { border-color: #007bff; box-shadow: 0 0 0 3px rgba(0,123,255,0.07); }

/* Toast */
.toast {
    position: fixed; bottom: 30px; right: 30px; z-index: 9999;
    background: #1a1f36; color: white; padding: 14px 20px;
    border-radius: 14px; font-size: 13px; font-weight: 600;
    box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    display: flex; align-items: center; gap: 10px;
    transform: translateY(80px); opacity: 0; transition: 0.35s cubic-bezier(.4,0,.2,1);
    pointer-events: none;
}
.toast.show { transform: translateY(0); opacity: 1; }
.toast.toast-ok  { border-left: 4px solid #27ae60; }
.toast.toast-err { border-left: 4px solid #e74c3c; }


.mobile-topbar {
    display: none;
    position: fixed; top: 0; left: 0; right: 0; z-index: 200;
    background: #1a1f36; padding: 14px 18px;
    align-items: center; justify-content: space-between;
}
.mobile-logo { font-size: 20px; font-weight: 800; color: white; text-decoration: none; }
.mobile-logo span { color: #4fc3f7; }
.hamburger-btn {
    background: rgba(255,255,255,0.1); border: none; border-radius: 8px;
    padding: 7px; cursor: pointer; color: white; display: flex; align-items: center;
    transition: 0.2s;
}
.hamburger-btn:hover { background: rgba(255,255,255,0.18); }

.sidebar-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.55); z-index: 150;
    backdrop-filter: blur(2px);
}
.sidebar-overlay.open { display: block; }

@media(max-width:900px){
    .mobile-topbar { display: flex; }
    .admin-content { margin-left:0; max-width:100vw; padding:20px 16px 60px; padding-top:72px; }
    .stat-grid { grid-template-columns: 1fr 1fr; }
    .dash-grid { grid-template-columns: 1fr; }
    .admin-table { font-size:12px; }
    .admin-table th, .admin-table td { padding: 10px 10px; }

    .admin-sidebar {
        position: fixed; left: -260px; top: 0; bottom: 0; z-index: 160;
        transition: left 0.3s cubic-bezier(.4,0,.2,1);
        width: 240px;
    }
    .admin-sidebar.open { left: 0; }
}

@media(max-width:600px){
    .stat-grid { grid-template-columns: 1fr 1fr; gap:10px; }
    .stat-card { padding: 16px 14px; }
    .stat-val { font-size:20px; }
    .table-card { padding: 16px; }
    .table-card-head { flex-direction: column; align-items: flex-start; gap: 6px; }
    .admin-topbar { flex-direction: column; align-items: flex-start; gap: 8px; }
    .filter-bar { gap: 6px; }
}
</style>
