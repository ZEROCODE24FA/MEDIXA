<?php
require_once __DIR__ . '/config.php';
sessionStart();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$nama   = trim($_POST['nama'] ?? '');
$email  = trim($_POST['email'] ?? '');
$jumlah = floatval($_POST['jumlah'] ?? 0);
$pesan  = trim($_POST['pesan'] ?? '');
$untuk  = trim($_POST['untuk'] ?? '');

if (empty($nama) || empty($email) || $jumlah < 10000) {
    jsonResponse(['success' => false, 'message' => 'Isi semua data. Minimum donasi Rp 10.000']);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'message' => 'Format email tidak valid']);
}

$db = getDB();
$stmt = $db->prepare("INSERT INTO donasi (nama_donatur, email_donatur, jumlah, pesan, status) VALUES (?, ?, ?, ?, 'pending')");
$stmt->execute([$nama, $email, $jumlah, $pesan]);
$donasiId = $db->lastInsertId();

sendDonasiEmail($email, $nama, $jumlah, $untuk, $pesan, $donasiId);

jsonResponse(['success' => true, 'message' => 'Terima kasih! Donasi Anda sedang diproses. Konfirmasi telah dikirim ke email Anda.']);
