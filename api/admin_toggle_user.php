<?php
require_once __DIR__ . '/config.php';
sessionStart();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$userId = intval($_POST['user_id'] ?? 0);
$aktif  = intval($_POST['aktif'] ?? 0);

if (!$userId) {
    jsonResponse(['success' => false, 'message' => 'User ID tidak valid']);
}

$db = getDB();
$stmt = $db->prepare("UPDATE users SET aktif = ? WHERE id = ? AND role != 'admin'");
$stmt->execute([$aktif, $userId]);

jsonResponse(['success' => true, 'message' => 'Status pengguna diperbarui']);
