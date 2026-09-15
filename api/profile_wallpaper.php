<?php
require_once __DIR__ . '/config.php';
sessionStart();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false], 405);
}

$wallpaper = trim($_POST['wallpaper'] ?? '');

$allowed = ['gradient-1','gradient-2','gradient-3','gradient-4','gradient-5','gradient-6','gradient-7','gradient-8'];
if (!in_array($wallpaper, $allowed)) {
    jsonResponse(['success' => false, 'message' => 'Wallpaper tidak valid']);
}

$db = getDB();
$stmt = $db->prepare("UPDATE users SET wallpaper = ? WHERE id = ?");
$stmt->execute([$wallpaper, $_SESSION['user_id']]);

jsonResponse(['success' => true, 'message' => 'Wallpaper berhasil diubah']);
