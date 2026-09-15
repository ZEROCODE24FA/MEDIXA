<?php
require_once __DIR__ . '/config.php';
sessionStart();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$nama  = trim($_POST['nama'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if (empty($nama)) {
    jsonResponse(['success' => false, 'message' => 'Nama tidak boleh kosong']);
}

$db = getDB();
$stmt = $db->prepare("UPDATE users SET nama = ?, phone = ? WHERE id = ?");
$stmt->execute([$nama, $phone, $_SESSION['user_id']]);
$_SESSION['user_name'] = $nama;

jsonResponse(['success' => true, 'message' => 'Profil berhasil diperbarui']);
