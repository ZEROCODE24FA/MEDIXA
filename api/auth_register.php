<?php
require_once __DIR__ . '/config.php';
sessionStart();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$nama    = trim($_POST['nama'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';
$role     = in_array($_POST['role'] ?? '', ['pengguna', 'donatur']) ? $_POST['role'] : 'pengguna';

if (empty($nama) || empty($email) || empty($password)) {
    jsonResponse(['success' => false, 'message' => 'Nama, email, dan password wajib diisi']);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'message' => 'Format email tidak valid']);
}

if (strlen($password) < 8) {
    jsonResponse(['success' => false, 'message' => 'Password minimal 8 karakter']);
}

if ($password !== $confirm) {
    jsonResponse(['success' => false, 'message' => 'Konfirmasi password tidak cocok']);
}

$db = getDB();
$existing = $db->prepare("SELECT id FROM users WHERE email = ?");
$existing->execute([$email]);
if ($existing->fetch()) {
    jsonResponse(['success' => false, 'message' => 'Email sudah terdaftar']);
}

$stmt = $db->prepare("INSERT INTO users (nama, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$nama, $email, $phone, password_hash($password, PASSWORD_BCRYPT), $role]);
$userId = $db->lastInsertId();

$_SESSION['user_id'] = $userId;
$_SESSION['user_name'] = $nama;
$_SESSION['user_role'] = $role;

jsonResponse(['success' => true, 'message' => 'Registrasi berhasil! Selamat datang di MEDIXA.', 'redirect' => BASE_PATH . '/home.php']);
