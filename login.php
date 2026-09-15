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
    <title>Login - MEDIXA</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="auth-page">
    <div class="login-container">
        <div class="logo-box">
            <i data-lucide="shield-plus" style="color: #1e90ff;"></i>
        </div>
        <h2>Selamat Datang</h2>
        <p class="subtitle">Masuk ke akun MEDIXA Anda</p>

        <div class="card">
            <div id="alert-box" style="display:none;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:16px;font-weight:500;"></div>
            <form id="loginForm">
                <div class="form-group">
                    <label>Email</label>
                    <div class="input-wrapper">
                        <i data-lucide="mail"></i>
                        <input type="email" name="email" placeholder="nama@email.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i data-lucide="lock"></i>
                        <input type="password" name="password" id="passInput" placeholder="Masukkan password" required>
                        <i data-lucide="eye" class="toggle-password" id="toggleEye" onclick="togglePass()" style="position:absolute;right:14px;left:auto;cursor:pointer;color:#aaa;"></i>
                    </div>
                </div>
                <div class="form-options">
                    <label style="display: flex; align-items: center; gap: 5px; margin: 0;">
                        <input type="checkbox"> Ingat saya
                    </label>
                    <a href="#">Lupa password?</a>
                </div>
                <button type="submit" class="btn-primary" id="btnLogin">Masuk</button>
            </form>
            <div class="divider">atau</div>
            <div class="footer-text">
                Belum punya akun? <a href="register.php">Daftar sekarang</a>
            </div>
        </div>
        <a href="home.php" class="back-home">← Kembali ke Beranda</a>
    </div>

    <script>
        lucide.createIcons();

        function togglePass() {
            const input = document.getElementById('passInput');
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        function showAlert(msg, success) {
            const box = document.getElementById('alert-box');
            box.textContent = msg;
            box.style.display = 'block';
            box.style.background = success ? '#e8f9f0' : '#fef0f0';
            box.style.color = success ? '#27ae60' : '#e74c3c';
            box.style.border = '1px solid ' + (success ? '#c3f0d8' : '#fdd');
        }

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnLogin');
            btn.textContent = 'Memproses...';
            btn.disabled = true;

            const formData = new FormData(this);
            try {
                const res = await fetch('api/auth_login.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    showAlert(data.message, true);
                    setTimeout(() => window.location.href = data.redirect || 'home.php', 800);
                } else {
                    showAlert(data.message, false);
                    btn.textContent = 'Masuk';
                    btn.disabled = false;
                }
            } catch(err) {
                showAlert('Terjadi kesalahan. Coba lagi.', false);
                btn.textContent = 'Masuk';
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>
