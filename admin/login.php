<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - MEDIXA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .login-wrap {
            width: 100%; max-width: 440px;
        }
        .login-logo {
            text-align: center; margin-bottom: 32px;
        }
        .login-logo .badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 50px; padding: 8px 20px;
            color: rgba(255,255,255,0.7); font-size: 12px; font-weight: 600;
            letter-spacing: 1px; text-transform: uppercase; margin-bottom: 16px;
        }
        .login-logo h1 {
            color: white; font-size: 32px; font-weight: 800;
            letter-spacing: -0.5px;
        }
        .login-logo h1 span { color: #4fc3f7; }
        .login-logo p { color: rgba(255,255,255,0.5); font-size: 14px; margin-top: 6px; }

        .login-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 24px;
            padding: 36px;
        }
        .card-header {
            text-align: center; margin-bottom: 28px;
        }
        .admin-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #4fc3f7, #0288d1);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
            box-shadow: 0 8px 24px rgba(79,195,247,0.3);
        }
        .card-header h2 { color: white; font-size: 20px; font-weight: 700; }
        .card-header p  { color: rgba(255,255,255,0.45); font-size: 13px; margin-top: 4px; }

        .form-field { margin-bottom: 18px; }
        .form-field label {
            display: block; font-size: 11px; font-weight: 700;
            color: rgba(255,255,255,0.5); margin-bottom: 8px;
            text-transform: uppercase; letter-spacing: 0.8px;
        }
        .input-wrap {
            position: relative; display: flex; align-items: center;
        }
        .input-wrap .iw-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: rgba(255,255,255,0.3); pointer-events: none;
            width: 16px; height: 16px; flex-shrink: 0;
        }
        .input-wrap input {
            width: 100%; padding: 13px 14px 13px 44px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            color: white; font-family: 'Poppins', sans-serif;
            font-size: 14px; outline: none; transition: 0.25s;
        }
        .input-wrap input::placeholder { color: rgba(255,255,255,0.25); }
        .input-wrap input:focus {
            border-color: #4fc3f7;
            background: rgba(79,195,247,0.08);
            box-shadow: 0 0 0 3px rgba(79,195,247,0.12);
        }
        .toggle-pw {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: rgba(255,255,255,0.3); padding: 0;
        }

        #alert-box {
            display: none; padding: 12px 16px; border-radius: 12px;
            font-size: 13px; font-weight: 600; margin-bottom: 16px;
            text-align: center;
        }
        .alert-err { background: rgba(239,83,80,0.15); color: #ef5350; border: 1px solid rgba(239,83,80,0.3); }

        .btn-login {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #4fc3f7, #0288d1);
            color: white; border: none; border-radius: 13px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px; font-weight: 700;
            cursor: pointer; transition: 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 6px 24px rgba(79,195,247,0.25);
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(79,195,247,0.35); }
        .btn-login:disabled { opacity: 0.6; transform: none; cursor: not-allowed; }

        .back-link {
            text-align: center; margin-top: 20px;
        }
        .back-link a {
            color: rgba(255,255,255,0.35); font-size: 13px;
            text-decoration: none; transition: 0.2s;
        }
        .back-link a:hover { color: rgba(255,255,255,0.6); }

        .hint {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px; padding: 12px 16px;
            margin-top: 20px;
        }
        .hint p { color: rgba(255,255,255,0.35); font-size: 11px; text-align: center; }
        .hint p strong { color: rgba(255,255,255,0.55); }

        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner {
            width: 16px; height: 16px;
            border: 2.5px solid rgba(255,255,255,0.3);
            border-top-color: white; border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-logo">
        <div class="badge">
            <i data-lucide="shield" style="width:12px;height:12px;"></i> Panel Administrator
        </div>
        <h1>MEDI<span>XA</span></h1>
        <p>Sistem Manajemen Kesehatan Indonesia</p>
    </div>

    <div class="login-card">
        <div class="card-header">
            <div class="admin-icon">
                <i data-lucide="lock-keyhole" style="color:white;width:28px;height:28px;"></i>
            </div>
            <h2>Masuk sebagai Admin</h2>
            <p>Masukkan kredensial admin Anda</p>
        </div>

        <div id="alert-box"></div>

        <form id="adminLoginForm">
            <div class="form-field">
                <label>Username Admin</label>
                <div class="input-wrap">
                    <svg class="iw-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <input type="text" name="username" placeholder="Contoh: admin1" autocomplete="username" required>
                </div>
            </div>
            <div class="form-field">
                <label>Password</label>
                <div class="input-wrap">
                    <svg class="iw-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input type="password" name="password" id="pwInput" placeholder="Masukkan password" autocomplete="current-password" required>
                    <button type="button" class="toggle-pw" onclick="togglePw()" id="eyeBtn">
                        <i data-lucide="eye" style="width:16px;height:16px;" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login" id="btnLogin">
                <i data-lucide="log-in" style="width:16px;height:16px;"></i> Masuk ke Dashboard
            </button>
        </form>

        <div class="hint">
            <p><strong>Akses Terbatas</strong> — Hanya untuk admin yang berwenang</p>
        </div>
    </div>

    <div class="back-link">
        <a href="/home.php">← Kembali ke Beranda</a>
    </div>
</div>

<script>
lucide.createIcons();

function togglePw() {
    const inp = document.getElementById('pwInput');
    const icon = document.getElementById('eyeIcon');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.setAttribute('data-lucide', 'eye-off');
    } else {
        inp.type = 'password';
        icon.setAttribute('data-lucide', 'eye');
    }
    lucide.createIcons();
}

document.getElementById('adminLoginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn  = document.getElementById('btnLogin');
    const alert = document.getElementById('alert-box');
    alert.style.display = 'none';

    btn.disabled = true;
    btn.innerHTML = '<div class="spinner"></div> Memverifikasi...';

    const fd = new FormData(this);
    const res = await fetch('../api/admin_auth.php', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.success) {
        btn.innerHTML = '<i data-lucide="check" style="width:16px;height:16px;"></i> Berhasil!';
        lucide.createIcons();
        setTimeout(() => { window.location.href = data.redirect; }, 500);
    } else {
        alert.textContent = data.message;
        alert.className = 'alert-err';
        alert.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="log-in" style="width:16px;height:16px;"></i> Masuk ke Dashboard';
        lucide.createIcons();
    }
});
</script>
</body>
</html>
