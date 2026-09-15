<?php
require_once __DIR__ . '/../api/config.php';
$basePath = '';
sessionStart();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rumah Sakit Terdekat - MEDIXA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_fitur.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>

        .rs-page { max-width: 1200px; margin: 50px auto; padding: 0 20px 80px; }
        .rs-header { text-align: center; margin-bottom: 40px; }
        .rs-header h1 { font-size: 32px; font-weight: 800; color: #1a1a1a; margin-bottom: 8px; }
        .rs-header p  { color: #6c757d; font-size: 16px; }
        .rs-layout  { display: grid; grid-template-columns: 380px 1fr; gap: 24px; align-items: start; }


        .rs-panel { background: white; border-radius: 20px; padding: 24px; border: 1px solid #f0f4f8; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        .rs-panel-title { display: flex; align-items: center; gap: 10px; font-size: 16px; font-weight: 700; color: #1a1a1a; margin-bottom: 18px; }
        .rs-panel-title .icon-circle { background: #ff4757; color: white; width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

        .search-rs { position: relative; margin-bottom: 16px; }
        .search-rs input { width: 100%; padding: 11px 16px 11px 40px; border: 1px solid #e9ecef; border-radius: 12px; font-size: 13px; outline: none; background: #f8fbff; font-family: 'Poppins', sans-serif; transition: 0.3s; }
        .search-rs input:focus { border-color: #ff4757; background: white; }
        .search-rs i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #aaa; }

        .btn-lokasi { width: 100%; padding: 13px; background: linear-gradient(90deg, #ff4757, #ff6b81); color: white; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; font-family: 'Poppins', sans-serif; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.3s; margin-bottom: 18px; font-size: 14px; }
        .btn-lokasi:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,71,87,0.35); }
        .btn-lokasi:disabled { opacity: 0.6; cursor: wait; transform: none; box-shadow: none; }

        .rs-list { max-height: 420px; overflow-y: auto; padding-right: 4px; }
        .rs-list::-webkit-scrollbar { width: 4px; }
        .rs-list::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .rs-list::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }

        .rs-item { padding: 14px; border-radius: 14px; cursor: pointer; transition: 0.2s; border: 1.5px solid transparent; margin-bottom: 8px; }
        .rs-item:hover { background: #fff5f5; border-color: #ffd6da; }
        .rs-item.active { background: #fff0f1; border-color: #ff4757; }
        .rs-item-header { display: flex; align-items: flex-start; gap: 10px; }
        .rs-num { min-width: 26px; height: 26px; border-radius: 8px; background: #ff4757; color: white; font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center; margin-top: 1px; }
        .rs-item h4 { font-size: 14px; font-weight: 700; color: #1a1a1a; margin-bottom: 3px; line-height: 1.3; }
        .rs-item .rs-jarak { font-size: 12px; color: #ff4757; font-weight: 600; display: flex; align-items: center; gap: 4px; }
        .rs-item .rs-alamat { font-size: 12px; color: #888; margin-top: 4px; margin-left: 36px; line-height: 1.5; }
        .rs-item .rs-tags  { margin-top: 8px; margin-left: 36px; display: flex; flex-wrap: wrap; gap: 5px; }
        .rs-tag { font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 20px; }


        .map-container { background: white; border-radius: 20px; overflow: hidden; border: 1px solid #f0f4f8; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        #map { width: 100%; height: 560px; }
        .map-overlay { padding: 16px 20px; border-top: 1px solid #f0f4f8; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .map-stat { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #666; }
        .map-stat strong { color: #1a1a1a; }
        .map-legend { display: flex; gap: 16px; }
        .legend-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #888; }
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; }


        .state-box { text-align: center; padding: 40px 20px; color: #bbb; }
        .state-box p { font-size: 14px; line-height: 1.6; }
        .state-box a { color: #ff4757; text-decoration: none; font-weight: 600; }
        .spinner { width: 36px; height: 36px; border: 3px solid #f0f4f8; border-top-color: #ff4757; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 14px; }


        #permissionCard {
            background: linear-gradient(135deg, #fff5f5, #fff9f9);
            border: 1.5px solid #ffd6da;
            border-radius: 18px;
            padding: 24px;
            margin-bottom: 18px;
            text-align: center;
            animation: fadeInCard 0.4s ease;
        }
        @keyframes fadeInCard { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
        .perm-icon-wrap {
            width: 64px; height: 64px; background: #fff0f1;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px; position: relative;
        }
        .perm-icon-wrap::before {
            content: '';
            position: absolute; inset: -5px;
            border-radius: 50%; border: 2px dashed #ffb3bb;
            animation: rotateDash 8s linear infinite;
        }
        @keyframes rotateDash { to { transform: rotate(360deg); } }
        #permissionCard h3 { font-size: 16px; font-weight: 800; color: #1a1a1a; margin-bottom: 8px; }
        #permissionCard p  { font-size: 13px; color: #777; line-height: 1.6; margin-bottom: 18px; }
        .perm-btns { display: flex; gap: 10px; justify-content: center; }
        .btn-izinkan {
            padding: 11px 22px; background: linear-gradient(90deg, #ff4757, #ff6b81);
            color: white; border: none; border-radius: 12px; font-weight: 700;
            cursor: pointer; font-family: 'Poppins',sans-serif; font-size: 13px;
            display: flex; align-items: center; gap: 7px; transition: 0.3s;
        }
        .btn-izinkan:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,71,87,0.35); }
        .btn-skip {
            padding: 11px 18px; background: #f5f5f5; color: #888;
            border: none; border-radius: 12px; font-weight: 600;
            cursor: pointer; font-family: 'Poppins',sans-serif; font-size: 13px; transition: 0.2s;
        }
        .btn-skip:hover { background: #eee; color: #555; }

        /* Location chip (setelah izin diberikan) */
        .loc-chip {
            display: flex; align-items: center; gap: 8px; padding: 10px 14px;
            background: #f0faf5; border: 1px solid #c3f0d8; border-radius: 12px;
            margin-bottom: 14px; font-size: 13px; font-weight: 600; color: #27ae60;
        }
        .loc-chip.denied { background: #fff8e1; border-color: #ffe083; color: #b7860a; }

        /* Info box */
        .rs-info-box { background: #fff5f5; border-left: 5px solid #ff4757; border-radius: 15px; padding: 18px 22px; margin-top: 24px; }
        .rs-info-box h4 { color: #ff4757; font-weight: 700; margin-bottom: 8px; font-size: 14px; }
        .rs-info-box p  { font-size: 13px; color: #555; line-height: 1.7; }

        /* Surakarta badge on map */
        .solo-badge {
            position: absolute; top: 12px; left: 50%; transform: translateX(-50%);
            background: white; padding: 6px 14px; border-radius: 20px;
            font-family: 'Poppins',sans-serif; font-size: 12px; font-weight: 700;
            color: #1a1a1a; box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            z-index: 1000; display: flex; align-items: center; gap: 6px;
            pointer-events: none;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        @media(max-width:900px) {
            .rs-layout { grid-template-columns: 1fr; }
            #map { height: 380px; }
            .rs-list { max-height: 280px; }
        }
        @media(max-width:600px) {
            .rs-page { padding: 0 12px 60px; margin-top: 30px; }
            .rs-header h1 { font-size: 24px; }
            .rs-header p  { font-size: 14px; }
            .rs-panel { padding: 18px 16px; }
            #map { height: 320px; }
            .map-overlay { flex-direction: column; align-items: flex-start; gap: 8px; }
            .map-legend { flex-wrap: wrap; gap: 10px; }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="rs-page">
    <div class="rs-header">
        <h1>Rumah Sakit Terdekat</h1>
        <p>Temukan fasilitas kesehatan di sekitar Surakarta secara real-time</p>
    </div>

    <div class="rs-layout">

        <div>
            <div class="rs-panel">
                <div class="rs-panel-title">
                    <div class="icon-circle"><i data-lucide="map-pin"></i></div>
                    Daftar Rumah Sakit
                </div>


                <div id="permissionCard">
                    <div class="perm-icon-wrap">
                        <i data-lucide="map-pin" style="color:#ff4757;width:28px;height:28px;"></i>
                    </div>
                    <h3>Izinkan Akses Lokasi</h3>
                    <p>MEDIXA membutuhkan izin lokasi untuk menemukan rumah sakit <strong>terdekat di Surakarta</strong> dari posisi Anda saat ini.</p>
                    <div class="perm-btns">
                        <button class="btn-izinkan" onclick="requestLocation()">
                            <i data-lucide="navigation" style="width:16px;height:16px;"></i>
                            Izinkan Lokasi
                        </button>
                        <button class="btn-skip" onclick="useDefaultLocation()">
                            Gunakan Surakarta
                        </button>
                    </div>
                </div>


                <div id="locChip" style="display:none;"></div>

                <div class="search-rs" style="display:none;" id="searchWrap">
                    <i data-lucide="search" style="width:16px;height:16px;"></i>
                    <input type="text" id="searchRS" placeholder="Cari nama rumah sakit..." oninput="filterRS()">
                </div>

                <button class="btn-lokasi" id="btnLokasi" onclick="requestLocation()" style="display:none;">
                    <i data-lucide="refresh-cw" style="width:16px;height:16px;"></i>
                    Perbarui Lokasi
                </button>

                <div id="rsList"></div>
            </div>

            <div class="rs-info-box">
                <h4>Darurat? Hubungi Segera</h4>
                <p>Hotline Kesehatan: <strong>119</strong> &nbsp;|&nbsp; Darurat Nasional: <strong>112</strong><br>
                Data bersumber dari OpenStreetMap.</p>
            </div>
        </div>


        <div class="map-container" style="position:relative;">
            <div class="solo-badge">
                <i data-lucide="map-pin" style="width:13px;height:13px;color:#ff4757;"></i>
                Surakarta, Jawa Tengah
            </div>
            <div id="map"></div>
            <div class="map-overlay">
                <div class="map-stat">
                    <i data-lucide="hospital" style="width:16px;height:16px;color:#ff4757;"></i>
                    Ditemukan: <strong id="countRS">0</strong> rumah sakit
                </div>
                <div class="map-stat" id="radiusStat" style="display:none;">
                    <i data-lucide="circle" style="width:16px;height:16px;color:#007bff;"></i>
                    Radius: <strong>5 km</strong>
                </div>
                <div class="map-legend">
                    <div class="legend-item"><div class="legend-dot" style="background:#ff4757;"></div> RS / Klinik</div>
                    <div class="legend-item"><div class="legend-dot" style="background:#007bff;"></div> Lokasi Anda</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
lucide.createIcons();


const SOLO_LAT  = -7.5755;
const SOLO_LNG  = 110.8243;
const SOLO_ZOOM = 13;

let map, userMarker, userCircle;
let rsMarkers = [];
let rsData    = [];
let userLatLng = null;

// Init peta di Surakarta
map = L.map('map', { zoomControl: true }).setView([SOLO_LAT, SOLO_LNG], SOLO_ZOOM);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19
}).addTo(map);


const userIcon = L.divIcon({
    html: `<div style="width:18px;height:18px;background:#007bff;border:3px solid white;border-radius:50%;box-shadow:0 2px 8px rgba(0,123,255,0.5);"></div>`,
    iconSize: [18, 18], iconAnchor: [9, 9], className: ''
});

function createHospitalIcon(num) {
    return L.divIcon({
        html: `<div style="width:32px;height:32px;background:#ff4757;border:2.5px solid white;border-radius:50% 50% 50% 4px;transform:rotate(-45deg);box-shadow:0 2px 8px rgba(255,71,87,0.5);display:flex;align-items:center;justify-content:center;">
                 <span style="transform:rotate(45deg);color:white;font-size:11px;font-weight:800;">${num}</span>
               </div>`,
        iconSize: [32, 32], iconAnchor: [16, 30], popupAnchor: [0, -32], className: ''
    });
}


function requestLocation() {
    if (!navigator.geolocation) {
        useDefaultLocation();
        return;
    }

    // Tampilkan status loading di card
    const card = document.getElementById('permissionCard');
    card.innerHTML = `
        <div style="text-align:center;padding:10px 0;">
            <div class="spinner" style="margin:0 auto 14px;"></div>
            <p style="font-size:13px;color:#888;">Menunggu izin lokasi dari browser...</p>
        </div>`;

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            // Sembunyikan card, tampilkan chip sukses
            card.style.display = 'none';
            showLocChip(true, `${lat.toFixed(4)}, ${lng.toFixed(4)}`);
            showSearchAndBtn();
            fetchHospitals(lat, lng);
        },
        (err) => {
            // Izin ditolak → fallback ke Surakarta
            card.style.display = 'none';
            showLocChip(false, 'Izin ditolak — menggunakan pusat Surakarta');
            showSearchAndBtn();
            fetchHospitals(SOLO_LAT, SOLO_LNG);
        },
        { enableHighAccuracy: true, timeout: 12000 }
    );
}

// Langsung pakai koordinat Surakarta (tombol "Gunakan Surakarta")
function useDefaultLocation() {
    document.getElementById('permissionCard').style.display = 'none';
    showLocChip(false, 'Menggunakan pusat Kota Surakarta');
    showSearchAndBtn();
    fetchHospitals(SOLO_LAT, SOLO_LNG);
}

function showLocChip(success, text) {
    const chip = document.getElementById('locChip');
    chip.style.display = 'flex';
    chip.className = 'loc-chip' + (success ? '' : ' denied');
    const icon = success ? 'map-pin' : 'map';
    chip.innerHTML = `<i data-lucide="${icon}" style="width:15px;height:15px;flex-shrink:0;"></i> ${text}`;
    lucide.createIcons();
}

function showSearchAndBtn() {
    document.getElementById('searchWrap').style.display = 'block';
    document.getElementById('btnLokasi').style.display  = 'flex';
}


function showLoading() {
    document.getElementById('rsList').innerHTML = `
        <div class="state-box">
            <div class="spinner"></div>
            <p>Mencari rumah sakit di sekitar Surakarta...</p>
        </div>`;
}

function showError(msg) {
    document.getElementById('rsList').innerHTML = `
        <div class="state-box">
            <i data-lucide="alert-circle" style="width:44px;height:44px;display:block;margin:0 auto 12px;color:#ff4757;opacity:0.5;"></i>
            <p>${msg}</p>
        </div>`;
    lucide.createIcons();
}

async function fetchHospitals(lat, lng) {
    userLatLng = [lat, lng];
    showLoading();

    // Fokus peta ke lokasi
    map.setView([lat, lng], 14);

    if (userMarker) map.removeLayer(userMarker);
    if (userCircle) map.removeLayer(userCircle);

    userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(map)
        .bindPopup('<div style="font-family:Poppins,sans-serif;font-size:13px;font-weight:700;color:#007bff;">📍 Lokasi Anda</div>');

    userCircle = L.circle([lat, lng], {
        radius: 5000,
        color: '#007bff', fillColor: '#007bff', fillOpacity: 0.04,
        weight: 1.5, dashArray: '6 4'
    }).addTo(map);

    // Query Overpass — area Surakarta + radius dari lokasi user
    const query = `
        [out:json][timeout:30];
        (
          node["amenity"~"hospital|clinic|doctors|pharmacy"]["name"](around:5000,${lat},${lng});
          way["amenity"~"hospital|clinic|doctors|pharmacy"]["name"](around:5000,${lat},${lng});
          relation["amenity"~"hospital|clinic|doctors|pharmacy"]["name"](around:5000,${lat},${lng});
        );
        out center;
    `;

    try {
        const res  = await fetch('https://overpass-api.de/api/interpreter', { method: 'POST', body: query });
        const data = await res.json();

        // Bersihkan marker lama
        rsMarkers.forEach(m => map.removeLayer(m));
        rsMarkers = [];
        rsData    = [];

        const elements = data.elements || [];
        if (elements.length === 0) {
            showError('Tidak ada fasilitas kesehatan ditemukan dalam radius 5 km.<br><a href="https://www.google.com/maps/search/rumah+sakit+surakarta" target="_blank">Cari di Google Maps →</a>');
            document.getElementById('countRS').textContent = '0';
            document.getElementById('radiusStat').style.display = 'none';
            resetBtn();
            return;
        }

        elements.forEach(el => {
            const elLat = el.lat || el.center?.lat;
            const elLng = el.lon || el.center?.lon;
            if (!elLat || !elLng) return;
            const dist    = getDistance(lat, lng, elLat, elLng);
            const tags    = el.tags || {};
            const amenity = tags.amenity || 'hospital';
            rsData.push({ lat: elLat, lng: elLng, nama: tags.name || 'Fasilitas Kesehatan', amenity, dist, tags });
        });

        rsData.sort((a, b) => a.dist - b.dist);
        rsData = rsData.slice(0, 25);

        rsData.forEach((rs, i) => {
            const marker = L.marker([rs.lat, rs.lng], { icon: createHospitalIcon(i + 1) }).addTo(map);
            marker.bindPopup(buildPopup(rs, i + 1));
            marker.on('click', () => highlightListItem(i));
            rsMarkers.push(marker);
        });

        document.getElementById('countRS').textContent     = rsData.length;
        document.getElementById('radiusStat').style.display = 'flex';
        renderRSList(rsData);
        resetBtn();

    } catch(e) {
        showError('Gagal memuat data. Periksa koneksi internet lalu coba lagi.');
        resetBtn();
    }
}


function buildPopup(rs, num) {
    const amenityLabel = { hospital:'Rumah Sakit', clinic:'Klinik', doctors:'Dokter', pharmacy:'Apotek' };
    const label  = amenityLabel[rs.amenity] || 'Fasilitas Kesehatan';
    const warna  = rs.amenity === 'hospital' ? '#ff4757' : rs.amenity === 'pharmacy' ? '#27ae60' : '#007bff';
    return `<div style="font-family:Poppins,sans-serif;min-width:200px;">
        <div style="font-size:13px;font-weight:800;color:#1a1a1a;margin-bottom:4px;">${num}. ${rs.nama}</div>
        <span style="background:${warna}15;color:${warna};font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;">${label}</span>
        <div style="font-size:12px;color:#ff4757;font-weight:700;margin-top:8px;">📍 ${formatJarak(rs.dist)}</div>
        ${rs.tags.phone ? `<div style="font-size:11px;color:#666;margin-top:4px;">📞 ${rs.tags.phone}</div>` : ''}
        <a href="https://www.google.com/maps/dir/?api=1&destination=${rs.lat},${rs.lng}" target="_blank"
           style="display:inline-block;margin-top:10px;padding:6px 14px;background:#ff4757;color:white;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none;">
           Rute Google Maps →
        </a>
    </div>`;
}

function formatJarak(m) { return m < 1000 ? `${Math.round(m)} m` : `${(m/1000).toFixed(1)} km`; }

function getDistance(lat1, lng1, lat2, lng2) {
    const R = 6371000;
    const dLat = (lat2-lat1)*Math.PI/180;
    const dLng = (lng2-lng1)*Math.PI/180;
    const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}

const amenityColors = { hospital:['#ff4757','#fff0f1'], clinic:['#007bff','#f0f7ff'], doctors:['#27ae60','#f0faf5'], pharmacy:['#f39c12','#fff8e1'] };
const amenityLabels = { hospital:'Rumah Sakit', clinic:'Klinik', doctors:'Dokter', pharmacy:'Apotek' };

function renderRSList(data) {
    if (!data.length) {
        document.getElementById('rsList').innerHTML = `<div class="state-box"><p>Tidak ada hasil yang cocok.</p></div>`;
        return;
    }
    document.getElementById('rsList').innerHTML = data.map((rs, i) => {
        const [c] = amenityColors[rs.amenity] || ['#888'];
        const label = amenityLabels[rs.amenity] || 'Faskes';
        return `<div class="rs-item" id="rs-item-${i}" onclick="focusRS(${i})">
            <div class="rs-item-header">
                <div class="rs-num">${i+1}</div>
                <div style="flex:1;">
                    <h4>${rs.nama}</h4>
                    <div class="rs-jarak"><i data-lucide="map-pin" style="width:12px;height:12px;"></i> ${formatJarak(rs.dist)}</div>
                </div>
            </div>
            <div class="rs-alamat">${rs.tags['addr:street'] ? rs.tags['addr:street'] + (rs.tags['addr:city'] ? ', '+rs.tags['addr:city'] : '') : 'Surakarta, Jawa Tengah'}</div>
            <div class="rs-tags">
                <span class="rs-tag" style="background:${c}15;color:${c};">${label}</span>
                ${rs.tags.emergency === 'yes'   ? '<span class="rs-tag" style="background:#fff0f1;color:#ff4757;">🚨 IGD</span>' : ''}
                ${rs.tags['opening_hours']       ? `<span class="rs-tag" style="background:#f0faf5;color:#27ae60;">⏰ ${rs.tags['opening_hours'].substring(0,12)}</span>` : ''}
            </div>
        </div>`;
    }).join('');
    lucide.createIcons();
}

function filterRS() {
    const q = document.getElementById('searchRS').value.toLowerCase();
    const filtered = rsData.filter(rs => rs.nama.toLowerCase().includes(q) || (amenityLabels[rs.amenity]||'').toLowerCase().includes(q));
    renderRSList(filtered);
}

function focusRS(i) {
    document.querySelectorAll('.rs-item').forEach(el => el.classList.remove('active'));
    const item = document.getElementById(`rs-item-${i}`);
    if (item) { item.classList.add('active'); item.scrollIntoView({ behavior:'smooth', block:'nearest' }); }
    if (rsMarkers[i]) { rsMarkers[i].openPopup(); map.setView([rsData[i].lat, rsData[i].lng], 16, { animate:true }); }
}

function highlightListItem(i) {
    document.querySelectorAll('.rs-item').forEach(el => el.classList.remove('active'));
    const item = document.getElementById(`rs-item-${i}`);
    if (item) { item.classList.add('active'); item.scrollIntoView({ behavior:'smooth', block:'nearest' }); }
}

function resetBtn() {
    const btn = document.getElementById('btnLokasi');
    btn.disabled = false;
    btn.innerHTML = '<i data-lucide="refresh-cw" style="width:16px;height:16px;"></i> Perbarui Lokasi';
    lucide.createIcons();
}
</script>
</body>
</html>
