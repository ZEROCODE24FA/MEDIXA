<?php
require_once __DIR__ . '/config.php';
sessionStart();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'message' => 'Login untuk menyimpan riwayat BMI']);
}

$berat  = floatval($_POST['berat'] ?? 0);
$tinggi = floatval($_POST['tinggi'] ?? 0);

if ($berat <= 0 || $tinggi <= 0) {
    jsonResponse(['success' => false, 'message' => 'Data tidak valid']);
}

$tinggiM = $tinggi / 100;
$bmi = round($berat / ($tinggiM * $tinggiM), 1);

if ($bmi < 18.5)      $kategori = 'Kekurangan Berat Badan';
elseif ($bmi <= 24.9) $kategori = 'Normal (Ideal)';
elseif ($bmi <= 29.9) $kategori = 'Kelebihan Berat Badan';
else                   $kategori = 'Obesitas';

$db = getDB();
$stmt = $db->prepare("INSERT INTO bmi_history (user_id, berat, tinggi, bmi, kategori) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $berat, $tinggi, $bmi, $kategori]);

jsonResponse(['success' => true, 'bmi' => $bmi, 'kategori' => $kategori]);
