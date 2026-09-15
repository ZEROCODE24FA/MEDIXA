<?php
require_once __DIR__ . '/config.php';
sessionStart();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isLoggedIn()) {
    jsonResponse(['success' => false], 400);
}

$nama_obat = trim($_POST['nama_obat'] ?? '');
$kategori  = trim($_POST['kategori'] ?? '');

if (empty($nama_obat)) {
    jsonResponse(['success' => false, 'message' => 'Nama obat kosong']);
}

$db = getDB();

// Hindari duplikat berturutan — cek entri terakhir user
$last = $db->prepare("SELECT nama_obat FROM obat_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$last->execute([$_SESSION['user_id']]);
$lastRow = $last->fetch(PDO::FETCH_ASSOC);

if ($lastRow && $lastRow['nama_obat'] === $nama_obat) {
    jsonResponse(['success' => true, 'message' => 'duplicate_skip']);
}

$stmt = $db->prepare("INSERT INTO obat_history (user_id, nama_obat, kategori) VALUES (?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $nama_obat, $kategori]);

jsonResponse(['success' => true]);
