<?php
require_once __DIR__ . '/api/config.php';
sessionStart();
$user = getCurrentUser();
$isLogged = isLoggedIn();

$db = getDB();
$totalUsers = (int)$db->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEDIXA - Prioritas Kesehatan Anda</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-14px); }
        }
        @keyframes float2 {
            0%, 100% { transform: translateY(0px) rotate(3deg); }
            50%       { transform: translateY(-10px) rotate(3deg); }
        }
        @keyframes pulse-ring {
            0%   { transform: scale(0.8); opacity: 0.6; }
            100% { transform: scale(1.6); opacity: 0; }
        }
        @keyframes slideInBadge {
            from { opacity: 0; transform: translateY(-12px) scale(0.9); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes shimmer {
            0%   { background-position: -400px 0; }
            100% { background-position: 400px 0; }
        }
        @keyframes countUp {
            from { opacity: 0; transform: scale(0.7); }
            to   { opacity: 1; transform: scale(1); }
        }


        .reveal { opacity: 0; transform: translateY(32px); transition: opacity 0.65s ease, transform 0.65s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-left { opacity: 0; transform: translateX(-30px); transition: opacity 0.6s ease, transform 0.6s ease; }
        .reveal-left.visible { opacity: 1; transform: translateX(0); }


        .hero { position: relative; overflow: hidden; }
        .hero-float-1 {
            position: absolute; top: 14%; right: 8%; width: 70px; height: 70px;
            background: white; border-radius: 20px; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 10px 30px rgba(0,123,255,0.12); animation: float 4s ease-in-out infinite;
            opacity: 0.9;
        }
        .hero-float-2 {
            position: absolute; bottom: 18%; right: 15%; width: 55px; height: 55px;
            background: white; border-radius: 16px; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 10px 30px rgba(32,226,215,0.15); animation: float2 5s ease-in-out infinite 0.5s;
            opacity: 0.85;
        }
        .hero-float-3 {
            position: absolute; top: 25%; left: 6%; width: 50px; height: 50px;
            background: white; border-radius: 14px; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 10px 30px rgba(255,71,87,0.1); animation: float 3.5s ease-in-out infinite 1s;
            opacity: 0.8;
        }
        .hero-bg-blob {
            position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: -1;
        }

        .badge-welcome { animation: slideInBadge 0.6s ease both; }
        .hero h1 { animation: fadeInUp 0.7s ease 0.1s both; }
        .hero p  { animation: fadeInUp 0.7s ease 0.2s both; }
        .hero-btns { animation: fadeInUp 0.7s ease 0.3s both; }


        .feature-card {
            transition: transform 0.35s cubic-bezier(.34,1.56,.64,1), box-shadow 0.35s ease !important;
        }
        .feature-card:hover {
            transform: rotate(1.5deg) translateY(-14px) scale(1.02) !important;
            box-shadow: 0 25px 50px rgba(0,0,0,0.09) !important;
        }


        .stat-block {
            background: white; border-radius: 24px; padding: 40px 50px;
            margin: 0 8% 60px; box-shadow: 0 10px 40px rgba(0,0,0,0.04);
            display: flex; align-items: center; justify-content: center; gap: 60px;
            flex-wrap: wrap; border: 1px solid #f0f4f8;
        }
        .stat-main { text-align: center; }
        .stat-main .num {
            font-size: 52px; font-weight: 800; color: #007bff; line-height: 1;
            margin-bottom: 6px; animation: countUp 0.6s ease both;
        }
        .stat-main .lbl { font-size: 15px; color: #888; font-weight: 500; }
        .stat-divider { width: 1px; height: 60px; background: #f0f0f0; }

        .pulse-wrap { position: relative; display: inline-flex; align-items: center; justify-content: center; }
        .pulse-ring {
            position: absolute; width: 60px; height: 60px; border-radius: 50%;
            background: rgba(0,123,255,0.15); animation: pulse-ring 2s ease-out infinite;
        }
        .pulse-icon {
            width: 52px; height: 52px; background: linear-gradient(135deg, #007bff, #00d2ff);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            position: relative; z-index: 1; box-shadow: 0 6px 20px rgba(0,123,255,0.3);
        }


        .obat-preview { padding: 70px 8%; background: #f8fbff; }
        .obat-preview-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 36px; flex-wrap: wrap; gap: 16px; }
        .obat-preview-head h2 { font-size: 28px; font-weight: 800; color: #1a1a1a; }
        .obat-preview-head p { color: #888; font-size: 14px; margin-top: 4px; }
        .obat-preview-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
        .obat-prev-card {
            background: white; border-radius: 18px; padding: 22px; border: 1px solid #f0f4f8;
            box-shadow: 0 4px 16px rgba(0,0,0,0.025); transition: 0.3s; cursor: pointer;
            text-decoration: none; color: inherit;
        }
        .obat-prev-card:hover { transform: translateY(-6px); box-shadow: 0 14px 32px rgba(0,0,0,0.07); border-color: #e0edff; }
        .obat-prev-icon { width: 46px; height: 46px; border-radius: 13px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
        .obat-prev-card h4 { font-size: 15px; font-weight: 700; margin-bottom: 4px; color: #1a1a1a; }
        .obat-prev-cat { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; display: inline-block; margin-bottom: 10px; }
        .obat-prev-card p { font-size: 12px; color: #777; line-height: 1.6; }
        .btn-see-all {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 26px; border-radius: 12px; background: linear-gradient(90deg, #20e2d7, #007bff);
            color: white; font-weight: 700; font-size: 14px; text-decoration: none; transition: 0.3s;
            box-shadow: 0 6px 20px rgba(0,123,255,0.2);
        }
        .btn-see-all:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(0,123,255,0.3); }

        @media(max-width:900px) {
            .obat-preview-cards { grid-template-columns: repeat(2,1fr); }
        }
        @media(max-width:600px) {
            .obat-preview-cards { grid-template-columns: 1fr; }
            .stat-block { padding: 30px; gap: 30px; }
            .stat-divider { display: none; }
            .hero-float-1, .hero-float-2, .hero-float-3 { display: none; }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/includes/navbar.php'; ?>


    <header class="hero">

        <div class="hero-bg-blob" style="width:500px;height:500px;background:rgba(0,123,255,0.06);top:-100px;right:-100px;"></div>
        <div class="hero-bg-blob" style="width:350px;height:350px;background:rgba(32,226,215,0.05);bottom:0;left:-80px;"></div>


        <div class="hero-float-1"><i data-lucide="heart-pulse" style="color:#007bff;width:30px;height:30px;"></i></div>
        <div class="hero-float-2"><i data-lucide="pill" style="color:#20e2d7;width:24px;height:24px;"></i></div>
        <div class="hero-float-3"><i data-lucide="shield-check" style="color:#ff4757;width:22px;height:22px;"></i></div>

        <div class="badge-welcome">
            <i data-lucide="sparkles"></i> Selamat Datang di MEDIXA
        </div>
        <h1>Kesehatan Anda,<br><span>Prioritas Kami</span></h1>
        <p>Platform kesehatan terpadu untuk membantu Anda mengelola kesehatan dengan lebih baik melalui fitur-fitur modern kami.</p>
        <div class="hero-btns">
            <?php if ($isLogged): ?>
                <a href="<?= BASE_PATH ?>/fitur/kalkulatorBMI.php" class="btn-main btn-blue">Mulai Sekarang</a>
                <a href="<?= BASE_PATH ?>/profile.php" class="btn-main btn-outline">Profil Saya</a>
            <?php else: ?>
                <a href="<?= BASE_PATH ?>/register.php" class="btn-main btn-blue">Mulai Sekarang</a>
                <a href="<?= BASE_PATH ?>/login.php" class="btn-main btn-outline">Masuk</a>
            <?php endif; ?>
        </div>
    </header>


    <section class="features-section reveal">
        <h2 class="section-title">Fitur Unggulan</h2>
        <div class="features-container-skew">
            <div class="features-grid">
                <a href="<?= BASE_PATH ?>/fitur/kalkulatorBMI.php" class="feature-card" style="text-decoration:none;color:inherit;">
                    <div class="icon-box" style="background:#007bff;"><i data-lucide="calculator"></i></div>
                    <h3>Kalkulator BMI</h3>
                    <p>Hitung Indeks Massa Tubuh Anda dengan mudah dan cepat melalui sistem kami.</p>
                </a>
                <a href="<?= BASE_PATH ?>/fitur/pintarObat.php" class="feature-card" style="text-decoration:none;color:inherit;">
                    <div class="icon-box" style="background:#20e2d7;"><i data-lucide="pill"></i></div>
                    <h3>Pintar Obat</h3>
                    <p>Cari informasi obat dan dosis yang tepat sesuai dengan kebutuhan medis Anda.</p>
                </a>
                <a href="<?= BASE_PATH ?>/fitur/rumahSakit.php" class="feature-card" style="text-decoration:none;color:inherit;">
                    <div class="icon-box" style="background:#ff4757;"><i data-lucide="map-pin"></i></div>
                    <h3>Rumah Sakit</h3>
                    <p>Temukan rumah sakit terdekat dari lokasi Anda saat ini secara real-time.</p>
                </a>
                <a href="<?= BASE_PATH ?>/fitur/donasi.php" class="feature-card" style="text-decoration:none;color:inherit;">
                    <div class="icon-box" style="background:#a55eea;"><i data-lucide="heart"></i></div>
                    <h3>Donasi</h3>
                    <p>Bantu pasien yang membutuhkan biaya pengobatan dengan cara berdonasi.</p>
                </a>
            </div>
        </div>
    </section>


    <div class="stat-block reveal">
        <div class="stat-main">
            <div class="num" id="statNum" data-target="<?= $totalUsers ?>">0</div>
            <div class="lbl">Pengguna Terdaftar di MEDIXA</div>
        </div>
        <div class="stat-divider"></div>
        <div class="pulse-wrap">
            <div class="pulse-ring"></div>
            <div class="pulse-icon">
                <i data-lucide="users" style="color:white;width:26px;height:26px;"></i>
            </div>
        </div>
        <div class="stat-divider"></div>
        <div style="max-width:220px;text-align:left;">
            <div style="font-size:14px;font-weight:700;color:#1a1a1a;margin-bottom:6px;">Bergabung sekarang!</div>
            <div style="font-size:13px;color:#888;line-height:1.6;">Jadilah bagian dari komunitas sehat MEDIXA dan nikmati semua fitur secara gratis.</div>
        </div>
    </div>


    <section class="obat-preview reveal">
        <div class="obat-preview-head">
            <div>
                <h2><i data-lucide="pill" style="display:inline;vertical-align:middle;color:#20e2d7;width:28px;height:28px;margin-right:8px;"></i>Pintar Obat</h2>
                <p>Kenali obat-obatan umum: kegunaan, dosis, dan efek sampingnya</p>
            </div>
            <a href="<?= BASE_PATH ?>/fitur/pintarObat.php" class="btn-see-all">
                <i data-lucide="arrow-right" style="width:16px;height:16px;"></i> Lihat Semua Obat
            </a>
        </div>
        <div class="obat-preview-cards">
            <?php
            $obatPreview = [
                ['nama'=>'Paracetamol','kategori'=>'Analgesik','warna'=>'#007bff','icon'=>'thermometer','kegunaan'=>'Pereda nyeri dan penurun demam yang paling umum digunakan.'],
                ['nama'=>'Amoxicillin','kategori'=>'Antibiotik','warna'=>'#e74c3c','icon'=>'shield','kegunaan'=>'Mengatasi infeksi bakteri pada saluran napas, kulit, dan telinga.'],
                ['nama'=>'Ibuprofen','kategori'=>'NSAID','warna'=>'#e67e22','icon'=>'zap','kegunaan'=>'Meredakan nyeri, peradangan, dan demam dengan cepat.'],
                ['nama'=>'Antasida','kategori'=>'Antasida','warna'=>'#27ae60','icon'=>'droplets','kegunaan'=>'Menetralkan asam lambung berlebih dan meredakan maag.'],
                ['nama'=>'Cetirizine','kategori'=>'Antihistamin','warna'=>'#8e44ad','icon'=>'wind','kegunaan'=>'Mengatasi alergi musiman, hidung tersumbat, dan gatal-gatal.'],
                ['nama'=>'Omeprazole','kategori'=>'PPI','warna'=>'#16a085','icon'=>'activity','kegunaan'=>'Mengobati tukak lambung dan penyakit refluks asam lambung.'],
                ['nama'=>'Metformin','kategori'=>'Antidiabetik','warna'=>'#2980b9','icon'=>'heart-pulse','kegunaan'=>'Mengontrol kadar gula darah pada penderita diabetes tipe 2.'],
                ['nama'=>'Vitamin C','kategori'=>'Suplemen','warna'=>'#f39c12','icon'=>'sun','kegunaan'=>'Meningkatkan daya tahan tubuh dan berperan sebagai antioksidan.'],
            ];
            foreach ($obatPreview as $o):
            ?>
            <a href="/fitur/pintarObat.php" class="obat-prev-card reveal" style="--delay:<?= rand(0,3)*0.1 ?>s;">
                <div class="obat-prev-icon" style="background:<?= $o['warna'] ?>18;">
                    <i data-lucide="<?= $o['icon'] ?>" style="color:<?= $o['warna'] ?>;width:22px;height:22px;"></i>
                </div>
                <h4><?= $o['nama'] ?></h4>
                <span class="obat-prev-cat" style="background:<?= $o['warna'] ?>15;color:<?= $o['warna'] ?>;"><?= $o['kategori'] ?></span>
                <p><?= $o['kegunaan'] ?></p>
            </a>
            <?php endforeach; ?>
        </div>
    </section>


    <div class="cta-banner reveal">
        <h2>Siap Memulai Perjalanan Kesehatan Anda?</h2>
        <p>Daftar sekarang dan dapatkan akses penuh ke semua fitur premium kami secara gratis!</p>
        <div class="cta-btns">
            <?php if ($isLogged): ?>
                <a href="<?= BASE_PATH ?>/fitur/kalkulatorBMI.php" class="btn-main btn-white">Cek BMI Sekarang</a>
                <a href="<?= BASE_PATH ?>/fitur/rumahSakit.php" class="btn-main btn-ghost">Cari Rumah Sakit</a>
            <?php else: ?>
                <a href="<?= BASE_PATH ?>/register.php" class="btn-main btn-white">Daftar Sekarang</a>
                <a href="<?= BASE_PATH ?>/login.php" class="btn-main btn-ghost">Sudah Punya Akun</a>
            <?php endif; ?>
        </div>
    </div>

<script>
lucide.createIcons();


const revealEls = document.querySelectorAll('.reveal, .reveal-left');
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
        if (entry.isIntersecting) {
            setTimeout(() => entry.target.classList.add('visible'), i * 60);
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.12 });
revealEls.forEach(el => observer.observe(el));


function animateCounter(el, target, duration = 1400) {
    let start = 0;
    const step = Math.ceil(target / (duration / 16));
    const timer = setInterval(() => {
        start += step;
        if (start >= target) { el.textContent = target.toLocaleString('id-ID'); clearInterval(timer); }
        else { el.textContent = start.toLocaleString('id-ID'); }
    }, 16);
}

const statEl = document.getElementById('statNum');
if (statEl) {
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(statEl, parseInt(statEl.dataset.target) || 0);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    counterObserver.observe(statEl);
}
</script>
</body>
</html>
