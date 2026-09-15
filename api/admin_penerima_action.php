<?php
require_once __DIR__ . '/config.php';
sessionStart();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$id     = intval($_POST['id'] ?? 0);
$action = trim($_POST['action'] ?? '');

if (!$id || !in_array($action, ['approve', 'reject', 'delete'])) {
    jsonResponse(['success' => false, 'message' => 'Parameter tidak valid']);
}

$db = getDB();

if ($action === 'delete') {
    $stmt = $db->prepare("DELETE FROM penerima_donasi WHERE id = ?");
    $stmt->execute([$id]);
    jsonResponse(['success' => true, 'message' => 'Permohonan dihapus']);
}

$status = $action === 'approve' ? 'approved' : 'rejected';
$stmt   = $db->prepare("UPDATE penerima_donasi SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);

jsonResponse(['success' => true, 'message' => $action === 'approve' ? 'Permohonan disetujui' : 'Permohonan ditolak']);
