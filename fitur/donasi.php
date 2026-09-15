<?php
require_once __DIR__ . '/../api/config.php';
$basePath = '';
sessionStart();
requireLogin();

$db = getDB();
$penerima = $db->query("SELECT * FROM penerima_donasi WHERE status = 'approved' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donasi - MEDIXA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_fitur.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        @keyframes float    { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }

        .donasi-page { max-width: 1100px; margin: 0 auto; padding: 0 20px 80px; }


        .donasi-hero {
            background: linear-gradient(135deg, #a55eea 0%, #6c3fc0 60%, #4a2d9c 100%);
            border-radius: 28px;
            padding: 60px 50px;
            margin: 40px 0 50px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 40px;
            align-items: center;
            overflow: hidden;
            position: relative;
            animation: fadeInUp 0.6s ease both;
        }
        .donasi-hero::before {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
            top: -80px; right: -60px;
        }
        .donasi-hero::after {
            content: '';
            position: absolute;
            width: 180px; height: 180px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
            bottom: -50px; left: 40%;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 7px;
            background: rgba(255,255,255,0.15); padding: 6px 14px;
            border-radius: 20px; font-size: 12px; font-weight: 700;
            color: white; margin-bottom: 18px; backdrop-filter: blur(4px);
        }
        .donasi-hero h1 {
            font-size: 34px; font-weight: 800; color: white;
            line-height: 1.25; margin-bottom: 16px;
        }
        .donasi-hero h1 span { color: #ffd6f5; }
        .donasi-hero p {
            font-size: 15px; color: rgba(255,255,255,0.85);
            line-height: 1.75; max-width: 560px; margin-bottom: 28px;
        }
        .hero-stats { display: flex; gap: 28px; }
        .hero-stat { text-align: center; }
        .hero-stat .val { font-size: 22px; font-weight: 800; color: white; line-height: 1; }
        .hero-stat .lbl { font-size: 11px; color: rgba(255,255,255,0.7); margin-top: 4px; }
        .hero-stat-divider { width: 1px; background: rgba(255,255,255,0.2); }

        .hero-icon-wrap {
            position: relative; z-index: 1;
            width: 160px; height: 160px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .hero-icon-circle {
            width: 120px; height: 120px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            animation: float 3.5s ease-in-out infinite;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        .hero-icon-ring {
            position: absolute; inset: 0; border-radius: 50%;
            border: 2px dashed rgba(255,255,255,0.25);
            animation: spin 12s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }


        .cara-kerja {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;
            margin-bottom: 50px; animation: fadeInUp 0.6s ease 0.1s both;
        }
        .step-card {
            background: white; border-radius: 20px; padding: 28px;
            border: 1px solid #f0f4f8; box-shadow: 0 4px 16px rgba(0,0,0,0.025);
            text-align: center; position: relative;
        }
        .step-num {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, #a55eea, #8854d0);
            color: white; font-weight: 800; font-size: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
        }
        .step-icon { margin-bottom: 12px; }
        .step-card h4 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px; }
        .step-card p  { font-size: 13px; color: #777; line-height: 1.65; }
        .step-arrow {
            position: absolute; right: -12px; top: 50%;
            transform: translateY(-50%);
            width: 24px; height: 24px; background: #f0f4f8;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            z-index: 1;
        }


        .section-head {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
        }
        .section-head h2 { font-size: 22px; font-weight: 800; color: #1a1a1a; }
        .section-head p  { font-size: 13px; color: #888; margin-top: 3px; }
        .count-badge {
            background: #f9f4ff; color: #a55eea;
            font-size: 12px; font-weight: 700;
            padding: 5px 14px; border-radius: 20px;
        }


        .penerima-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; margin-bottom: 50px; }
        .penerima-card {
            background: white; border-radius: 20px; padding: 26px;
            border: 1px solid #f0f4f8; box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            transition: 0.3s; display: flex; flex-direction: column;
        }
        .penerima-card:hover { transform: translateY(-5px); box-shadow: 0 15px 36px rgba(0,0,0,0.07); border-color: #e8d9fb; }
        .avatar-penerima {
            width: 52px; height: 52px; border-radius: 14px;
            background: linear-gradient(135deg, #a55eea, #8854d0);
            color: white; display: flex; align-items: center;
            justify-content: center; font-size: 20px; font-weight: 800; margin-bottom: 14px;
        }
        .penerima-card h3    { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .sakit-badge         { background: #fef0f0; color: #e74c3c; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; display: inline-block; margin-bottom: 10px; }
        .penerima-card p     { font-size: 13px; color: #666; line-height: 1.6; margin-bottom: 16px; flex: 1; }
        .progress-bar        { background: #f0f4f8; border-radius: 50px; height: 8px; overflow: hidden; }
        .progress-fill       { background: linear-gradient(90deg, #a55eea, #8854d0); border-radius: 50px; height: 100%; }
        .progress-text       { display: flex; justify-content: space-between; font-size: 12px; color: #888; margin-top: 6px; }
        .progress-text strong { color: #a55eea; }
        .persen-label        { font-size: 11px; color: #a55eea; font-weight: 700; margin-top: 4px; }
        .btn-donasi {
            width: 100%; padding: 12px;
            background: linear-gradient(90deg, #a55eea, #8854d0);
            color: white; border: none; border-radius: 10px;
            font-weight: 700; cursor: pointer; font-family: 'Poppins', sans-serif;
            transition: 0.3s; margin-top: 14px; display: flex;
            align-items: center; justify-content: center; gap: 7px;
        }
        .btn-donasi:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(165,94,234,0.3); }


        .empty-donasi {
            background: white; border-radius: 24px; padding: 60px 40px;
            border: 2px dashed #e8d9fb; text-align: center; margin-bottom: 50px;
            animation: fadeInUp 0.5s ease both;
        }
        .empty-icon { width: 80px; height: 80px; background: #f9f4ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .empty-donasi h3 { font-size: 20px; font-weight: 800; color: #1a1a1a; margin-bottom: 8px; }
        .empty-donasi p  { font-size: 14px; color: #888; line-height: 1.7; max-width: 420px; margin: 0 auto 24px; }
        .btn-daftar-penerima {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 24px; background: linear-gradient(90deg, #a55eea, #8854d0);
            color: white; text-decoration: none; border-radius: 12px;
            font-weight: 700; font-size: 14px; transition: 0.3s;
        }
        .btn-daftar-penerima:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(165,94,234,0.3); }


        .donasi-form-section {
            background: white; border-radius: 24px; padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid #f0f4f8;
            max-width: 620px; margin: 0 auto;
            animation: fadeInUp 0.6s ease 0.2s both;
        }
        .form-section-title { display: flex; align-items: center; gap: 12px; margin-bottom: 6px; }
        .form-section-title .fst-icon { width: 40px; height: 40px; background: #f9f4ff; border-radius: 11px; display: flex; align-items: center; justify-content: center; }
        .form-section-title h2 { font-size: 20px; font-weight: 800; color: #1a1a1a; }
        .form-sub { font-size: 13px; color: #888; margin-bottom: 28px; padding-left: 52px; }
        .form-field { margin-bottom: 18px; }
        .form-field label { display: block; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 8px; }
        .input-wrap { position: relative; }
        .input-wrap i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #bbb; }
        .input-wrap input {
            width: 100%; padding: 12px 15px 12px 42px;
            border: 1px solid #e9ecef; border-radius: 12px;
            font-size: 14px; outline: none; background: #f8fbff;
            font-family: 'Poppins', sans-serif; transition: 0.3s; color: #333;
        }
        .input-wrap input:focus { border-color: #a55eea; background: white; box-shadow: 0 0 0 3px rgba(165,94,234,0.08); }
        .amount-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 12px; }
        .amount-btn {
            padding: 11px; border: 1.5px solid #e9ecef; border-radius: 10px;
            background: white; cursor: pointer; font-weight: 700;
            font-family: 'Poppins', sans-serif; font-size: 12px; transition: 0.2s; color: #555;
        }
        .amount-btn:hover, .amount-btn.active { border-color: #a55eea; color: #a55eea; background: #f9f4ff; }
        .input-plain {
            width: 100%; padding: 12px 14px; border: 1px solid #e9ecef;
            border-radius: 12px; outline: none; font-family: 'Poppins', sans-serif;
            background: #f8fbff; font-size: 14px; transition: 0.3s; color: #333;
        }
        .input-plain:focus { border-color: #a55eea; background: white; box-shadow: 0 0 0 3px rgba(165,94,234,0.08); }
        .btn-submit-donasi {
            width: 100%; padding: 14px;
            background: linear-gradient(90deg, #a55eea, #8854d0);
            color: white; border: none; border-radius: 12px;
            font-weight: 700; cursor: pointer; font-family: 'Poppins', sans-serif;
            margin-top: 8px; font-size: 15px; transition: 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-submit-donasi:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(165,94,234,0.35); }
        .btn-submit-donasi:disabled { opacity: 0.65; cursor: wait; transform: none; box-shadow: none; }

        .trust-badges {
            display: flex; align-items: center; justify-content: center;
            gap: 20px; margin-top: 18px; flex-wrap: wrap;
        }
        .trust-item { display: flex; align-items: center; gap: 6px; font-size: 11px; color: #aaa; font-weight: 500; }

        @media(max-width:900px) {
            .cara-kerja { grid-template-columns: 1fr; gap: 14px; }
            .step-arrow { display:none; }
            .penerima-grid { grid-template-columns: repeat(2,1fr); }
        }
        @media(max-width:768px) {
            .donasi-page { padding: 0 14px 60px; }
            .donasi-hero { grid-template-columns: 1fr; padding: 32px 22px; gap: 0; border-radius: 20px; margin: 20px 0 36px; }
            .donasi-hero h1 { font-size: 26px; }
            .donasi-hero p { font-size: 13px; }
            .hero-icon-wrap { display: none; }
            .hero-stats { gap: 18px; }
            .hero-stat .val { font-size: 18px; }
            .penerima-grid { grid-template-columns: 1fr; }
            .amount-grid { grid-template-columns: repeat(2,1fr); }
            .donasi-form-section { padding: 26px 20px; }
        }
        @media(max-width:480px) {
            .donasi-hero { padding: 26px 18px; }
            .donasi-hero h1 { font-size: 22px; }
            .cara-kerja { grid-template-columns: 1fr; }
            .amount-grid { grid-template-columns: repeat(2,1fr); }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="donasi-page">


    <div class="donasi-hero">
        <div>
            <div class="hero-badge">
                <i data-lucide="heart" style="width:13px;height:13px;"></i>
                Program Donasi MEDIXA
            </div>
            <h1>Kesehatan adalah<br><span>Gerakan Bersama</span></h1>
            <p>MEDIXA mengintegrasikan sistem donasi yang memungkinkan pengguna berkontribusi bagi pasien kurang mampu, menjadikan akses kesehatan sebagai gerakan bersama — bukan hanya kebutuhan individu.</p>
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="val"><?= count($penerima) ?></div>
                    <div class="lbl">Penerima Aktif</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="val">100%</div>
                    <div class="lbl">Transparan</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="val">Rp 5.000</div>
                    <div class="lbl">Biaya Admin</div>
                </div>
            </div>
        </div>
        <div class="hero-icon-wrap">
            <div class="hero-icon-ring"></div>
            <div class="hero-icon-circle">
                <i data-lucide="heart-handshake" style="color:white;width:56px;height:56px;"></i>
            </div>
        </div>
    </div>


    <div class="cara-kerja">
        <div class="step-card">
            <div class="step-num">1</div>
            <div class="step-icon">
                <div style="width:48px;height:48px;background:#f9f4ff;border-radius:13px;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                    <i data-lucide="file-text" style="color:#a55eea;width:22px;height:22px;"></i>
                </div>
            </div>
            <h4>Ajukan Permohonan</h4>
            <p>Pasien mendaftar sebagai penerima donasi dengan melengkapi data medis dan kondisi keuangan yang dapat diverifikasi.</p>
            <div class="step-arrow"><i data-lucide="chevron-right" style="width:13px;height:13px;color:#ccc;"></i></div>
        </div>
        <div class="step-card">
            <div class="step-num">2</div>
            <div class="step-icon">
                <div style="width:48px;height:48px;background:#f0faf5;border-radius:13px;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                    <i data-lucide="shield-check" style="color:#27ae60;width:22px;height:22px;"></i>
                </div>
            </div>
            <h4>Verifikasi Tim MEDIXA</h4>
            <p>Tim kami meninjau kelayakan permohonan dalam 1–3 hari kerja untuk memastikan bantuan tepat sasaran.</p>
            <div class="step-arrow"><i data-lucide="chevron-right" style="width:13px;height:13px;color:#ccc;"></i></div>
        </div>
        <div class="step-card">
            <div class="step-num">3</div>
            <div class="step-icon">
                <div style="width:48px;height:48px;background:#fff8e1;border-radius:13px;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                    <i data-lucide="hand-heart" style="color:#f39c12;width:22px;height:22px;"></i>
                </div>
            </div>
            <h4>Donasi Tersalurkan</h4>
            <p>Setelah disetujui, profil penerima tampil di halaman ini dan donasi dari komunitas MEDIXA langsung tersalurkan.</p>
        </div>
    </div>


    <div class="section-head">
        <div>
            <h2>Penerima Donasi</h2>
            <p>Pasien yang telah diverifikasi dan membutuhkan bantuan Anda</p>
        </div>
        <?php if (!empty($penerima)): ?>
        <span class="count-badge"><?= count($penerima) ?> penerima aktif</span>
        <?php endif; ?>
    </div>

    <?php if (empty($penerima)): ?>

    <div class="empty-donasi">
        <div class="empty-icon">
            <i data-lucide="inbox" style="color:#a55eea;width:36px;height:36px;"></i>
        </div>
        <h3>Belum Ada Penerima Donasi</h3>
        <p>Saat ini belum ada permohonan donasi yang disetujui. Jika Anda atau orang terdekat membutuhkan bantuan biaya pengobatan, silakan daftar sebagai penerima donasi.</p>
        <a href="registerPenerimaDonasi.php" class="btn-daftar-penerima">
            <i data-lucide="plus-circle" style="width:17px;height:17px;"></i>
            Daftar Sebagai Penerima
        </a>
    </div>

    <?php else: ?>

    <div class="penerima-grid">
        <?php foreach ($penerima as $p):
            $persen = min(100, ($p['terkumpul'] / $p['target']) * 100);
            $inisial = strtoupper(substr($p['nama'], 0, 1));
        ?>
        <div class="penerima-card">
            <div class="avatar-penerima"><?= $inisial ?></div>
            <h3><?= htmlspecialchars($p['nama']) ?></h3>
            <span class="sakit-badge"><?= htmlspecialchars($p['penyakit']) ?></span>
            <p><?= htmlspecialchars($p['deskripsi']) ?></p>
            <div>
                <div class="progress-bar"><div class="progress-fill" style="width:<?= $persen ?>%"></div></div>
                <div class="progress-text">
                    <strong>Rp <?= number_format($p['terkumpul'], 0, ',', '.') ?></strong>
                    <span>dari Rp <?= number_format($p['target'], 0, ',', '.') ?></span>
                </div>
                <div class="persen-label"><?= round($persen) ?>% terkumpul</div>
            </div>
            <button class="btn-donasi" onclick="scrollToDonasi('<?= htmlspecialchars($p['nama']) ?>')">
                <i data-lucide="heart" style="width:15px;height:15px;"></i> Donasikan Sekarang
            </button>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>


    <div class="donasi-form-section" id="donasiForm">
        <div class="form-section-title">
            <div class="fst-icon"><i data-lucide="send-horizontal" style="color:#a55eea;width:20px;height:20px;"></i></div>
            <h2>Kirim Donasi</h2>
        </div>
        <p class="form-sub">Setiap kontribusi Anda, sebesar apapun, sangat berarti bagi mereka yang berjuang.</p>

        <div id="donasi-alert" style="display:none;padding:13px 18px;border-radius:12px;font-size:13px;margin-bottom:18px;font-weight:500;"></div>

        <form id="formDonasi">
            <div class="form-field">
                <label>Untuk (Nama Penerima)</label>
                <div class="input-wrap">
                    <i data-lucide="user" style="width:16px;height:16px;"></i>
                    <input type="text" name="untuk" id="untukInput" placeholder="Nama penerima donasi (opsional)">
                </div>
            </div>
            <div class="form-field">
                <label>Nama Donatur</label>
                <div class="input-wrap">
                    <i data-lucide="user-check" style="width:16px;height:16px;"></i>
                    <input type="text" name="nama" placeholder="Nama lengkap Anda atau 'Hamba Allah'" required>
                </div>
            </div>
            <div class="form-field">
                <label>Email</label>
                <div class="input-wrap">
                    <i data-lucide="mail" style="width:16px;height:16px;"></i>
                    <input type="email" name="email" placeholder="nama@email.com" required>
                </div>
            </div>
            <div class="form-field">
                <label>Jumlah Donasi</label>
                <div class="amount-grid">
                    <button type="button" class="amount-btn" onclick="setAmount(10000,this)">Rp 10.000</button>
                    <button type="button" class="amount-btn" onclick="setAmount(25000,this)">Rp 25.000</button>
                    <button type="button" class="amount-btn" onclick="setAmount(50000,this)">Rp 50.000</button>
                    <button type="button" class="amount-btn" onclick="setAmount(100000,this)">Rp 100.000</button>
                    <button type="button" class="amount-btn" onclick="setAmount(250000,this)">Rp 250.000</button>
                    <button type="button" class="amount-btn" onclick="setAmount(500000,this)">Rp 500.000</button>
                </div>
                <input type="number" name="jumlah" id="jumlahInput" class="input-plain" placeholder="Atau masukkan jumlah lain (min. Rp 10.000)" oninput="clearAmountBtn()">
            </div>
            <div class="form-field">
                <label>Pesan Dukungan <span style="font-weight:400;color:#bbb;">(Opsional)</span></label>
                <textarea name="pesan" class="input-plain" placeholder="Tulis pesan semangat untuk penerima..." style="resize:vertical;min-height:80px;"></textarea>
            </div>
            <button type="submit" class="btn-submit-donasi" id="btnKirim">
                <i data-lucide="heart" style="width:17px;height:17px;"></i> Kirim Donasi Sekarang
            </button>
        </form>

        <div class="trust-badges">
            <div class="trust-item"><i data-lucide="shield-check" style="width:14px;height:14px;color:#27ae60;"></i> Terverifikasi</div>
            <div class="trust-item"><i data-lucide="lock" style="width:14px;height:14px;color:#007bff;"></i> Data Aman</div>
            <div class="trust-item"><i data-lucide="receipt" style="width:14px;height:14px;color:#a55eea;"></i> Biaya Admin Rp 5.000</div>
        </div>
    </div>

</div>

<script>
lucide.createIcons();

function scrollToDonasi(nama) {
    document.getElementById('untukInput').value = nama;
    document.getElementById('donasiForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function setAmount(val, el) {
    document.getElementById('jumlahInput').value = val;
    document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
}

function clearAmountBtn() {
    document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('active'));
}

document.getElementById('formDonasi').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnKirim');
    btn.innerHTML = '<div style="width:17px;height:17px;border:2px solid rgba(255,255,255,0.4);border-top-color:white;border-radius:50%;animation:spin .8s linear infinite;"></div> Memproses...';
    btn.disabled = true;

    const res  = await fetch('../api/donasi_kirim.php', { method: 'POST', body: new FormData(this) });
    const data = await res.json();

    const alertEl = document.getElementById('donasi-alert');
    alertEl.style.display = 'block';
    alertEl.textContent   = data.message;
    alertEl.style.background = data.success ? '#e8f9f0' : '#fef0f0';
    alertEl.style.color      = data.success ? '#27ae60' : '#e74c3c';
    alertEl.style.border     = '1px solid ' + (data.success ? '#c3f0d8' : '#fdd');

    if (data.success) {
        this.reset();
        document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('active'));
    }

    btn.innerHTML = '<i data-lucide="heart" style="width:17px;height:17px;"></i> Kirim Donasi Sekarang';
    btn.disabled  = false;
    lucide.createIcons();
    alertEl.scrollIntoView({ behavior:'smooth', block:'nearest' });
});
</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>
</body>
</html>
