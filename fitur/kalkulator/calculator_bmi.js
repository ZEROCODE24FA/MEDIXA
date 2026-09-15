document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    const bmiForm = document.getElementById('bmiForm');
    const resultArea = document.getElementById('resultArea');

    if (bmiForm) {
        bmiForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const weight = parseFloat(document.getElementById('weight').value);
            const heightCm = parseFloat(document.getElementById('height').value);

            if (!weight || !heightCm || weight <= 0 || heightCm <= 0) {
                alert("Masukkan berat dan tinggi yang valid ya!");
                return;
            }

            const heightM = heightCm / 100;
            const bmi = (weight / (heightM * heightM)).toFixed(1);

            const data = getBMICategory(bmi);

            updateResultUI(bmi, data);
        });
    }
});

function getBMICategory(bmi) {
    if (bmi < 18.5) {
        return {
            label: "Kekurangan Berat Badan",
            color: "#f1c40f", // Kuning
            advice: "Cobalah tingkatkan asupan nutrisi dan konsultasi ke ahli gizi."
        };
    } else if (bmi >= 18.5 && bmi <= 24.9) {
        return {
            label: "Normal (Ideal)",
            color: "#2ecc71", // Hijau
            advice: "Luar biasa! Berat badan Anda ideal. Tetap jaga pola makan!"
        };
    } else if (bmi >= 25 && bmi <= 29.9) {
        return {
            label: "Kelebihan Berat Badan",
            color: "#e67e22", // Oranye
            advice: "Anda sedikit di atas rentang ideal. Yuk, mulai rutin olahraga."
        };
    } else {
        return {
            label: "Obesitas",
            color: "#e74c3c", // Merah
            advice: "Sangat disarankan untuk mulai pola hidup sehat dan olahraga rutin."
        };
    }
}

function updateResultUI(bmi, data) {
    const resultArea = document.getElementById('resultArea');
    
    resultArea.innerHTML = `
        <div class="result-container" style="text-align: center; animation: fadeIn 0.5s ease-in-out;">
            <p style="font-size: 14px; color: #888; margin-bottom: 5px;">Skor BMI Anda</p>
            <h2 style="font-size: 64px; color: ${data.color}; margin-bottom: 10px; font-weight: 800;">${bmi}</h2>
            <div style="background: ${data.color}; color: white; padding: 6px 20px; border-radius: 50px; display: inline-block; font-weight: 700; margin-bottom: 15px; font-size: 14px;">
                ${data.label}
            </div>
            <p style="font-size: 14px; color: #555; line-height: 1.6; padding: 0 20px;">${data.advice}</p>
            <button onclick="window.location.reload()" style="margin-top: 25px; background: none; border: 1px solid #eee; padding: 10px 20px; border-radius: 12px; cursor: pointer; color: #aaa; font-size: 12px; transition: 0.3s;">
                Hitung Ulang
            </button>
        </div>
    `;
}