<?php
require_once __DIR__ . '/api/config.php';
sessionStart();
if (isLoggedIn()) {
    header('Location: /home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - MEDIXA</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="auth-page">
    <div class="login-container">
        <div class="logo-box">
            <i data-lucide="shield-plus" style="color: #1e90ff;"></i>
        </div>
        <h2>Daftar Akun</h2>
        <p class="subtitle">Bergabung dengan MEDIXA</p>

        <div class="card">
            <div id="alert-box" style="display:none;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:16px;font-weight:500;"></div>
            <form id="registerForm">
                <label style="font-size:13px;font-weight:600;color:#444;display:block;margin-bottom:8px;">Daftar Sebagai</label>
                <div class="role-selector">
                    <button type="button" class="btn-role active" data-role="pengguna" onclick="setRole('pengguna', this)">Pengguna</button>
                    <button type="button" class="btn-role" data-role="donatur" onclick="setRole('donatur', this)">Donatur</button>
                </div>
                <input type="hidden" name="role" id="roleInput" value="pengguna">

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <div class="input-wrapper">
                        <i data-lucide="user"></i>
                        <input type="text" name="nama" placeholder="Nama lengkap Anda" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <div class="input-wrapper">
                        <i data-lucide="mail"></i>
                        <input type="email" name="email" placeholder="nama@email.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <div class="input-wrapper">
                        <i data-lucide="phone"></i>
                        <input type="text" name="phone" placeholder="08xxxxxxxxxx">
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i data-lucide="lock"></i>
                        <input type="password" name="password" placeholder="Minimal 8 karakter" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <div class="input-wrapper">
                        <i data-lucide="lock"></i>
                        <input type="password" name="confirm_password" placeholder="Ulangi password" required>
                    </div>
                </div>
                <button type="submit" class="btn-primary" id="btnRegister">Daftar</button>
            </form>
            <div class="footer-text">
                Sudah punya akun? <a href="/login.php">Masuk di sini</a>
            </div>
        </div>
        <a href="/home.php" class="back-home">← Kembali ke Beranda</a>
    </div>

    <script>
        lucide.createIcons();

        function setRole(role, el) {
            document.getElementById('roleInput').value = role;
            document.querySelectorAll('.btn-role').forEach(b => b.classList.remove('active'));
            el.classList.add('active');
        }

        function showAlert(msg, success) {
            const box = document.getElementById('alert-box');
            box.textContent = msg;
            box.style.display = 'block';
            box.style.background = success ? '#e8f9f0' : '#fef0f0';
            box.style.color = success ? '#27ae60' : '#e74c3c';
            box.style.border = '1px solid ' + (success ? '#c3f0d8' : '#fdd');
        }

        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnRegister');
            btn.textContent = 'Mendaftarkan...';
            btn.disabled = true;

            const formData = new FormData(this);
            try {
                const res = await fetch('<?= BASE_PATH ?>/api/auth_register.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    showAlert(data.message, true);
                    setTimeout(() => window.location.href = data.redirect || '<?= BASE_PATH ?>/home.php', 1000);
                } else {
                    showAlert(data.message, false);
                    btn.textContent = 'Daftar';
                    btn.disabled = false;
                }
            } catch(err) {
                showAlert('Terjadi kesalahan. Coba lagi.', false);
                btn.textContent = 'Daftar';
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>
