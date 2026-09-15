<?php
require_once __DIR__ . '/config.php';
sessionStart();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$nama       = trim($_POST['nama'] ?? '');
$telepon    = trim($_POST['telepon'] ?? '');
$alamat     = trim($_POST['alamat'] ?? '');
$penyakit   = trim($_POST['penyakit'] ?? '');
$deskripsi  = trim($_POST['deskripsi'] ?? '');
$target     = floatval($_POST['target'] ?? 0);

if (empty($nama) || empty($penyakit) || empty($deskripsi) || $target < 1000000) {
    jsonResponse(['success' => false, 'message' => 'Lengkapi semua data. Target minimal Rp 1.000.000.']);
}

$db = getDB();

// Check if already applied
$existing = $db->prepare("SELECT id FROM penerima_donasi WHERE user_id = ? AND status IN ('pending','approved')");
$existing->execute([$_SESSION['user_id']]);
if ($existing->fetch()) {
    jsonResponse(['success' => false, 'message' => 'Anda sudah memiliki permohonan yang sedang diproses atau disetujui.']);
}

$stmt = $db->prepare("INSERT INTO penerima_donasi (user_id, nama, penyakit, deskripsi, target) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $nama, $penyakit, $deskripsi, $target]);

jsonResponse(['success' => true, 'message' => 'Permohonan berhasil dikirim! Tim MEDIXA akan meninjau dalam 1–3 hari kerja. Terima kasih.']);
