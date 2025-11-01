<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: /frontend/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard IoT</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #f0f4ff, #d9e4ff);
      margin: 0;
      padding: 0;
      color: #333;
    }

    header {
      background: linear-gradient(90deg, #4C6EF5, #5C7CFA);
      padding: 20px;
      color: white;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
      border-bottom: 2px solid rgba(255,255,255,0.2);
      animation: fadeInDown 0.8s ease;
    }
    @keyframes fadeInDown {
      from {opacity:0; transform: translateY(-20px);}
      to {opacity:1; transform: translateY(0);}
    }

    header h2 { margin: 0; font-weight: 600; font-size: 22px; }
    header a {
      color: white; text-decoration: none; font-size: 14px;
      padding: 8px 14px; border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.4);
      transition: 0.3s ease;
    }
    header a:hover { background: rgba(255,255,255,0.2); }

    .container { padding: 30px; max-width: 1100px; margin: 0 auto; }
    .section-title {
      font-size: 22px;
      font-weight: 700;
      margin: 26px 0 16px;
      color: #2c3e50;
      border-left: 4px solid #4C6EF5;
      padding-left: 10px;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
      gap: 18px;
    }

    .card {
      background: white;
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.08);
      border: 1px solid rgba(0,0,0,0.05);
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    .card h4 {
      margin: 0;
      font-size: 15px;
      color: #555;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .card h4 i { font-size: 16px; color: #4C6EF5; }
    .card p {
      margin: 10px 0 0;
      font-size: 22px;
      font-weight: 700;
      color: #4C6EF5;
      transition: color 0.3s ease;
    }

    .progress-bar {
      width: 100%;
      height: 10px;
      border-radius: 5px;
      background: #eee;
      margin-top: 12px;
      overflow: hidden;
    }
    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #4C6EF5, #5C7CFA);
      width: 0%;
      transition: width 1s ease;
    }

    .button-link {
      display: inline-block;
      background: linear-gradient(90deg, #4C6EF5, #5C7CFA);
      border-radius: 12px;
      padding: 14px 22px;
      color: white;
      font-size: 16px;
      font-weight: 500;
      margin-top: 18px;
      margin-right: 12px;
      text-decoration: none;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    }
    .button-link:hover {
      transform: scale(1.06);
      box-shadow: 0 6px 18px rgba(0,0,0,0.25);
    }

    @media (max-width: 720px) {
      .button-link { display: block; margin-bottom: 12px; }
    }
  </style>
  <!-- icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
  <header>
    <h2>Hi, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h2>
    <div><a href="../backend/logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></div>
  </header>

  <div class="container">
    <div class="section-title">📊 Kualitas Air</div>
    <div class="grid">
      <div class="card">
        <h4><i class="fa fa-water"></i> Tingkat Air</h4>
        <p id="tingkatAir">75%</p>
        <div class="progress-bar"><div id="tingkatAirBar" class="progress-fill"></div></div>
      </div>


      <div class="card">
        <h4><i class="fa fa-eye"></i> Kekeruhan Air</h4>
        <p id="kekeruhanAir">25.5 NTU</p>
      </div>
    </div>

    <div class="section-title">⚡ Baterai Status</div>
    <div class="grid">
      <div class="card">
        <h4><i class="fa fa-plug"></i> Tegangan Baterai</h4>
        <p id="teganganBaterai">13.9 V</p>
      </div>
      <div class="card">
        <h4><i class="fa fa-bolt"></i> Arus Baterai</h4>
        <p id="arusBaterai">12.8 A</p>
      </div>
    </div>
    <div class="section-title">⚡ Solar Panel Status</div>
    <div class="grid">
      <div class="card">
        <h4><i class="fa fa-plug"></i> Tegangan Solar Panel</h4>
        <p id="teganganSolarpanel">13.9 V</p>
      </div>
      <div class="card">
        <h4><i class="fa fa-bolt"></i> Arus Solar Panel</h4>
        <p id="arusSolarpanel">12.8 A</p>
      </div>
    </div>

    <div class="section-title">🎛️ Kontrol</div>
    <a href="pembukaan_pakan.php" class="button-link"><i class="fa fa-fish"></i> Kontrol Pakan</a>
    <a href="riwayat_pakan.php" class="button-link"><i class="fa fa-history"></i> Riwayat Pakan</a>
  </div>

  <script>
    function getRandom(min, max, decimals = 1) {
      const str = (Math.random() * (max - min) + min).toFixed(decimals);
      return parseFloat(str);
    }

    function updateRandomData() {
      const tingkatAir = getRandom(50, 100, 0);
      const kekeruhan = getRandom(10, 80, 1);
      const teganganBaterai = getRandom(12, 14, 1);
      const arusBaterai = getRandom(11, 13, 1);
      const teganganSolarpanel = getRandom(12, 14, 1);
      const arusSolarpanel = getRandom(11, 13, 1);

      document.getElementById('tingkatAir').textContent = tingkatAir + '%';
      document.getElementById('kekeruhanAir').textContent = kekeruhan + ' NTU';
      document.getElementById('teganganBaterai').textContent = teganganBaterai + ' V';
      document.getElementById('arusBaterai').textContent = arusBaterai + ' A';
      document.getElementById('teganganSolarpanel').textContent = teganganSolarpanel + ' V';
      document.getElementById('arusSolarpanel').textContent = arusSolarpanel + ' A';

      document.getElementById('tingkatAirBar').style.width = tingkatAir + "%";

      // warna dinamis
      
    }

    updateRandomData();
    setInterval(updateRandomData, 5000);
  </script>
</body>
</html>
