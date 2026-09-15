<?php
require_once __DIR__ . '/config.php';
sessionStart();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    jsonResponse(['success' => false, 'message' => 'Username dan password wajib diisi']);
}

// Map username → email
$validUsernames = ['admin1','admin2','admin3','admin4'];
if (!in_array($username, $validUsernames)) {
    jsonResponse(['success' => false, 'message' => 'Username atau password salah']);
}

$email = $username . '@medixa.id';

$db = getDB();
$stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' AND aktif = 1");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    jsonResponse(['success' => false, 'message' => 'Username atau password salah']);
}

$_SESSION['user_id']   = $user['id'];
$_SESSION['user_name'] = $user['nama'];
$_SESSION['user_role'] = 'admin';

jsonResponse(['success' => true, 'message' => 'Login berhasil', 'redirect' => '/Medixa/admin/dashboard.php']);
