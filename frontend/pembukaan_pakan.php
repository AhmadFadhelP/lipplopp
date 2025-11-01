<?php
session_start();
// cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: /frontend/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pembukaan Pakan</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #EEEEEE; /* sama seperti dashboard */
      color: #333;
    }
    header {
      background: linear-gradient(90deg, #4C6EF5, #5C7CFA); /* sama persis dashboard */
      padding: 15px 20px;
      color: white;
      font-size: 20px;
      font-weight: bold;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
      text-align: center;
    }
    .container {
      padding: 20px;
      max-width: 900px;
      margin: auto;
    }
    .section-title {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 20px;
      color: #333;
      text-align: center;
    }
    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }
    .card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      padding: 30px 15px;
      cursor: pointer;
      transition: all 0.25s ease;
      text-align: center;
      font-size: 20px;
      font-weight: bold;
      color: #333;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }
    .selected {
      border: 2px solid #4C6EF5; /* warna utama dashboard */
      background: #EEF2FF;       /* biru muda biar nyatu */
      color: #2C3E99;
    }
    .confirmation {
      margin-top: 20px;
      font-size: 16px;
      font-weight: bold;
      color: #4C6EF5;
      text-align: center;
      display: none;
    }
    .btn-back {
      display: inline-block;
      margin-top: 25px;
      padding: 12px 20px;
      background: linear-gradient(90deg, #4C6EF5, #5C7CFA); /* sama dashboard */
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 500;
      transition: 0.2s;
      box-shadow: 0 3px 8px rgba(0,0,0,0.15);
    }
    .btn-back:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 12px rgba(0,0,0,0.25);
    }
  </style>
  <script>
    function selectOption(cardId) {
      let cards = document.querySelectorAll('.card');
      cards.forEach(c => c.classList.remove('selected'));
      let card = document.getElementById(cardId);
      card.classList.add('selected');
      let berat = card.dataset.value;

      // Kirim ke server
      fetch("../backend/simpan_pakan.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "berat=" + encodeURIComponent(berat)
      })
      .then(res => res.json())
      .then(data => {
        let confirmBox = document.getElementById("confirmBox");
        if (data.success) {
          confirmBox.textContent = "✅ Berat pakan tersimpan: " + berat;
          confirmBox.style.display = "block";
        } else {
          confirmBox.textContent = "❌ Gagal menyimpan: " + data.message;
          confirmBox.style.display = "block";
        }
      })
      .catch(err => {
        let confirmBox = document.getElementById("confirmBox");
        confirmBox.textContent = "⚠️ Error: " + err;
        confirmBox.style.display = "block";
      });
    }
  </script>
</head>
<body>
  <header>
    Pembukaan Pakan
  </header>

  <div class="container">
    <div class="section-title">Pilih Berat Pakan</div>

    <div class="card-grid">
      <div id="opt1" class="card" data-value="500g" onclick="selectOption('opt1')">500 g</div>
      <div id="opt2" class="card" data-value="800g" onclick="selectOption('opt2')">800 g</div>
      <div id="opt3" class="card" data-value="1000g" onclick="selectOption('opt3')">1000 g</div>
    </div>

    <div id="confirmBox" class="confirmation"></div>

    <a href="dashboard.php" class="btn-back">← Kembali</a>
  </div>
</body>
</html>
