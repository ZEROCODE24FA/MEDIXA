<?php
require_once __DIR__ . '/../api/config.php';
$basePath = '';
sessionStart();
requireLogin();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Penerima Donasi - MEDIXA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .reg-page { max-width: 720px; margin: 50px auto; padding: 0 20px 80px; }
        .reg-header { text-align: center; margin-bottom: 40px; }
        .reg-header h1 { font-size: 30px; font-weight: 800; color: #1a1a1a; margin-bottom: 8px; }
        .reg-header p { color: #6c757d; font-size: 15px; }
        .reg-card { background: white; border-radius: 24px; padding: 40px; border: 1px solid #f0f4f8; box-shadow: 0 10px 40px rgba(0,0,0,0.04); }
        .section-label { font-size: 13px; font-weight: 700; color: #007bff; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
        .section-label::after { content: ''; flex: 1; height: 1px; background: #e9ecef; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .form-field { margin-bottom: 20px; }
        .form-field label { display: block; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 8px; }
        .form-field input, .form-field textarea, .form-field select {
            width: 100%; padding: 12px 16px; border: 1px solid #e9ecef; border-radius: 12px;
            font-size: 14px; outline: none; font-family: 'Poppins', sans-serif;
            background: #f8fbff; transition: 0.3s; color: #333;
        }
        .form-field input:focus, .form-field textarea:focus, .form-field select:focus {
            border-color: #a55eea; background: white; box-shadow: 0 0 0 3px rgba(165,94,234,0.08);
        }
        .form-field textarea { resize: vertical; min-height: 90px; }
        .target-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-bottom: 10px; }
        .target-btn { padding: 10px; border: 1.5px solid #e9ecef; border-radius: 10px; background: white; cursor: pointer; font-family: 'Poppins',sans-serif; font-size: 12px; font-weight: 700; color: #888; transition: 0.2s; }
        .target-btn:hover, .target-btn.active { border-color: #a55eea; color: #a55eea; background: #f9f4ff; }
        .btn-submit { width: 100%; padding: 15px; background: linear-gradient(90deg, #a55eea, #8854d0); color: white; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; font-family: 'Poppins',sans-serif; font-size: 15px; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 8px; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(165,94,234,0.35); }
        .info-banner { background: linear-gradient(135deg, #f9f4ff, #f0e8ff); border: 1px solid #e2d0fb; border-radius: 14px; padding: 18px 22px; margin-bottom: 30px; display: flex; gap: 14px; align-items: flex-start; }
        .info-banner i { color: #a55eea; flex-shrink: 0; margin-top: 2px; }
        .info-banner p { font-size: 13px; color: #555; line-height: 1.7; }
        @media(max-width:600px){ .form-row { grid-template-columns:1fr; } .target-grid { grid-template-columns:repeat(2,1fr); } }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="reg-page">
    <div class="reg-header">
        <h1>Daftar Penerima Donasi</h1>
        <p>Ajukan permohonan bantuan dana untuk kebutuhan pengobatan Anda</p>
    </div>

    <div class="info-banner">
        <i data-lucide="info" style="width:20px;height:20px;"></i>
        <p>Permohonan Anda akan ditinjau oleh tim MEDIXA dalam <strong>1–3 hari kerja</strong>. Pastikan semua data yang diisi akurat dan dapat diverifikasi. Setelah disetujui, profil Anda akan tampil di halaman donasi.</p>
    </div>

    <div class="reg-card">
        <div id="form-alert" style="display:none;padding:14px 18px;border-radius:12px;font-size:13px;margin-bottom:20px;font-weight:500;"></div>

        <form id="regForm">
            <div class="section-label"><i data-lucide="user" style="width:14px;height:14px;"></i> Data Pribadi</div>

            <div class="form-row">
                <div class="form-field">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                </div>
                <div class="form-field">
                    <label>Nomor Telepon / WA</label>
                    <input type="text" name="telepon" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="08xxxxxxxxxx" required>
                </div>
            </div>

            <div class="form-field">
                <label>Alamat Lengkap</label>
                <textarea name="alamat" placeholder="Jalan, Kelurahan, Kecamatan, Kota, Provinsi" required></textarea>
            </div>

            <div class="section-label" style="margin-top:10px;"><i data-lucide="heart-pulse" style="width:14px;height:14px;"></i> Kondisi Kesehatan</div>

            <div class="form-row">
                <div class="form-field">
                    <label>Diagnosa / Penyakit</label>
                    <input type="text" name="penyakit" placeholder="Contoh: Kanker Stadium III" required>
                </div>
                <div class="form-field">
                    <label>Rumah Sakit yang Menangani</label>
                    <input type="text" name="rumah_sakit" placeholder="Nama rumah sakit" required>
                </div>
            </div>

            <div class="form-field">
                <label>Cerita Singkat Kondisi Anda</label>
                <textarea name="deskripsi" placeholder="Ceritakan kondisi kesehatan Anda, riwayat penyakit, dan kenapa membutuhkan bantuan..." style="min-height:120px;" required></textarea>
            </div>

            <div class="section-label" style="margin-top:10px;"><i data-lucide="wallet" style="width:14px;height:14px;"></i> Target Dana</div>

            <div class="form-field">
                <label>Target Dana yang Dibutuhkan</label>
                <div class="target-grid">
                    <button type="button" class="target-btn" onclick="setTarget(5000000, this)">Rp 5 Juta</button>
                    <button type="button" class="target-btn" onclick="setTarget(10000000, this)">Rp 10 Juta</button>
                    <button type="button" class="target-btn" onclick="setTarget(25000000, this)">Rp 25 Juta</button>
                    <button type="button" class="target-btn" onclick="setTarget(50000000, this)">Rp 50 Juta</button>
                    <button type="button" class="target-btn" onclick="setTarget(75000000, this)">Rp 75 Juta</button>
                    <button type="button" class="target-btn" onclick="setTarget(100000000, this)">Rp 100 Juta</button>
                </div>
                <input type="number" name="target" id="targetInput" placeholder="Atau masukkan jumlah lain (Rp)" min="1000000" required oninput="clearTargetBtn()">
            </div>

            <div class="section-label" style="margin-top:10px;"><i data-lucide="file-text" style="width:14px;height:14px;"></i> Dokumen Pendukung</div>

            <div class="form-row">
                <div class="form-field">
                    <label>Nomor KTP</label>
                    <input type="text" name="ktp" placeholder="16 digit nomor KTP" maxlength="16" required>
                </div>
                <div class="form-field">
                    <label>Nomor Rekening (opsional)</label>
                    <input type="text" name="rekening" placeholder="No. rekening bank">
                </div>
            </div>

            <div class="form-field">
                <label>Nama Bank</label>
                <select name="bank">
                    <option value="">-- Pilih Bank (opsional) --</option>
                    <option>BCA</option><option>BNI</option><option>BRI</option>
                    <option>Mandiri</option><option>BSI</option><option>CIMB Niaga</option>
                    <option>Permata</option><option>Danamon</option><option>Lainnya</option>
                </select>
            </div>

            <div class="form-field" style="display:flex;align-items:flex-start;gap:10px;">
                <input type="checkbox" name="setuju" id="setuju" required style="width:auto;margin-top:3px;">
                <label for="setuju" style="cursor:pointer;font-weight:400;color:#555;">Saya menyatakan bahwa semua informasi yang saya berikan adalah <strong>benar dan dapat dipertanggungjawabkan</strong>.</label>
            </div>

            <button type="submit" class="btn-submit" id="btnSubmit">
                <i data-lucide="send" style="width:18px;height:18px;"></i> Kirim Permohonan
            </button>
        </form>
    </div>
</div>

<script>
lucide.createIcons();

function setTarget(val, el) {
    document.getElementById('targetInput').value = val;
    document.querySelectorAll('.target-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
}
function clearTargetBtn() {
    document.querySelectorAll('.target-btn').forEach(b => b.classList.remove('active'));
}

document.getElementById('regForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmit');
    btn.innerHTML = '<div style="width:18px;height:18px;border:2px solid rgba(255,255,255,0.4);border-top-color:white;border-radius:50%;animation:spin .8s linear infinite;"></div> Mengirim...';
    btn.disabled = true;

    const res = await fetch('../api/register_penerima.php', { method: 'POST', body: new FormData(this) });
    const data = await res.json();
    const alertEl = document.getElementById('form-alert');
    alertEl.style.display = 'block';
    alertEl.textContent = data.message;
    alertEl.style.background = data.success ? '#e8f9f0' : '#fef0f0';
    alertEl.style.color = data.success ? '#27ae60' : '#e74c3c';
    alertEl.style.border = '1px solid ' + (data.success ? '#c3f0d8' : '#fdd');
    if (data.success) { this.reset(); document.querySelectorAll('.target-btn').forEach(b => b.classList.remove('active')); }
    btn.innerHTML = '<i data-lucide="send" style="width:18px;height:18px;"></i> Kirim Permohonan';
    btn.disabled = false;
    lucide.createIcons();
    alertEl.scrollIntoView({ behavior:'smooth', block:'start' });
});
</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>
</body>
</html>
