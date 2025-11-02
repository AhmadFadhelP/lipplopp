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

    .battery-section-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
    }

    .vertical-grid {
      grid-template-columns: 1fr;
    }

    .battery-layout {
      display: flex;
      gap: 18px;
      align-items: flex-start;
    }

    .battery-parameters {
      display: grid;
      grid-template-columns: 1fr;
      gap: 18px;
      flex-grow: 1;
    }

    @media (max-width: 720px) {
      .button-link { display: block; margin-bottom: 12px; }
      .battery-section-grid {
        grid-template-columns: 1fr;
      }
      .battery-layout {
        flex-direction: column;
      }
    }

    /* Battery Indicator CSS */
    .battery__card {
      position: relative;
      width: 100%;
      height: 240px;
      background-color: #fff;
      padding: 1.5rem 2rem;
      border-radius: 1.5rem;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      align-items: center;
    }

    .battery-section-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
    }

    .battery__text {
      margin-bottom: .5rem;
    }

    .battery__percentage {
      font-size: 2.5rem;
    }

    .battery__status {
      position: absolute;
      bottom: 1.5rem;
      display: flex;
      align-items: center;
      column-gap: .5rem;
      font-size: .75rem;
    }

    .battery__status i {
      font-size: 1.25rem;
    }

    .battery__pill {
      position: relative;
      width: 75px;
      height: 180px;
      background-color: #eee;
      box-shadow: inset 20px 0 48px hsl(0, 0%, 86%), 
                  inset -4px 12px 48px hsl(0, 0%, 96%);
      border-radius: 3rem;
      justify-self: flex-end;
    }

    .battery__level {
      position: absolute;
      inset: 2px;
      border-radius: 3rem;
      overflow: hidden;
    }

    .battery__liquid {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 36px;
      background: linear-gradient(90deg, hsl(7, 89%, 46%) 15%, hsl(11, 93%, 68%) 100%);
      box-shadow: inset -10px 0 12px hsla(0, 0%, 0%, .1), 
                  inset 12px 0 12px hsla(0, 0%, 0%, .15);
      transition: .3s;
    }

    .battery__liquid::after {
      content: '';
      position: absolute;
      height: 8px;
      background: linear-gradient(90deg, hsl(7, 89%, 46%) 15%, hsl(11, 93%, 68%) 100%);
      box-shadow: inset 0px -3px 6px hsla(0, 0%, 0%, .2);
      left: 0;
      right: 0;
      margin: 0 auto;
      top: -4px;
      border-radius: 50%;
    }

    .green-color {
      background: linear-gradient(90deg, hsl(92, 89%, 46%) 15%, hsl(92, 90%, 68%) 100%);
    }

    .animated-green {
      background: linear-gradient(90deg, hsl(92, 89%, 46%) 15%, hsl(92, 90%, 68%) 100%);
      animation: animated-charging-battery 1.2s infinite alternate;
    }

    .animated-red {
      background: linear-gradient(90deg, hsl(7, 89%, 46%) 15%, hsl(11, 93%, 68%) 100%);
      animation: animated-low-battery 1.2s infinite alternate;
    }

    .animated-green,
    .animated-red,
    .green-color {
      -webkit-background-clip: text;
      color: transparent;
    }

    @keyframes animated-charging-battery {
      0% {
        text-shadow: none;
      }
      100% {
        text-shadow: 0 0 6px hsl(92, 90%, 68%);
      }
    }

    @keyframes animated-low-battery {
      0% {
        text-shadow: none;
      }
      100% {
        text-shadow: 0 0 8px hsl(7, 89%, 46%);
      }
    }

    .gradient-color-red,
    .gradient-color-red::after {
      background: linear-gradient(90deg, hsl(7, 89%, 46%) 15%, hsl(11, 93%, 68%) 100%);
    }

    .gradient-color-orange,
    .gradient-color-orange::after {
      background: linear-gradient(90deg, hsl(22, 89%, 46%) 15%, hsl(54, 90%, 45%) 100%);
    }

    .gradient-color-yellow,
    .gradient-color-yellow::after {
      background: linear-gradient(90deg, hsl(54, 89%, 46%) 15%, hsl(92, 90%, 45%) 100%);
    }

    .gradient-color-green,
    .gradient-color-green::after {
      background: linear-gradient(90deg, hsl(92, 89%, 46%) 15%, hsl(92, 90%, 68%) 100%);
    }
  </style>
  <!-- icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <div class="battery-layout">
        <div class="battery__card">
            <div class="battery__data">
                <p class="battery__text">Battery</p>
                <h1 class="battery__percentage">
                    20%
                </h1>

                <p class="battery__status">
                    Low battery <i class="ri-plug-line"></i>
                </p>
            </div>

            <div class="battery__pill">
                <div class="battery__level">
                    <div class="battery__liquid"></div>
                </div>
            </div>
        </div>
        <div class="battery-parameters">
            <div class="card">
                <h4><i class="fa fa-plug"></i> Tegangan Baterai</h4>
                <p id="teganganBaterai">13.9 V</p>
            </div>
            <div class="card">
                <h4><i class="fa fa-bolt"></i> Arus Baterai</h4>
                <p id="arusBaterai">12.8 A</p>
            </div>
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

    <div class="section-title">📈 Real-time Charts</div>
    <div class="grid">
      <div class="card">
          <canvas id="airChart"></canvas>
      </div>
      <div class="card">
          <canvas id="powerChart"></canvas>
      </div>
    </div>

    <div class="section-title">🎛️ Kontrol</div>
    <a href="pembukaan_pakan.php" class="button-link"><i class="fa fa-fish"></i> Kontrol Pakan</a>
    <a href="riwayat_pakan.php" class="button-link"><i class="fa fa-history"></i> Riwayat Pakan</a>
  </div>

  <script>
    let airChart, powerChart;
    let lastDataId = null; // Track last data ID to prevent duplicates

    function initCharts() {
        const airCtx = document.getElementById('airChart').getContext('2d');
        airChart = new Chart(airCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Tingkat Air (%)',
                    data: [],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Kekeruhan Air (NTU)',
                    data: [],
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Value'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Time'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });

        const powerCtx = document.getElementById('powerChart').getContext('2d');
        powerChart = new Chart(powerCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Tegangan Baterai (V)',
                    data: [],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Arus Baterai (A)',
                    data: [],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Tegangan Solar Panel (V)',
                    data: [],
                    borderColor: 'rgba(255, 206, 86, 1)',
                    backgroundColor: 'rgba(255, 206, 86, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Arus Solar Panel (A)',
                    data: [],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Value'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Time'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });
    }

    function initBattery(voltage, current){
        const batteryLiquid = document.querySelector('.battery__liquid'),
              batteryStatus = document.querySelector('.battery__status'),
              batteryPercentage = document.querySelector('.battery__percentage')

        // Calculate battery percentage using voltage and current
        // For 12V Lead-Acid battery with compensation for charge/discharge current

        // Compensate voltage for current flow (internal resistance effect)
        // Typical internal resistance: ~0.01 ohm for 12V battery
        // During charging: measured voltage is higher than actual
        // During discharging: measured voltage is lower than actual
        const internalResistance = 0.015; // ohms
        const voltageCompensation = current * internalResistance;
        const compensatedVoltage = voltage - voltageCompensation;

        // Use non-linear state-of-charge curve for lead-acid battery
        // This is more accurate than linear interpolation
        let level;
        if (compensatedVoltage >= 12.65) {
            level = 100;
        } else if (compensatedVoltage >= 12.45) {
            // 12.65V-12.45V = 100%-90%
            level = 90 + ((compensatedVoltage - 12.45) / (12.65 - 12.45)) * 10;
        } else if (compensatedVoltage >= 12.24) {
            // 12.45V-12.24V = 90%-70%
            level = 70 + ((compensatedVoltage - 12.24) / (12.45 - 12.24)) * 20;
        } else if (compensatedVoltage >= 12.06) {
            // 12.24V-12.06V = 70%-40%
            level = 40 + ((compensatedVoltage - 12.06) / (12.24 - 12.06)) * 30;
        } else if (compensatedVoltage >= 11.88) {
            // 12.06V-11.88V = 40%-20%
            level = 20 + ((compensatedVoltage - 11.88) / (12.06 - 11.88)) * 20;
        } else if (compensatedVoltage >= 11.31) {
            // 11.88V-11.31V = 20%-5%
            level = 5 + ((compensatedVoltage - 11.31) / (11.88 - 11.31)) * 15;
        } else if (compensatedVoltage >= 10.50) {
            // 11.31V-10.50V = 5%-0%
            level = 0 + ((compensatedVoltage - 10.50) / (11.31 - 10.50)) * 5;
        } else {
            level = 0; // Critical low
        }

        level = Math.max(0, Math.min(100, Math.round(level))); // Clamp 0-100

        // Determine if charging (positive current means charging from solar)
        let charging = current > 0.5;

        batteryPercentage.innerHTML = level+ '%'
        batteryLiquid.style.height = `${level}%`

        // Determine battery status based on level, charging state, and current
        if(level >= 95 && charging){
            batteryStatus.innerHTML = `Fully charged <i class="ri-battery-2-fill green-color"></i> ${current.toFixed(1)}A`
            batteryLiquid.style.height = '103%'
        }
        else if(level >= 95){
            batteryStatus.innerHTML = `Full battery <i class="ri-battery-2-fill green-color"></i>`
            batteryLiquid.style.height = '103%'
        }
        else if(charging && current > 2.0){
            batteryStatus.innerHTML = `Fast charging <i class="ri-flashlight-line animated-green"></i> ${current.toFixed(1)}A`
        }
        else if(charging){
            batteryStatus.innerHTML = `Charging <i class="ri-flashlight-line animated-green"></i> ${current.toFixed(1)}A`
        }
        else if(level <= 20 && current < -1.0){
            batteryStatus.innerHTML = `Low battery (discharging) <i class="ri-plug-line animated-red"></i> ${Math.abs(current).toFixed(1)}A`
        }
        else if(level <= 20){
            batteryStatus.innerHTML = `Low battery <i class="ri-plug-line animated-red"></i>`
        }
        else if(current < -1.0){
            batteryStatus.innerHTML = `Discharging ${Math.abs(current).toFixed(1)}A`
        }
        else{
            batteryStatus.innerHTML = `Standby`
        }

        if(level <=20){
            batteryLiquid.classList.add('gradient-color-red')
            batteryLiquid.classList.remove('gradient-color-orange','gradient-color-yellow','gradient-color-green')
        }
        else if(level <= 40){
            batteryLiquid.classList.add('gradient-color-orange')
            batteryLiquid.classList.remove('gradient-color-red','gradient-color-yellow','gradient-color-green')
        }
        else if(level <= 80){
            batteryLiquid.classList.add('gradient-color-yellow')
            batteryLiquid.classList.remove('gradient-color-red','gradient-color-orange','gradient-color-green')
        }
        else{
            batteryLiquid.classList.add('gradient-color-green')
            batteryLiquid.classList.remove('gradient-color-red','gradient-color-orange','gradient-color-yellow')
        }
    }

    async function updateCharts() {
      try {
        // Fetch last 20 records for chart display
        const response = await fetch('/backend/api/sensors.php?limit=20');
        const result = await response.json();

        if (result.success && result.data && result.data.records && result.data.records.length > 0) {
          // Reverse to show oldest to newest
          const records = result.data.records.reverse();

          // Extract data for charts
          const labels = records.map(record => {
            const date = new Date(record.timestamp);
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
          });

          const waterLevels = records.map(r => parseFloat(r.water_level) || 0);
          const turbidities = records.map(r => parseFloat(r.turbidity) || 0);
          const batteryVoltages = records.map(r => parseFloat(r.battery_voltage) || 0);
          const batteryCurrents = records.map(r => parseFloat(r.battery_current) || 0);
          const solarVoltages = records.map(r => parseFloat(r.solar_voltage) || 0);
          const solarCurrents = records.map(r => parseFloat(r.solar_current) || 0);

          // Update air chart
          airChart.data.labels = labels;
          airChart.data.datasets[0].data = waterLevels;
          airChart.data.datasets[1].data = turbidities;
          airChart.update();

          // Update power chart
          powerChart.data.labels = labels;
          powerChart.data.datasets[0].data = batteryVoltages;
          powerChart.data.datasets[1].data = batteryCurrents;
          powerChart.data.datasets[2].data = solarVoltages;
          powerChart.data.datasets[3].data = solarCurrents;
          powerChart.update();

          console.log('Charts updated successfully with ' + records.length + ' records');
        } else {
          console.log('No data available for charts yet');
        }
      } catch (error) {
        console.error('Error updating charts:', error);
      }
    }

    async function updateRealData() {
      try {
        // Fetch latest sensor data from API
        const response = await fetch('/backend/api/sensors.php?latest=true');
        const result = await response.json();

        if (result.success && result.data) {
          const data = result.data;

          // Update display with real data (always update display values)
          const tingkatAir = parseFloat(data.water_level) || 0;
          const kekeruhan = parseFloat(data.turbidity) || 0;
          const teganganBaterai = parseFloat(data.battery_voltage) || 0;
          const arusBaterai = parseFloat(data.battery_current) || 0;
          const teganganSolarpanel = parseFloat(data.solar_voltage) || 0;
          const arusSolarpanel = parseFloat(data.solar_current) || 0;

          document.getElementById('tingkatAir').textContent = tingkatAir.toFixed(1) + '%';
          document.getElementById('kekeruhanAir').textContent = kekeruhan.toFixed(1) + ' NTU';
          document.getElementById('teganganBaterai').textContent = teganganBaterai.toFixed(1) + ' V';
          document.getElementById('arusBaterai').textContent = arusBaterai.toFixed(1) + ' A';
          document.getElementById('teganganSolarpanel').textContent = teganganSolarpanel.toFixed(1) + ' V';
          document.getElementById('arusSolarpanel').textContent = arusSolarpanel.toFixed(1) + ' A';

          document.getElementById('tingkatAirBar').style.width = tingkatAir + "%";

          initBattery(teganganBaterai, arusBaterai);

        } else {
          console.log('No sensor data available yet. Waiting for data...');
          // Show "waiting for data" message
          document.getElementById('tingkatAir').textContent = '--';
          document.getElementById('kekeruhanAir').textContent = '--';
          document.getElementById('teganganBaterai').textContent = '--';
          document.getElementById('arusBaterai').textContent = '--';
          document.getElementById('teganganSolarpanel').textContent = '--';
          document.getElementById('arusSolarpanel').textContent = '--';
        }
      } catch (error) {
        console.error('Error fetching sensor data:', error);
        // Keep showing last known data or show error
      }
    }

    initCharts();
    updateRealData();
    updateCharts();
    setInterval(updateRealData, 5000);
    setInterval(updateCharts, 30000); // Update charts every 30 seconds
  </script>
</body>
</html>
