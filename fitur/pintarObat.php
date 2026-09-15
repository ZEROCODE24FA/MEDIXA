<?php
require_once __DIR__ . '/../api/config.php';
$basePath = '';
sessionStart();
$isLogged = isLoggedIn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintar Obat - MEDIXA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_fitur.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @keyframes fadeInUp { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeIn    { from { opacity:0; } to { opacity:1; } }

        .obat-page { max-width: 1200px; margin: 50px auto; padding: 0 20px 80px; }
        .obat-header { text-align: center; margin-bottom: 40px; animation: fadeInUp 0.6s ease both; }
        .obat-header h1 { font-size: 32px; font-weight: 800; color: #1a1a1a; margin-bottom: 8px; }
        .obat-header p { color: #6c757d; font-size: 16px; }

        .obat-toolbar { background: white; border-radius: 20px; padding: 24px 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid #f0f4f8; margin-bottom: 28px; display: flex; gap: 12px; flex-wrap: wrap; align-items: center; animation: fadeInUp 0.6s ease 0.1s both; }
        .search-wrap { flex: 1; min-width: 200px; position: relative; }
        .search-wrap input { width: 100%; padding: 12px 16px 12px 42px; border: 1px solid #e9ecef; border-radius: 12px; font-size: 14px; outline: none; font-family: 'Poppins', sans-serif; background: #f8fbff; transition: 0.3s; }
        .search-wrap input:focus { border-color: #20e2d7; background: white; box-shadow: 0 0 0 3px rgba(32,226,215,0.1); }
        .search-wrap i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #bbb; }
        .filter-btns { display: flex; gap: 8px; flex-wrap: wrap; }
        .filter-btn { padding: 9px 16px; border-radius: 10px; border: 1.5px solid #e9ecef; background: white; cursor: pointer; font-family: 'Poppins',sans-serif; font-size: 12px; font-weight: 700; color: #888; transition: 0.2s; }
        .filter-btn:hover, .filter-btn.active { border-color: #20e2d7; color: #20e2d7; background: #f0fffe; }
        .obat-count { font-size: 13px; color: #aaa; font-weight: 500; white-space: nowrap; }

        .obat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
        .obat-card { background: white; border-radius: 18px; padding: 22px; border: 1px solid #f0f4f8; box-shadow: 0 4px 16px rgba(0,0,0,0.025); transition: 0.3s; cursor: pointer; animation: fadeInUp 0.5s ease both; }
        .obat-card:hover { transform: translateY(-5px); box-shadow: 0 16px 36px rgba(0,0,0,0.07); border-color: #e0edff; }
        .obat-icon { width: 46px; height: 46px; border-radius: 13px; display: flex; align-items: center; justify-content: center; margin-bottom: 13px; }
        .obat-card h3 { font-size: 15px; font-weight: 700; color: #1a1a1a; margin-bottom: 5px; }
        .obat-kat { font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 20px; display: inline-block; margin-bottom: 9px; }
        .obat-card p { font-size: 12px; color: #777; line-height: 1.6; }
        .obat-card .gol { font-size: 10px; color: #bbb; margin-top: 10px; display: flex; align-items: center; gap: 5px; }

        .obat-detail-panel {
            background: white; border-radius: 22px; padding: 32px; box-shadow: 0 15px 45px rgba(0,0,0,0.06);
            border: 1px solid #f0f4f8; margin-bottom: 28px; display: none;
            animation: fadeIn 0.4s ease both;
        }
        .obat-detail-panel.show { display: block; }
        .det-head { display: flex; align-items: flex-start; gap: 18px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #f0f4f8; }
        .det-icon { width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .det-head h2 { font-size: 22px; font-weight: 800; color: #1a1a1a; margin-bottom: 6px; }
        .det-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px; }
        .det-item label { font-size: 10px; font-weight: 700; color: #bbb; text-transform: uppercase; letter-spacing: 0.6px; display: block; margin-bottom: 5px; }
        .det-item p { font-size: 13px; color: #444; line-height: 1.7; }
        .warn-box { background: #fff8e1; border-left: 4px solid #f39c12; border-radius: 10px; padding: 14px 18px; }
        .warn-box p { font-size: 13px; color: #7d6608; line-height: 1.7; }
        .btn-close { margin-left: auto; background: #f5f5f5; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: 0.2s; }
        .btn-close:hover { background: #fee; }

        .empty-msg { text-align: center; padding: 60px 20px; color: #ccc; grid-column: span 4; }

        @media(max-width:1000px){ .obat-grid { grid-template-columns: repeat(3,1fr); } }
        @media(max-width:768px){ .obat-grid { grid-template-columns: repeat(2,1fr); } .det-grid { grid-template-columns: 1fr; } }
        @media(max-width:500px){ .obat-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="obat-page">
    <div class="obat-header">
        <h1>Pintar Obat</h1>
        <p>Informasi lengkap obat-obatan umum — kegunaan, dosis, efek samping & peringatan</p>
    </div>

    <div class="obat-toolbar">
        <div class="search-wrap">
            <i data-lucide="search" style="width:16px;height:16px;"></i>
            <input type="text" id="searchInput" placeholder="Cari nama obat atau kategori..." oninput="filterObat()">
        </div>
        <div class="filter-btns" id="filterBtns">
            <button class="filter-btn active" data-cat="Semua" onclick="setFilter('Semua',this)">Semua</button>
            <button class="filter-btn" data-cat="Analgesik" onclick="setFilter('Analgesik',this)">Analgesik</button>
            <button class="filter-btn" data-cat="Antibiotik" onclick="setFilter('Antibiotik',this)">Antibiotik</button>
            <button class="filter-btn" data-cat="Antihistamin" onclick="setFilter('Antihistamin',this)">Alergi</button>
            <button class="filter-btn" data-cat="Antasida" onclick="setFilter('Antasida',this)">Lambung</button>
            <button class="filter-btn" data-cat="Suplemen" onclick="setFilter('Suplemen',this)">Suplemen</button>
        </div>
        <span class="obat-count" id="obatCount"></span>
    </div>

    <div id="obatDetail" class="obat-detail-panel"></div>
    <div class="obat-grid" id="obatGrid"></div>
</div>

<script>
lucide.createIcons();

const obatData = [

    { nama:'Paracetamol', kat:'Analgesik', warna:'#007bff', icon:'thermometer', gol:'Bebas',
      kegunaan:'Pereda nyeri dan penurun demam yang paling umum digunakan.',
      dosis:'Dewasa: 500–1000 mg setiap 4–6 jam (maks. 4 g/hari). Anak: 10–15 mg/kgBB.',
      efek:'Aman bila dosis tepat. Overdosis menyebabkan kerusakan hati.',
      peringatan:'Jangan melebihi dosis. Hindari alkohol. Hati-hati pada pasien gangguan hati.' },

    { nama:'Ibuprofen', kat:'Analgesik', warna:'#e67e22', icon:'zap', gol:'Bebas Terbatas',
      kegunaan:'Meredakan nyeri, demam, dan peradangan dengan cepat.',
      dosis:'Dewasa: 200–400 mg setiap 4–6 jam (maks. 1200 mg/hari). Konsumsi setelah makan.',
      efek:'Gangguan lambung, mual, pusing. Risiko perdarahan lambung.',
      peringatan:'Hindari jika ada masalah lambung/ginjal. Tidak dianjurkan ibu hamil trimester 3.' },

    { nama:'Asam Mefenamat', kat:'Analgesik', warna:'#c0392b', icon:'activity', gol:'Keras (Resep)',
      kegunaan:'Meredakan nyeri sedang, nyeri haid, nyeri gigi, dan sakit kepala.',
      dosis:'500 mg awal, dilanjutkan 250 mg tiap 6 jam. Konsumsi setelah makan.',
      efek:'Gangguan pencernaan, diare, pusing, kantuk.',
      peringatan:'Tidak boleh dikonsumsi lebih dari 7 hari tanpa supervisi dokter.' },

    { nama:'Aspirin', kat:'Analgesik', warna:'#e74c3c', icon:'droplet', gol:'Bebas Terbatas',
      kegunaan:'Pereda nyeri ringan, demam, dan pencegahan serangan jantung dosis rendah.',
      dosis:'Nyeri/demam: 500 mg tiap 4–6 jam. Kardiovaskular: 80–160 mg sekali sehari.',
      efek:'Iritasi lambung, perdarahan, tinnitus pada dosis tinggi.',
      peringatan:'Jangan diberikan ke anak di bawah 12 tahun (risiko sindrom Reye).' },

    { nama:'Tramadol', kat:'Analgesik', warna:'#8e44ad', icon:'shield-alert', gol:'Keras (Resep)',
      kegunaan:'Nyeri sedang hingga berat yang tidak responsif terhadap analgesik biasa.',
      dosis:'50–100 mg tiap 4–6 jam (maks. 400 mg/hari). Sesuai petunjuk dokter.',
      efek:'Mual, pusing, mengantuk, konstipasi. Potensi ketergantungan.',
      peringatan:'Hanya dengan resep dokter. Hindari alkohol dan depresan SSP lainnya.' },


    { nama:'Amoxicillin', kat:'Antibiotik', warna:'#e74c3c', icon:'shield', gol:'Keras (Resep)',
      kegunaan:'Infeksi bakteri pada saluran napas, telinga, kulit, dan saluran kemih.',
      dosis:'Dewasa: 250–500 mg tiap 8 jam. Anak: 25–50 mg/kgBB/hari dibagi 3 dosis.',
      efek:'Diare, mual, ruam kulit, reaksi alergi.',
      peringatan:'Selesaikan seluruh kurs antibiotik. Informasikan riwayat alergi penisilin.' },

    { nama:'Azithromycin', kat:'Antibiotik', warna:'#d35400', icon:'shield-plus', gol:'Keras (Resep)',
      kegunaan:'Infeksi saluran napas, kulit, dan penyakit menular seksual tertentu.',
      dosis:'500 mg hari pertama, lanjut 250 mg/hari selama 4 hari. Atau sesuai resep dokter.',
      efek:'Mual, diare, nyeri perut, perubahan rasa.',
      peringatan:'Interaksi dengan antikoagulan. Hati-hati pada pasien aritmia jantung.' },

    { nama:'Ciprofloxacin', kat:'Antibiotik', warna:'#16a085', icon:'shield-check', gol:'Keras (Resep)',
      kegunaan:'Infeksi bakteri gram-negatif, ISK, diare bakteri, infeksi tulang dan sendi.',
      dosis:'250–750 mg tiap 12 jam. Durasi sesuai jenis infeksi dan arahan dokter.',
      efek:'Mual, diare, pusing, fotosensitivitas, tendinitis.',
      peringatan:'Hindari antasida saat bersamaan. Tidak untuk anak di bawah 18 tahun.' },

    { nama:'Erythromycin', kat:'Antibiotik', warna:'#2980b9', icon:'shield-plus', gol:'Keras (Resep)',
      kegunaan:'Alternatif penisilin untuk infeksi bakteri, jerawat parah, dan infeksi kulit.',
      dosis:'250–500 mg tiap 6 jam. Konsumsi 30 menit sebelum makan.',
      efek:'Gangguan lambung, mual, nyeri perut, hepatotoksisitas.',
      peringatan:'Banyak interaksi obat. Konsultasikan dengan dokter sebelum menggunakan.' },


    { nama:'Cetirizine', kat:'Antihistamin', warna:'#8e44ad', icon:'wind', gol:'Bebas Terbatas',
      kegunaan:'Alergi musiman, rhinitis alergi, urtikaria (gatal-gatal biduran).',
      dosis:'Dewasa: 10 mg sekali sehari. Anak 6–12 thn: 5 mg dua kali sehari.',
      efek:'Mengantuk ringan, mulut kering, pusing.',
      peringatan:'Hati-hati mengemudi. Hindari alkohol. Aman untuk siang hari.' },

    { nama:'Loratadine', kat:'Antihistamin', warna:'#c0392b', icon:'feather', gol:'Bebas',
      kegunaan:'Alergi, rhinitis alergi, urtikaria tanpa efek mengantuk bermakna.',
      dosis:'Dewasa: 10 mg sekali sehari. Anak 2–12 thn: 5 mg/hari.',
      efek:'Sangat sedikit efek mengantuk, mulut kering ringan.',
      peringatan:'Konsultasi dokter jika hamil atau menyusui.' },

    { nama:'Diphenhydramine', kat:'Antihistamin', warna:'#7f8c8d', icon:'moon', gol:'Bebas',
      kegunaan:'Alergi, mabuk perjalanan, insomnia ringan, dan batuk kering malam hari.',
      dosis:'25–50 mg tiap 4–6 jam. Tidak lebih dari 300 mg/hari.',
      efek:'Mengantuk berat, mulut kering, retensi urine, penglihatan kabur.',
      peringatan:'Jangan mengemudi. Tidak untuk anak di bawah 2 tahun. Hindari kombinasi alkohol.' },


    { nama:'Antasida', kat:'Antasida', warna:'#27ae60', icon:'droplets', gol:'Bebas',
      kegunaan:'Menetralkan asam lambung berlebih, meredakan maag dan heartburn.',
      dosis:'1–2 tablet atau 10–20 ml suspensi 1 jam setelah makan dan sebelum tidur.',
      efek:'Sembelit (Al-based), diare (Mg-based), perubahan pola BAB.',
      peringatan:'Pisahkan 2 jam dengan obat lain karena dapat mengganggu absorpsi.' },

    { nama:'Omeprazole', kat:'Antasida', warna:'#16a085', icon:'activity', gol:'Keras (Resep)',
      kegunaan:'Tukak lambung, GERD, hipersekresi asam lambung, eradikasi H. pylori.',
      dosis:'20–40 mg sekali sehari sebelum makan pagi. Durasi 4–8 minggu.',
      efek:'Sakit kepala, diare, mual, sembelit.',
      peringatan:'Penggunaan jangka panjang dapat menurunkan kadar magnesium dan B12.' },

    { nama:'Lansoprazole', kat:'Antasida', warna:'#1abc9c', icon:'droplet', gol:'Keras (Resep)',
      kegunaan:'GERD, tukak lambung, tukak duodenum, sindrom Zollinger-Ellison.',
      dosis:'15–30 mg sekali sehari sebelum makan. Durasi sesuai kondisi.',
      efek:'Mual, diare, sakit perut, sakit kepala.',
      peringatan:'Sama seperti PPI lain, waspadai penggunaan jangka panjang.' },

    { nama:'Domperidone', kat:'Antasida', warna:'#e67e22', icon:'shield-check', gol:'Keras (Resep)',
      kegunaan:'Mual, muntah, rasa penuh setelah makan, refluks gastroesofageal.',
      dosis:'10 mg tiga kali sehari sebelum makan dan sebelum tidur bila perlu.',
      efek:'Mulut kering, sakit kepala, gangguan menstruasi (jarang).',
      peringatan:'Tidak untuk penggunaan jangka panjang. Risiko aritmia jantung pada dosis tinggi.' },


    { nama:'Metformin', kat:'Antidiabetik', warna:'#2980b9', icon:'heart-pulse', gol:'Keras (Resep)',
      kegunaan:'Lini pertama pengobatan diabetes mellitus tipe 2 dan prediabetes.',
      dosis:'Awal: 500 mg 2x/hari bersama makan. Dapat ditingkatkan hingga 2000 mg/hari.',
      efek:'Mual, diare, nyeri perut (umumnya sementara), defisiensi vitamin B12.',
      peringatan:'Monitor fungsi ginjal berkala. Hentikan sebelum prosedur dengan kontras iodine.' },

    { nama:'Glibenclamide', kat:'Antidiabetik', warna:'#1a6dad', icon:'droplet', gol:'Keras (Resep)',
      kegunaan:'Diabetes mellitus tipe 2 pada pasien yang tidak terkontrol dengan diet saja.',
      dosis:'2.5–5 mg sekali sehari bersama sarapan. Maks. 15 mg/hari.',
      efek:'Hipoglikemia, mual, gangguan hati (jarang).',
      peringatan:'Pantau kadar gula darah. Hati-hati pada pasien lansia (risiko hipoglikemia).' },


    { nama:'Vitamin C', kat:'Suplemen', warna:'#f39c12', icon:'sun', gol:'Bebas',
      kegunaan:'Meningkatkan imunitas, antioksidan, mendukung pembentukan kolagen.',
      dosis:'500–1000 mg per hari. AKG harian: 75–90 mg. Tidak lebih dari 2000 mg/hari.',
      efek:'Dosis tinggi: gangguan pencernaan, diare, batu ginjal.',
      peringatan:'Konsumsi bersama makanan untuk mengurangi iritasi lambung.' },

    { nama:'Vitamin D3', kat:'Suplemen', warna:'#f1c40f', icon:'sun-medium', gol:'Bebas',
      kegunaan:'Kesehatan tulang, imunitas, penyerapan kalsium dan fosfor.',
      dosis:'1000–2000 IU per hari. Defisiensi berat: sesuai anjuran dokter.',
      efek:'Toksisitas pada dosis sangat tinggi (hiperkalsemia).',
      peringatan:'Cek kadar vitamin D sebelum suplementasi dosis tinggi.' },

    { nama:'Zinc', kat:'Suplemen', warna:'#7f8c8d', icon:'layers', gol:'Bebas',
      kegunaan:'Mendukung imunitas, penyembuhan luka, pertumbuhan, dan fungsi reproduksi.',
      dosis:'Dewasa: 8–11 mg/hari. Suplemen: 15–30 mg/hari. Maks. 40 mg/hari.',
      efek:'Mual, gangguan pencernaan (bila perut kosong).',
      peringatan:'Konsumsi bersama makanan. Dosis berlebih mengganggu penyerapan tembaga.' },

    { nama:'Asam Folat', kat:'Suplemen', warna:'#e91e8c', icon:'heart', gol:'Bebas',
      kegunaan:'Pencegahan cacat tabung saraf janin, anemia megaloblastik, kehamilan.',
      dosis:'400–800 mcg per hari. Ibu hamil: minimal 400 mcg/hari (mulai pra-konsepsi).',
      efek:'Sangat jarang. Dosis sangat tinggi dapat menutupi kekurangan B12.',
      peringatan:'Ibu hamil wajib konsumsi. Konsultasi dokter untuk dosis lebih tinggi.' },


    { nama:'Ambroxol', kat:'Ekspektoran', warna:'#3498db', icon:'cloud-rain', gol:'Bebas',
      kegunaan:'Mengencerkan dan memudahkan pengeluaran dahak pada batuk berdahak.',
      dosis:'Dewasa: 30 mg tiga kali sehari. Anak: 1.2–1.6 mg/kgBB/hari dibagi beberapa dosis.',
      efek:'Mual, diare, mulut kering, sakit kepala.',
      peringatan:'Tidak dianjurkan untuk batuk kering. Minum banyak air untuk membantu.' },

    { nama:'Salbutamol', kat:'Bronkodilator', warna:'#27ae60', icon:'wind', gol:'Keras (Resep)',
      kegunaan:'Pelega sesak napas pada asma, bronkitis, dan PPOK (bronkodilator kerja cepat).',
      dosis:'Inhaler: 1–2 semprotan setiap 4–6 jam atau saat serangan. Oral: 2–4 mg tiga kali sehari.',
      efek:'Tremor tangan, palpitasi, sakit kepala, hipokalemia.',
      peringatan:'Jangan digunakan berlebihan. Segera ke IGD jika serangan asma berat.' },

    { nama:'Dexamethasone', kat:'Kortikosteroid', warna:'#c0392b', icon:'shield-alert', gol:'Keras (Resep)',
      kegunaan:'Peradangan berat, reaksi alergi parah, edema otak, COVID-19 berat.',
      dosis:'Sangat bervariasi (0.5–24 mg/hari) tergantung kondisi. Hanya dengan resep dokter.',
      efek:'Retensi cairan, kenaikan gula darah, penekanan imun, osteoporosis.',
      peringatan:'Jangan hentikan tiba-tiba setelah penggunaan lama. Harus bertahap (tapering).' },

    { nama:'Loperamide', kat:'Antidiare', warna:'#795548', icon:'droplets', gol:'Bebas Terbatas',
      kegunaan:'Mengurangi frekuensi dan konsistensi tinja pada diare akut.',
      dosis:'4 mg awal, lanjut 2 mg setiap BAB cair (maks. 16 mg/hari). Maks. 2 hari.',
      efek:'Sembelit, kembung, mual, mulut kering.',
      peringatan:'Tidak untuk diare berdarah atau infeksi bakteri parah. Tetap rehidrasi.' },

    { nama:'ORS / Oralit', kat:'Rehidrasi', warna:'#00bcd4', icon:'cup-soda', gol:'Bebas',
      kegunaan:'Mengganti cairan dan elektrolit yang hilang akibat diare, muntah, dan dehidrasi.',
      dosis:'Minum sedikit-sedikit tapi sering. Dewasa: 200–400 ml setiap BAB cair.',
      efek:'Aman. Sangat jarang efek samping bila digunakan sesuai petunjuk.',
      peringatan:'Jangan tambahkan gula/garam ekstra. Gunakan air matang yang sudah dingin.' },
];

let activeFilter = 'Semua';
let currentDetail = -1;

function setFilter(cat, el) {
    activeFilter = cat;
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    filterObat();
}

function filterObat() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const filtered = obatData.filter(o => {
        const matchCat = activeFilter === 'Semua' || o.kat === activeFilter;
        const matchQ   = o.nama.toLowerCase().includes(q) || o.kat.toLowerCase().includes(q) || o.kegunaan.toLowerCase().includes(q);
        return matchCat && matchQ;
    });
    renderGrid(filtered);
}

function renderGrid(data) {
    const grid = document.getElementById('obatGrid');
    document.getElementById('obatCount').textContent = data.length + ' obat';
    if (data.length === 0) {
        grid.innerHTML = '<div class="empty-msg"><p style="font-size:15px;">Tidak ditemukan 😕</p></div>';
        return;
    }
    grid.innerHTML = data.map(o => {
        const idx = obatData.indexOf(o);
        const golColor = o.gol.includes('Bebas (') || o.gol === 'Bebas' ? '#27ae60' : o.gol.includes('Terbatas') ? '#e67e22' : '#e74c3c';
        return `<div class="obat-card" onclick="showDetail(${idx})">
            <div class="obat-icon" style="background:${o.warna}18;">
                <i data-lucide="${o.icon}" style="color:${o.warna};width:22px;height:22px;"></i>
            </div>
            <h3>${o.nama}</h3>
            <span class="obat-kat" style="background:${o.warna}15;color:${o.warna};">${o.kat}</span>
            <p>${o.kegunaan}</p>
            <div class="gol"><span style="width:6px;height:6px;border-radius:50%;background:${golColor};display:inline-block;"></span>${o.gol}</div>
        </div>`;
    }).join('');
    lucide.createIcons();
}

const IS_LOGGED = <?= $isLogged ? 'true' : 'false' ?>;

function showDetail(idx) {
    const o = obatData[idx];
    const golColor = o.gol.includes('Bebas (') || o.gol === 'Bebas' ? '#27ae60' : o.gol.includes('Terbatas') ? '#e67e22' : '#e74c3c';
    const panel = document.getElementById('obatDetail');
    panel.innerHTML = `
        <div class="det-head">
            <div class="det-icon" style="background:${o.warna}18;">
                <i data-lucide="${o.icon}" style="color:${o.warna};width:28px;height:28px;"></i>
            </div>
            <div style="flex:1;">
                <h2>${o.nama}</h2>
                <span style="background:${o.warna}15;color:${o.warna};font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;display:inline-block;margin-right:6px;">${o.kat}</span>
                <span style="background:${golColor}15;color:${golColor};font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;display:inline-block;">${o.gol}</span>
            </div>
            <button class="btn-close" onclick="document.getElementById('obatDetail').classList.remove('show')">
                <i data-lucide="x" style="width:16px;height:16px;color:#888;"></i>
            </button>
        </div>
        <div class="det-grid">
            <div class="det-item"><label>Kegunaan</label><p>${o.kegunaan}</p></div>
            <div class="det-item"><label>Dosis</label><p>${o.dosis}</p></div>
            <div class="det-item"><label>Efek Samping</label><p>${o.efek}</p></div>
            <div class="det-item"><label>Golongan Obat</label><p>${o.gol}</p></div>
        </div>
        <div class="warn-box">
            <p><strong>⚠ Peringatan:</strong> ${o.peringatan} <em>Selalu konsultasikan dengan dokter atau apoteker sebelum menggunakan obat.</em></p>
        </div>`;
    panel.classList.add('show');
    panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    lucide.createIcons();

    // Simpan riwayat ke DB jika user sudah login
    if (IS_LOGGED) {
        const fd = new FormData();
        fd.append('nama_obat', o.nama);
        fd.append('kategori', o.kat);
        fetch('../api/obat_history_save.php', { method: 'POST', body: fd }).catch(() => {});
    }
}

filterObat();
</script>
</body>
</html>
