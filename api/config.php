<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'medixa_db'); 
define('DB_USER', 'root');
define('DB_PASS', 'root');          

define('BASE_PATH', '/Medixa');

function getDB() {
    static $db = null;

    if ($db === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            
            $db = new PDO($dsn, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch (PDOException $e) {
            die("Koneksi MySQL Gagal: " . $e->getMessage());
        }
    }

    return $db;
}

function initDB($db) {
    $statements = [
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            phone VARCHAR(50),
            password VARCHAR(255) NOT NULL,
            role VARCHAR(20) DEFAULT 'pengguna',
            aktif TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",

        "CREATE TABLE IF NOT EXISTS bmi_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            berat REAL,
            tinggi REAL,
            bmi REAL,
            kategori VARCHAR(50),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(user_id) REFERENCES users(id)
        ) ENGINE=InnoDB",

        "CREATE TABLE IF NOT EXISTS donasi (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_donatur VARCHAR(255),
            email_donatur VARCHAR(255),
            jumlah REAL,
            pesan TEXT,
            status VARCHAR(20) DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",

        "CREATE TABLE IF NOT EXISTS penerima_donasi (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            nama VARCHAR(255),
            penyakit VARCHAR(255),
            deskripsi TEXT,
            target REAL DEFAULT 0,
            terkumpul REAL DEFAULT 0,
            foto VARCHAR(255),
            status VARCHAR(20) DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(user_id) REFERENCES users(id)
        ) ENGINE=InnoDB",

        "CREATE TABLE IF NOT EXISTS obat_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            nama_obat VARCHAR(255) NOT NULL,
            kategori VARCHAR(100),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(user_id) REFERENCES users(id)
        ) ENGINE=InnoDB",

        "CREATE TABLE IF NOT EXISTS email_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            to_email VARCHAR(255) NOT NULL,
            to_name VARCHAR(255),
            subject VARCHAR(255),
            type VARCHAR(50) DEFAULT 'donasi',
            status VARCHAR(20) DEFAULT 'sent',
            error_msg TEXT,
            donasi_id INT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
    ];

    foreach ($statements as $sql) {
        $db->exec($sql);
    }

    // Add wallpaper column if not exists
    try { $db->exec("ALTER TABLE users ADD COLUMN wallpaper VARCHAR(50) DEFAULT 'gradient-1'"); } catch(Exception $e) {}

    // Seed admin accounts
    $admins = [
        ['Admin 1', 'admin1@medixa.id', 'V3425002'],
        ['Admin 2', 'admin2@medixa.id', 'V3425004'],
        ['Admin 3', 'admin3@medixa.id', 'V3425042'],
        ['Admin 4', 'admin4@medixa.id', 'V3425088'],
    ];
    foreach ($admins as $a) {
        $exists = $db->prepare("SELECT id FROM users WHERE email = ?");
        $exists->execute([$a[1]]);
        if (!$exists->fetch()) {
            $stmt = $db->prepare("INSERT INTO users (nama, email, phone, password, role) VALUES (?, ?, ?, ?, 'admin')");
            $stmt->execute([$a[0], $a[1], '081234567890', password_hash($a[2], PASSWORD_BCRYPT)]);
        }
    }
}

function requireAdmin() {
    sessionStart();
    if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
        header('Location: /admin/login.php');
        exit;
    }
}

