<?php
require_once __DIR__ . '/../api/config.php';
$basePath = '';
sessionStart();
$isLogged = isLoggedIn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator BMI - MEDIXA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_fitur.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

    <main class="bmi-page-content">
        <header class="bmi-header">
            <h1>Kalkulator BMI</h1>
            <p>Hitung Indeks Massa Tubuh Anda</p>
        </header>

        <section class="bmi-main-grid">
            <div class="bmi-card">
                <div class="card-title">
                    <div class="icon-circle"><i data-lucide="calculator"></i></div>
                    <span>Hitung BMI</span>
                </div>
                <form id="bmiForm">
                    <div class="input-group">
                        <label>Berat Badan (kg)</label>
                        <input type="number" id="weight" placeholder="Masukkan berat badan" required step="0.1">
                    </div>
                    <div class="input-group">
                        <label>Tinggi Badan (cm)</label>
                        <input type="number" id="height" placeholder="Masukkan tinggi badan" required>
                    </div>
                    <button type="submit" class="btn-calculate">Hitung BMI Sekarang</button>
                    <?php if ($isLogged): ?>
                    <p id="save-status" style="text-align:center;font-size:12px;color:#888;margin-top:10px;display:none;"></p>
                    <?php else: ?>
                    <p style="text-align:center;font-size:12px;color:#aaa;margin-top:10px;"><a href="../login.php" style="color:#007bff;">Login</a> untuk menyimpan riwayat BMI</p>
                    <?php endif; ?>
                </form>
            </div>

            <div class="bmi-card result-card">
                <h3>Hasil Perhitungan</h3>
                <div class="result-placeholder" id="resultArea">
                    <div class="placeholder-content">
                        <i data-lucide="calculator" class="large-icon"></i>
                        <p>Masukkan data untuk melihat hasil</p>
                    </div>
                </div>
            </div>
        </section>

        <article class="bmi-info-box">
            <h4>Tentang BMI</h4>
            <p>Body Mass Index (BMI) adalah ukuran yang digunakan untuk menilai apakah berat badan seseorang seimbang dengan tinggi badannya. BMI dihitung dengan membagi berat badan (kg) dengan kuadrat tinggi badan (m²).</p>
        </article>

        <div class="bmi-categories" style="display:grid;grid-template-columns:repeat(4,1fr);gap:15px;margin-top:25px;">
            <div style="background:white;border-radius:16px;padding:20px;border-top:4px solid #f1c40f;box-shadow:0 5px 20px rgba(0,0,0,0.03);">
                <div style="font-weight:700;color:#f1c40f;margin-bottom:5px;">Kurus</div>
                <div style="font-size:13px;color:#888;">BMI &lt; 18.5</div>
            </div>
            <div style="background:white;border-radius:16px;padding:20px;border-top:4px solid #2ecc71;box-shadow:0 5px 20px rgba(0,0,0,0.03);">
                <div style="font-weight:700;color:#2ecc71;margin-bottom:5px;">Normal</div>
                <div style="font-size:13px;color:#888;">BMI 18.5 – 24.9</div>
            </div>
            <div style="background:white;border-radius:16px;padding:20px;border-top:4px solid #e67e22;box-shadow:0 5px 20px rgba(0,0,0,0.03);">
                <div style="font-weight:700;color:#e67e22;margin-bottom:5px;">Gemuk</div>
                <div style="font-size:13px;color:#888;">BMI 25 – 29.9</div>
            </div>
            <div style="background:white;border-radius:16px;padding:20px;border-top:4px solid #e74c3c;box-shadow:0 5px 20px rgba(0,0,0,0.03);">
                <div style="font-weight:700;color:#e74c3c;margin-bottom:5px;">Obesitas</div>
                <div style="font-size:13px;color:#888;">BMI &ge; 30</div>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();

        document.getElementById('bmiForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const weight = parseFloat(document.getElementById('weight').value);
            const heightCm = parseFloat(document.getElementById('height').value);
            if (!weight || !heightCm || weight <= 0 || heightCm <= 0) {
                alert("Masukkan berat dan tinggi yang valid!");
                return;
            }
            const heightM = heightCm / 100;
            const bmi = (weight / (heightM * heightM)).toFixed(1);
            const data = getBMICategory(bmi);
            updateResultUI(bmi, data);

            <?php if ($isLogged): ?>
            const fd = new FormData();
            fd.append('berat', weight);
            fd.append('tinggi', heightCm);
            try {
                const res = await fetch('/api/bmi_save.php', { method: 'POST', body: fd });
                const json = await res.json();
                const ss = document.getElementById('save-status');
                ss.style.display = 'block';
                ss.textContent = json.success ? '✓ Riwayat tersimpan' : '';
                ss.style.color = '#2ecc71';
            } catch(e) {}
            <?php endif; ?>
        });

        function getBMICategory(bmi) {
            if (bmi < 18.5) return { label: "Kekurangan Berat Badan", color: "#f1c40f", advice: "Cobalah tingkatkan asupan nutrisi dan konsultasi ke ahli gizi." };
            else if (bmi <= 24.9) return { label: "Normal (Ideal)", color: "#2ecc71", advice: "Luar biasa! Berat badan Anda ideal. Tetap jaga pola makan!" };
            else if (bmi <= 29.9) return { label: "Kelebihan Berat Badan", color: "#e67e22", advice: "Anda sedikit di atas rentang ideal. Yuk, mulai rutin olahraga." };
            else return { label: "Obesitas", color: "#e74c3c", advice: "Sangat disarankan untuk mulai pola hidup sehat dan olahraga rutin." };
        }

        function updateResultUI(bmi, data) {
            document.getElementById('resultArea').innerHTML = `
                <div style="text-align:center;padding:20px;animation:fadeIn .5s ease-in-out;">
                    <p style="font-size:14px;color:#888;margin-bottom:5px;">Skor BMI Anda</p>
                    <h2 style="font-size:64px;color:${data.color};margin-bottom:10px;font-weight:800;">${bmi}</h2>
                    <div style="background:${data.color};color:white;padding:6px 20px;border-radius:50px;display:inline-block;font-weight:700;margin-bottom:15px;font-size:14px;">${data.label}</div>
                    <p style="font-size:14px;color:#555;line-height:1.6;padding:0 20px;">${data.advice}</p>
                    <button onclick="window.location.reload()" style="margin-top:25px;background:none;border:1px solid #eee;padding:10px 20px;border-radius:12px;cursor:pointer;color:#aaa;font-size:12px;">Hitung Ulang</button>
                </div>`;
        }
    </script>
</body>
</html>
