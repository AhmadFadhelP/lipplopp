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
    :root {
      --bg-gradient-start: #f0f4ff;
      --bg-gradient-end: #d9e4ff;
      --header-gradient-start: #4C6EF5;
      --header-gradient-end: #5C7CFA;
      --card-bg: white;
      --text-primary: #333;
      --text-secondary: #555;
      --text-title: #2c3e50;
      --border-color: rgba(0,0,0,0.05);
      --shadow-color: rgba(0,0,0,0.08);
      --shadow-hover: rgba(0,0,0,0.15);
      --accent-color: #4C6EF5;
      --progress-bg: #eee;
      --battery-bg: #eee;
    }

    body.dark-mode {
      --bg-gradient-start: #1a1a2e;
      --bg-gradient-end: #16213e;
      --header-gradient-start: #0f3460;
      --header-gradient-end: #16537e;
      --card-bg: #1e2a3a;
      --text-primary: #e4e4e4;
      --text-secondary: #b0b0b0;
      --text-title: #ffffff;
      --border-color: rgba(255,255,255,0.1);
      --shadow-color: rgba(0,0,0,0.3);
      --shadow-hover: rgba(0,0,0,0.5);
      --accent-color: #5C7CFA;
      --progress-bg: #2a3f5f;
      --battery-bg: #2a3f5f;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, var(--bg-gradient-start), var(--bg-gradient-end));
      margin: 0;
      padding: 0;
      color: var(--text-primary);
      transition: background 0.3s ease, color 0.3s ease;
      min-height: 100vh;
    }

    header {
      background: linear-gradient(90deg, var(--header-gradient-start), var(--header-gradient-end));
      padding: 20px;
      color: white;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
      border-bottom: 2px solid rgba(255,255,255,0.2);
      animation: fadeInDown 0.8s ease;
      flex-wrap: wrap;
      gap: 10px;
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

    .header-controls {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .dark-mode-toggle {
      background: rgba(255,255,255,0.2);
      border: 1px solid rgba(255,255,255,0.4);
      color: white;
      padding: 8px 12px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: 0.3s ease;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .dark-mode-toggle:hover {
      background: rgba(255,255,255,0.3);
    }

    .container { padding: 30px; max-width: 1100px; margin: 0 auto; }
    .section-title {
      font-size: 22px;
      font-weight: 700;
      margin: 26px 0 16px;
      color: var(--text-title);
      border-left: 4px solid var(--accent-color);
      padding-left: 10px;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
      gap: 18px;
    }

    .card {
      background: var(--card-bg);
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 6px 16px var(--shadow-color);
      border: 1px solid var(--border-color);
      transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.3s ease;
    }
    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 10px 20px var(--shadow-hover);
    }

    .card h4 {
      margin: 0;
      font-size: 15px;
      color: var(--text-secondary);
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .card h4 i { font-size: 16px; color: var(--accent-color); }
    .card p {
      margin: 10px 0 0;
      font-size: 22px;
      font-weight: 700;
      color: var(--accent-color);
      transition: color 0.3s ease;
    }

    .progress-bar {
      width: 100%;
      height: 10px;
      border-radius: 5px;
      background: var(--progress-bg);
      margin-top: 12px;
      overflow: hidden;
    }
    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, var(--header-gradient-start), var(--header-gradient-end));
      width: 0%;
      transition: width 1s ease;
    }

    .button-link {
      display: inline-block;
      background: linear-gradient(90deg, var(--header-gradient-start), var(--header-gradient-end));
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

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
      header {
        padding: 15px;
      }

      header h2 {
        font-size: 18px;
      }

      header a, .dark-mode-toggle {
        font-size: 12px;
        padding: 6px 10px;
      }

      .container {
        padding: 15px;
      }

      .section-title {
        font-size: 18px;
        margin: 20px 0 12px;
      }

      .grid {
        grid-template-columns: 1fr;
        gap: 12px;
      }

      .card {
        padding: 15px;
      }

      .card h4 {
        font-size: 14px;
      }

      .card p {
        font-size: 20px;
      }

      .button-link {
        display: block;
        margin-bottom: 12px;
        margin-right: 0;
        text-align: center;
        font-size: 14px;
        padding: 12px 18px;
      }

      .battery-section-grid {
        grid-template-columns: 1fr;
      }

      .battery-layout {
        flex-direction: column;
      }

      .battery__card {
        height: auto;
        min-height: 200px;
        padding: 1rem 1.5rem;
      }

      .battery__percentage {
        font-size: 2rem !important;
      }

      .battery__pill {
        width: 60px;
        height: 150px;
      }
    }

    /* Extra small mobile devices (portrait phones) */
    @media (max-width: 480px) {
      header h2 {
        font-size: 16px;
        width: 100%;
        text-align: center;
      }

      .header-controls {
        width: 100%;
        justify-content: center;
      }

      .container {
        padding: 10px;
      }

      .section-title {
        font-size: 16px;
      }

      .card p {
        font-size: 18px;
      }

      .battery__card {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 20px;
      }

      .battery__pill {
        justify-self: center;
      }

      .battery__status {
        position: relative;
        bottom: 0;
        justify-content: center;
        margin-top: 10px;
      }
    }

    /* Battery Indicator CSS */
    .battery__card {
      position: relative;
      width: 100%;
      height: 240px;
      background-color: var(--card-bg);
      padding: 1.5rem 2rem;
      border-radius: 1.5rem;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      align-items: center;
      box-shadow: 0 6px 16px var(--shadow-color);
      border: 1px solid var(--border-color);
      transition: background 0.3s ease;
    }

    .battery-section-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
    }

    .battery__text {
      margin-bottom: .5rem;
      transition: color 0.3s ease;
    }

    .battery__percentage {
      font-size: 2.5rem;
      transition: color 0.3s ease;
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
      background-color: var(--battery-bg);
      box-shadow: inset 20px 0 48px hsl(0, 0%, 86%),
                  inset -4px 12px 48px hsl(0, 0%, 96%);
      border-radius: 3rem;
      justify-self: flex-end;
      transition: background-color 0.3s ease;
    }

    body.dark-mode .battery__pill {
      box-shadow: inset 20px 0 48px rgba(0, 0, 0, 0.3),
                  inset -4px 12px 48px rgba(0, 0, 0, 0.2);
    }

    /* Dark mode chart improvements */
    body.dark-mode canvas {
      filter: brightness(0.9);
    }

    body.dark-mode .battery__text {
      color: var(--text-secondary);
    }

    body.dark-mode .battery__percentage {
      color: var(--text-primary);
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
    <div class="header-controls">
      <button class="dark-mode-toggle" id="darkModeToggle" onclick="toggleDarkMode()">
        <i class="fa fa-moon"></i>
        <span class="toggle-text">Dark</span>
      </button>
      <a href="../backend/logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>
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

    function getChartColors() {
        const isDarkMode = document.body.classList.contains('dark-mode');
        return {
            gridColor: isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)',
            textColor: isDarkMode ? '#e4e4e4' : '#666'
        };
    }

    function initCharts() {
        const colors = getChartColors();
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
                        grid: {
                            color: colors.gridColor
                        },
                        ticks: {
                            color: colors.textColor
                        },
                        title: {
                            display: true,
                            text: 'Value',
                            color: colors.textColor
                        }
                    },
                    x: {
                        grid: {
                            color: colors.gridColor
                        },
                        ticks: {
                            color: colors.textColor
                        },
                        title: {
                            display: true,
                            text: 'Time',
                            color: colors.textColor
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: colors.textColor
                        }
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
                        grid: {
                            color: colors.gridColor
                        },
                        ticks: {
                            color: colors.textColor
                        },
                        title: {
                            display: true,
                            text: 'Value',
                            color: colors.textColor
                        }
                    },
                    x: {
                        grid: {
                            color: colors.gridColor
                        },
                        ticks: {
                            color: colors.textColor
                        },
                        title: {
                            display: true,
                            text: 'Time',
                            color: colors.textColor
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: colors.textColor
                        }
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });
    }

    function updateChartColors() {
        const colors = getChartColors();

        // Update air chart
        if (airChart) {
            airChart.options.scales.y.grid.color = colors.gridColor;
            airChart.options.scales.y.ticks.color = colors.textColor;
            airChart.options.scales.y.title.color = colors.textColor;
            airChart.options.scales.x.grid.color = colors.gridColor;
            airChart.options.scales.x.ticks.color = colors.textColor;
            airChart.options.scales.x.title.color = colors.textColor;
            airChart.options.plugins.legend.labels.color = colors.textColor;
            airChart.update();
        }

        // Update power chart
        if (powerChart) {
            powerChart.options.scales.y.grid.color = colors.gridColor;
            powerChart.options.scales.y.ticks.color = colors.textColor;
            powerChart.options.scales.y.title.color = colors.textColor;
            powerChart.options.scales.x.grid.color = colors.gridColor;
            powerChart.options.scales.x.ticks.color = colors.textColor;
            powerChart.options.scales.x.title.color = colors.textColor;
            powerChart.options.plugins.legend.labels.color = colors.textColor;
            powerChart.update();
        }
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

    // Dark Mode Toggle Function
    function toggleDarkMode() {
      const body = document.body;
      const toggle = document.getElementById('darkModeToggle');
      const toggleText = toggle.querySelector('.toggle-text');
      const toggleIcon = toggle.querySelector('i');

      body.classList.toggle('dark-mode');

      // Update button icon and text
      if (body.classList.contains('dark-mode')) {
        toggleIcon.className = 'fa fa-sun';
        toggleText.textContent = 'Light';
        localStorage.setItem('darkMode', 'enabled');
      } else {
        toggleIcon.className = 'fa fa-moon';
        toggleText.textContent = 'Dark';
        localStorage.setItem('darkMode', 'disabled');
      }

      // Update chart colors when toggling dark mode
      updateChartColors();
    }

    // Load Dark Mode Preference on Page Load
    function loadDarkModePreference() {
      const darkMode = localStorage.getItem('darkMode');
      const body = document.body;
      const toggle = document.getElementById('darkModeToggle');
      const toggleText = toggle.querySelector('.toggle-text');
      const toggleIcon = toggle.querySelector('i');

      if (darkMode === 'enabled') {
        body.classList.add('dark-mode');
        toggleIcon.className = 'fa fa-sun';
        toggleText.textContent = 'Light';
      }
    }

    // Initialize everything
    loadDarkModePreference();
    initCharts();
    updateRealData();
    updateCharts();
    setInterval(updateRealData, 5000);
    setInterval(updateCharts, 30000); // Update charts every 30 seconds
  </script>
</body>
</html>
