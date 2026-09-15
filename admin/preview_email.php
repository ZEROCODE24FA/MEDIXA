<?php
require_once __DIR__ . '/../api/config.php';
sessionStart();
requireAdmin();

$kode    = 'MDX-000001';
$nominal = 'Rp 50.000';
$biaya   = 'Rp 5.000';
$bersih  = 'Rp 45.000';
$tgl     = date('d M Y, H:i') . ' WIB';
$toName  = 'Budi Santoso';
$untukTeks = 'Anak Kurang Mampu - Surakarta';
$pesan   = 'Semoga lekas sembuh dan bisa sekolah lagi. Semangat!';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Preview Template Email - MEDIXA</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>
<?php include __DIR__ . '/admin_style.php'; ?>
<style>
    .preview-bar {
        position: sticky; top: 0; z-index: 100;
        background: #1a1f36; padding: 12px 24px;
        display: flex; align-items: center; justify-content: space-between;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .preview-bar h3 { color: white; font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .preview-badge { background: rgba(0,123,255,0.2); color: #60a5fa; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
    .preview-back { display: flex; align-items: center; gap: 6px; color: rgba(255,255,255,0.6); text-decoration: none; font-size: 13px; font-weight: 600; transition: 0.2s; }
    .preview-back:hover { color: white; }
    .email-canvas { background: #f0f4f8; min-height: calc(100vh - 52px); padding: 32px 16px; }
    /* ── Email Template Styles ── */
    .wrapper { max-width: 560px; margin: 0 auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.1); }
    .header { background: linear-gradient(135deg, #007bff 0%, #00d2ff 100%); padding: 36px 32px 28px; text-align: center; }
    .logo-wrap { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 20px; }
    .logo-icon { width: 42px; height: 42px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .logo-text { font-size: 22px; font-weight: 800; color: white; letter-spacing: -0.5px; font-family: 'Poppins', sans-serif; }
    .logo-text span { color: #ffd6f5; }
    .check-circle { width: 72px; height: 72px; background: rgba(255,255,255,0.18); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 36px; }
    .header h1 { font-size: 22px; font-weight: 800; color: white; line-height: 1.3; font-family: 'Poppins', sans-serif; }
    .header p  { font-size: 14px; color: rgba(255,255,255,0.85); margin-top: 6px; font-family: 'Poppins', sans-serif; }
    .body { padding: 32px; }
    .greeting { font-size: 15px; color: #555; margin-bottom: 24px; line-height: 1.7; font-family: 'Poppins', sans-serif; }
    .amount-box { background: linear-gradient(135deg, #f0f8ff 0%, #e8f4ff 100%); border: 1px solid #cce0ff; border-radius: 16px; padding: 24px; text-align: center; margin-bottom: 24px; }
    .amount-label { font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; font-family: 'Poppins', sans-serif; }
    .amount-value { font-size: 36px; font-weight: 800; color: #007bff; line-height: 1; font-family: 'Poppins', sans-serif; }
    .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; font-family: 'Poppins', sans-serif; }
    .detail-table tr td { padding: 11px 0; border-bottom: 1px solid #f0f4f8; font-size: 13px; vertical-align: top; }
    .detail-table tr:last-child td { border-bottom: none; }
    .detail-table td:first-child { color: #888; width: 48%; }
    .detail-table td:last-child { color: #1a1a1a; font-weight: 600; text-align: right; }
    .status-badge-em { display: inline-block; background: #fff8e1; color: #b7860a; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; font-family: 'Poppins', sans-serif; }
    .info-box { background: #f8fbff; border-left: 4px solid #007bff; border-radius: 0 12px 12px 0; padding: 14px 16px; margin-bottom: 24px; font-size: 13px; color: #555; line-height: 1.65; font-family: 'Poppins', sans-serif; }
    .cta-btn { display: block; text-align: center; background: linear-gradient(90deg, #007bff, #00d2ff); color: white; text-decoration: none; padding: 15px 28px; border-radius: 12px; font-weight: 700; font-size: 15px; margin-bottom: 24px; font-family: 'Poppins', sans-serif; }
    .divider { height: 1px; background: #f0f4f8; margin: 8px 0 24px; }
    .pesan-box { background: #f9f4ff; border: 1px solid #e8d9fb; border-radius: 12px; padding: 16px; margin-bottom: 24px; font-family: 'Poppins', sans-serif; }
    .pesan-box .lbl { font-size: 11px; color: #a55eea; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; }
    .pesan-box p { font-size: 13px; color: #555; line-height: 1.65; font-style: italic; }
    .footer-em { background: #f8fbff; padding: 22px 32px; text-align: center; font-size: 12px; color: #aaa; line-height: 1.75; font-family: 'Poppins', sans-serif; }
</style>
</head>
<body>

<!-- Preview Top Bar -->
<div class="preview-bar">
    <h3>
        <i data-lucide="mail" style="width:16px;height:16px;color:#60a5fa;"></i>
        Preview Template Email Donasi
        <span class="preview-badge">Contoh</span>
    </h3>
    <a href="/admin/laporan_donasi.php" class="preview-back">
        <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Kembali ke Laporan
    </a>
</div>

<!-- Email Canvas -->
<div class="email-canvas">
    <div class="wrapper">

        <!-- Header -->
        <div class="header">
            <div class="logo-wrap">
                <div class="logo-icon">🛡️</div>
                <div class="logo-text">MEDI<span>XA</span></div>
            </div>
            <div class="check-circle">✅</div>
            <h1>Donasi Diterima!</h1>
            <p>Terima kasih atas kepedulian dan kebaikan Anda</p>
        </div>

        <!-- Body -->
        <div class="body">
            <p class="greeting">
                Halo, <strong><?= $toName ?></strong>! 👋<br>
                Kami dengan bangga mengkonfirmasi bahwa donasi Anda untuk <strong><?= $untukTeks ?></strong> telah berhasil kami terima dan sedang dalam proses verifikasi oleh tim MEDIXA.
            </p>

            <div class="amount-box">
                <div class="amount-label">Total Donasi</div>
                <div class="amount-value"><?= $nominal ?></div>
            </div>

            <table class="detail-table">
                <tr>
                    <td>Kode Transaksi</td>
                    <td><code style="background:#f0f4f8;padding:2px 8px;border-radius:6px;font-size:12px;"><?= $kode ?></code></td>
                </tr>
                <tr><td>Untuk</td><td><?= $untukTeks ?></td></tr>
                <tr><td>Nominal Donasi</td><td><?= $nominal ?></td></tr>
                <tr><td>Biaya Admin</td><td style="color:#888;"><?= $biaya ?></td></tr>
                <tr><td>Donasi Bersih</td><td style="color:#27ae60;font-weight:800;"><?= $bersih ?></td></tr>
                <tr><td>Tanggal &amp; Waktu</td><td><?= $tgl ?></td></tr>
                <tr><td>Status</td><td><span class="status-badge-em">⏳ Diproses</span></td></tr>
            </table>

            <div class="pesan-box">
                <div class="lbl">💬 Pesan Dukungan Anda</div>
                <p>"<?= htmlspecialchars($pesan) ?>"</p>
            </div>

            <div class="info-box">
                🔔 <strong>Informasi Penting:</strong> Donasi Anda akan diverifikasi oleh tim kami dalam 1×24 jam. Jika ada pertanyaan, hubungi kami di <strong>support@medixa.id</strong>.
            </div>

            <a href="#" class="cta-btn">🫀 Lihat Program Donasi</a>

            <div class="divider"></div>
            <p style="font-size:13px;color:#888;text-align:center;line-height:1.8;font-family:'Poppins',sans-serif;">
                Donasi Anda membuat perbedaan nyata bagi mereka yang membutuhkan.<br>
                <strong style="color:#007bff;">Bersama kita sehatkan Indonesia. 🇮🇩</strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer-em">
            <strong>MEDIXA</strong> — Sistem Manajemen Kesehatan Indonesia<br>
            Email ini dikirim otomatis, mohon tidak membalas pesan ini.<br>
            &copy; <?= date('Y') ?> MEDIXA. All rights reserved.
        </div>

    </div>
</div>

<script>lucide.createIcons();</script>
</body>
</html>
