# IoT Fish Feeder Web Application

This web application is a dashboard for monitoring and controlling an IoT-based fish feeding system. It allows users to monitor water quality and power systems, and to dispense fish food.

## Features

*   **User Authentication:** Users can register and log in to the system.
*   **Dashboard:** Displays real-time (simulated) data for:
    *   Water Quality: Water Level, Temperature, pH, and Turbidity.
    *   Power System: Input Voltage and Output Voltage.
*   **Fish Food Control:** Users can select a predefined weight of fish food to dispense.
*   **Feeding History:** Users can view a history of when and how much food was dispensed.

## How It Works

The application is built with PHP and uses a MySQL database.

1.  **Database (`config.php`):** The application connects to a MySQL database named `db_tugasakhir`. You will need to create this database and the necessary tables (`users`, `pakan`).

2.  **Authentication:**
    *   New users can register through the `signup.php` page, and their details are saved in the `users` table (`register.php`). Passwords are securely hashed.
    *   Registered users can log in via `index.php`. The `login.php` script verifies their credentials.
    *   Sessions are used to keep users logged in.

3.  **Dashboard (`dashboard.php`):**
    *   After logging in, the user is redirected to the dashboard.
    *   The dashboard displays various metrics. In this version, the data is randomly generated using JavaScript for demonstration purposes.

4.  **Fish Feeding:**
    *   The "Kontrol Pakan" page (`pembukaan_pakan.php`) allows the user to choose a weight of food.
    *   When a weight is selected, a request is sent to `simpan_pakan.php`, which records the feeding event in the `pakan` table in the database.
    *   The "Riwayat Pakan" page (`riwayat_pakan.php`) displays the feeding history from the `pakan` table.

## Database Setup

You will need to create a MySQL database and two tables.

**Database Name:** `db_tugasakhir`

**SQL for `users` table:**
```sql
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**SQL for `pakan` table:**
```sql
CREATE TABLE `pakan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `berat` varchar(50) NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## How to Run

1.  Make sure you have a web server with PHP and a MySQL database (like XAMPP or WAMP).
2.  Create the `db_tugasakhir` database and the `users` and `pakan` tables using the SQL above.
3.  Place all the project files in your web server's root directory (e.g., `htdocs` in XAMPP).
4.  Open your web browser and navigate to `http://localhost/your_project_folder/`.

## File Structure

*   `index.php`: Login page.
*   `signup.php`: Registration page.
*   `register.php`: Handles registration logic.
*   `login.php`: Handles login logic.
*   `logout.php`: Handles logout logic.
*   `config.php`: Database connection settings.
*   `dashboard.php`: The main dashboard page.
*   `pembukaan_pakan.php`: Page for selecting fish food weight.
*   `simpan_pakan.php`: API endpoint to save the food weight to the database.
*   `riwayat_pakan.php`: Page to display the feeding history.
*   `tests/`: Contains tests for the application.