function getAdminUser() {
    if (!isset($_SESSION['user_id'])) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT id, nama, email, role FROM users WHERE id = ? AND role = 'admin'");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function sessionStart() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    sessionStart();
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT id, nama, email, phone, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function sendDonasiEmail(string $toEmail, string $toName, float $jumlah, string $untuk, string $pesan, int $donasiId): void {
    $db = getDB();
    $kode    = 'MDX-' . str_pad($donasiId, 6, '0', STR_PAD_LEFT);
    $nominal = 'Rp ' . number_format($jumlah, 0, ',', '.');
    $biaya   = 'Rp 5.000';
    $bersih  = 'Rp ' . number_format($jumlah - 5000, 0, ',', '.');
    $tgl     = date('d M Y, H:i') . ' WIB';
    $untukTeks = $untuk ?: 'Program Donasi MEDIXA';

    $subject = "Konfirmasi Donasi MEDIXA – $kode";

    $html = <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { background: #f0f4f8; font-family: 'Segoe UI', Arial, sans-serif; color: #333; }
  .wrapper { max-width: 560px; margin: 32px auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.08); }
  .header { background: linear-gradient(135deg, #007bff 0%, #00d2ff 100%); padding: 36px 32px 28px; text-align: center; }
  .logo-wrap { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 20px; }
  .logo-icon { width: 42px; height: 42px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; }
  .logo-text { font-size: 22px; font-weight: 800; color: white; letter-spacing: -0.5px; }
  .logo-text span { color: #ffd6f5; }
  .check-circle { width: 72px; height: 72px; background: rgba(255,255,255,0.18); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 36px; }
  .header h1 { font-size: 22px; font-weight: 800; color: white; line-height: 1.3; }
  .header p  { font-size: 14px; color: rgba(255,255,255,0.85); margin-top: 6px; }
  .body { padding: 32px; }
  .greeting { font-size: 15px; color: #555; margin-bottom: 24px; line-height: 1.7; }
  .amount-box { background: linear-gradient(135deg, #f0f8ff 0%, #e8f4ff 100%); border: 1px solid #cce0ff; border-radius: 16px; padding: 24px; text-align: center; margin-bottom: 24px; }
  .amount-label { font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
  .amount-value { font-size: 36px; font-weight: 800; color: #007bff; line-height: 1; }
  .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
  .detail-table tr td { padding: 11px 0; border-bottom: 1px solid #f0f4f8; font-size: 13px; vertical-align: top; }
  .detail-table tr:last-child td { border-bottom: none; }
  .detail-table td:first-child { color: #888; width: 48%; }
  .detail-table td:last-child { color: #1a1a1a; font-weight: 600; text-align: right; }
  .status-badge { display: inline-block; background: #fff8e1; color: #b7860a; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
  .info-box { background: #f8fbff; border-left: 4px solid #007bff; border-radius: 0 12px 12px 0; padding: 14px 16px; margin-bottom: 24px; font-size: 13px; color: #555; line-height: 1.65; }
  .cta-btn { display: block; text-align: center; background: linear-gradient(90deg, #007bff, #00d2ff); color: white; text-decoration: none; padding: 15px 28px; border-radius: 12px; font-weight: 700; font-size: 15px; margin-bottom: 24px; }
  .divider { height: 1px; background: #f0f4f8; margin: 8px 0 24px; }
  .pesan-box { background: #f9f4ff; border: 1px solid #e8d9fb; border-radius: 12px; padding: 16px; margin-bottom: 24px; }
  .pesan-box .lbl { font-size: 11px; color: #a55eea; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; }
  .pesan-box p { font-size: 13px; color: #555; line-height: 1.65; font-style: italic; }
  .footer { background: #f8fbff; padding: 22px 32px; text-align: center; font-size: 12px; color: #aaa; line-height: 1.75; }
  .footer strong { color: #888; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <div class="logo-wrap">
      <div class="logo-icon">🛡️</div>
      <div class="logo-text">MEDI<span>XA</span></div>
    </div>
    <div class="check-circle">✅</div>
    <h1>Donasi Diterima!</h1>
    <p>Terima kasih atas kepedulian dan kebaikan Anda</p>
  </div>

  <div class="body">
    <p class="greeting">Halo, <strong>$toName</strong>! 👋<br>
    Kami dengan bangga mengkonfirmasi bahwa donasi Anda untuk <strong>$untukTeks</strong> telah berhasil kami terima dan sedang dalam proses verifikasi oleh tim MEDIXA.</p>

    <div class="amount-box">
      <div class="amount-label">Total Donasi</div>
      <div class="amount-value">$nominal</div>
    </div>

    <table class="detail-table">
      <tr><td>Kode Transaksi</td><td><code style="background:#f0f4f8;padding:2px 8px;border-radius:6px;font-size:12px;">$kode</code></td></tr>
      <tr><td>Untuk</td><td>$untukTeks</td></tr>
      <tr><td>Nominal Donasi</td><td>$nominal</td></tr>
      <tr><td>Biaya Admin</td><td style="color:#888;">$biaya</td></tr>
      <tr><td>Donasi Bersih</td><td style="color:#27ae60;font-weight:800;">$bersih</td></tr>
      <tr><td>Tanggal & Waktu</td><td>$tgl</td></tr>
      <tr><td>Status</td><td><span class="status-badge">⏳ Diproses</span></td></tr>
    </table>

HTML;

    if (!empty($pesan)) {
        $pesanHtml = htmlspecialchars($pesan);
        $html .= <<<HTML
    <div class="pesan-box">
      <div class="lbl">💬 Pesan Dukungan Anda</div>
      <p>"$pesanHtml"</p>
    </div>
HTML;
    }

    $html .= <<<HTML
    <div class="info-box">
      🔔 <strong>Informasi Penting:</strong> Donasi Anda akan diverifikasi oleh tim kami dalam 1×24 jam. Jika ada pertanyaan, hubungi kami di <strong>support@medixa.id</strong>.
    </div>

    <a href="https://medixa.id/fitur/donasi.php" class="cta-btn">🫀 Lihat Program Donasi</a>

    <div class="divider"></div>
    <p style="font-size:13px;color:#888;text-align:center;line-height:1.8;">
      Donasi Anda membuat perbedaan nyata bagi mereka yang membutuhkan.<br>
      <strong style="color:#007bff;">Bersama kita sehatkan Indonesia. 🇮🇩</strong>
    </p>
  </div>

  <div class="footer">
    <strong>MEDIXA</strong> — Sistem Manajemen Kesehatan Indonesia<br>
    Email ini dikirim otomatis, mohon tidak membalas pesan ini.<br>
    &copy; <?= date('Y') ?> MEDIXA. All rights reserved.
  </div>
</div>
</body>
</html>
HTML;

    $boundary = md5(uniqid());
    $headers  = implode("\r\n", [
        "MIME-Version: 1.0",
        "Content-Type: text/html; charset=UTF-8",
        "From: MEDIXA <noreply@medixa.id>",
        "Reply-To: support@medixa.id",
        "X-Mailer: MEDIXA-PHP",
        "X-Priority: 1",
    ]);

    $sent = @mail($toEmail, $subject, $html, $headers);

    $logStmt = $db->prepare("INSERT INTO email_log (to_email, to_name, subject, type, status, error_msg, donasi_id) VALUES (?, ?, ?, 'donasi', ?, ?, ?)");
    $logStmt->execute([
        $toEmail,
        $toName,
        $subject,
        $sent ? 'sent' : 'failed',
        $sent ? null : 'mail() returned false',
        $donasiId,
    ]);
}

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}