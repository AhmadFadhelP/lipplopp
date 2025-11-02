<!DOCTYPE html>
<html>
<head>
    <title>Battery Calculation Test with Real Data</title>
    <style>
        body { font-family: 'Segoe UI', Arial; padding: 20px; background: linear-gradient(135deg, #f0f4ff, #d9e4ff); }
        .container { max-width: 1000px; margin: 0 auto; }
        h1 { color: #4C6EF5; text-align: center; }
        .comparison { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0; }
        .box { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .box h3 { color: #333; margin-top: 0; border-bottom: 2px solid #4C6EF5; padding-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f5f7ff; color: #4C6EF5; font-weight: 600; }
        .old-value { color: #999; }
        .new-value { color: #4C6EF5; font-weight: bold; }
        .status { padding: 4px 8px; border-radius: 4px; display: inline-block; font-size: 12px; }
        .charging { background: #d4edda; color: #155724; }
        .discharging { background: #f8d7da; color: #721c24; }
        .standby { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚡ Battery Percentage Calculation Comparison</h1>
        <p style="text-align: center; color: #666;">Comparing old linear method vs. new algorithm with current compensation</p>

        <div class="comparison">
            <div class="box">
                <h3>❌ Old Method (Linear)</h3>
                <p><small>Simple voltage mapping: (V - 10.5) / (12.6 - 10.5) × 100</small></p>
                <ul>
                    <li>Ignores current flow</li>
                    <li>Linear interpolation (inaccurate)</li>
                    <li>No compensation for charging/discharging</li>
                </ul>
            </div>

            <div class="box">
                <h3>✅ New Method (Advanced)</h3>
                <p><small>Voltage compensation + non-linear SOC curve</small></p>
                <ul>
                    <li>Compensates for internal resistance</li>
                    <li>Non-linear battery discharge curve</li>
                    <li>Accurate during charge/discharge</li>
                </ul>
            </div>
        </div>

        <div class="box">
            <h3>📊 Test Scenarios</h3>
            <table>
                <thead>
                    <tr>
                        <th>Scenario</th>
                        <th>Voltage</th>
                        <th>Current</th>
                        <th>Status</th>
                        <th>Old %</th>
                        <th>New %</th>
                        <th>Difference</th>
                    </tr>
                </thead>
                <tbody id="test-results">
                    <tr><td colspan="7" style="text-align: center;">Loading...</td></tr>
                </tbody>
            </table>
        </div>

        <div class="box" style="margin-top: 20px;">
            <h3>🔬 Algorithm Explanation</h3>
            <p><strong>Voltage Compensation:</strong></p>
            <code style="background: #f5f5f5; padding: 10px; display: block; border-radius: 4px;">
                Compensated Voltage = Measured Voltage - (Current × Internal Resistance)<br>
                Internal Resistance = 0.015Ω
            </code>
            <p style="margin-top: 15px;"><strong>State of Charge Curve (12V Lead-Acid):</strong></p>
            <ul>
                <li>≥12.65V → 100%</li>
                <li>12.45V-12.65V → 90%-100%</li>
                <li>12.24V-12.45V → 70%-90%</li>
                <li>12.06V-12.24V → 40%-70%</li>
                <li>11.88V-12.06V → 20%-40%</li>
                <li>11.31V-11.88V → 5%-20%</li>
                <li>10.50V-11.31V → 0%-5%</li>
            </ul>
        </div>
    </div>

    <script>
        // Old linear calculation
        function oldCalculation(voltage) {
            const minVoltage = 10.5;
            const maxVoltage = 12.6;
            let level = ((voltage - minVoltage) / (maxVoltage - minVoltage)) * 100;
            return Math.max(0, Math.min(100, Math.round(level)));
        }

        // New calculation with compensation
        function newCalculation(voltage, current) {
            const internalResistance = 0.015;
            const voltageCompensation = current * internalResistance;
            const compensatedVoltage = voltage - voltageCompensation;

            let level;
            if (compensatedVoltage >= 12.65) {
                level = 100;
            } else if (compensatedVoltage >= 12.45) {
                level = 90 + ((compensatedVoltage - 12.45) / (12.65 - 12.45)) * 10;
            } else if (compensatedVoltage >= 12.24) {
                level = 70 + ((compensatedVoltage - 12.24) / (12.45 - 12.24)) * 20;
            } else if (compensatedVoltage >= 12.06) {
                level = 40 + ((compensatedVoltage - 12.06) / (12.24 - 12.06)) * 30;
            } else if (compensatedVoltage >= 11.88) {
                level = 20 + ((compensatedVoltage - 11.88) / (12.06 - 11.88)) * 20;
            } else if (compensatedVoltage >= 11.31) {
                level = 5 + ((compensatedVoltage - 11.31) / (11.88 - 11.31)) * 15;
            } else if (compensatedVoltage >= 10.50) {
                level = 0 + ((compensatedVoltage - 10.50) / (11.31 - 10.50)) * 5;
            } else {
                level = 0;
            }

            return Math.max(0, Math.min(100, Math.round(level)));
        }

        const scenarios = [
            { name: "Fully Charged (Charging)", voltage: 13.00, current: 4.00 },
            { name: "Nearly Full (Light Charge)", voltage: 12.60, current: 1.50 },
            { name: "Medium Charge (Standby)", voltage: 12.30, current: 0.20 },
            { name: "Medium Charge (Discharging)", voltage: 12.10, current: -3.00 },
            { name: "Low Battery (Heavy Discharge)", voltage: 11.90, current: -4.50 },
            { name: "Recovering (Charging)", voltage: 12.00, current: 3.00 },
            { name: "Critical Low (Standby)", voltage: 11.50, current: 0.00 },
            { name: "Very Low (Discharging)", voltage: 11.20, current: -2.00 }
        ];

        let html = '';
        scenarios.forEach(scenario => {
            const oldPercent = oldCalculation(scenario.voltage);
            const newPercent = newCalculation(scenario.voltage, scenario.current);
            const diff = newPercent - oldPercent;
            const diffSign = diff > 0 ? '+' : '';

            let statusClass = 'standby';
            let statusText = 'Standby';
            if (scenario.current > 0.5) {
                statusClass = 'charging';
                statusText = 'Charging';
            } else if (scenario.current < -0.5) {
                statusClass = 'discharging';
                statusText = 'Discharging';
            }

            html += `
                <tr>
                    <td><strong>${scenario.name}</strong></td>
                    <td>${scenario.voltage.toFixed(2)}V</td>
                    <td>${scenario.current.toFixed(1)}A</td>
                    <td><span class="status ${statusClass}">${statusText}</span></td>
                    <td class="old-value">${oldPercent}%</td>
                    <td class="new-value">${newPercent}%</td>
                    <td style="color: ${diff > 0 ? '#28a745' : '#dc3545'}">${diffSign}${diff}%</td>
                </tr>
            `;
        });

        document.getElementById('test-results').innerHTML = html;
    </script>
</body>
</html>
