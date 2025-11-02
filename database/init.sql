
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pakan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    berat VARCHAR(255) NOT NULL,
    waktu DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sensor_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    water_level DECIMAL(5,2) DEFAULT NULL COMMENT 'Water level percentage (0-100)',
    turbidity DECIMAL(6,2) DEFAULT NULL COMMENT 'Water turbidity in NTU',
    battery_voltage DECIMAL(5,2) DEFAULT NULL COMMENT 'Battery voltage in volts',
    battery_current DECIMAL(6,2) DEFAULT NULL COMMENT 'Battery current in amperes',
    solar_voltage DECIMAL(5,2) DEFAULT NULL COMMENT 'Solar panel voltage in volts',
    solar_current DECIMAL(6,2) DEFAULT NULL COMMENT 'Solar panel current in amperes',
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data reading timestamp',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_timestamp (timestamp),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='IoT sensor data from water quality and power monitoring';

INSERT INTO `sensor_data` (`id`, `water_level`, `turbidity`, `battery_voltage`, `battery_current`, `solar_voltage`, `solar_current`, `timestamp`, `created_at`, `updated_at`) VALUES
(1, 85.50, 15.20, 12.80, 2.10, 18.50, 1.50, '2025-11-02 10:00:00', '2025-11-02 10:00:00', '2025-11-02 10:00:00');


CREATE TABLE sensor_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alert_type VARCHAR(50) NOT NULL COMMENT 'Type of alert (low_battery, high_turbidity, etc)',
    alert_level ENUM('info', 'warning', 'critical') DEFAULT 'info',
    message TEXT NOT NULL,
    sensor_value DECIMAL(10,2) DEFAULT NULL,
    threshold_value DECIMAL(10,2) DEFAULT NULL,
    is_resolved BOOLEAN DEFAULT FALSE,
    resolved_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_alert_type (alert_type),
    INDEX idx_is_resolved (is_resolved),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='System alerts and notifications';
