<?php
$servername = "db";
$username   = "root";    // default XAMPP
$password   = "root";        // default kosong
$dbname     = "db_tugasakhir";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
