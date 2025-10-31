<?php
$servername = "localhost";
$username   = "root";    // default XAMPP
$password   = "";        // default kosong
$dbname     = "db_tugasakhir";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
