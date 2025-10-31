<?php
header('Content-Type: application/json');

// Koneksi database
$host = "localhost";
$user = "root";       // sesuaikan
$pass = "";           // sesuaikan
$db   = "tugasakhir"; // sesuaikan

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit();
}

// Ambil data POST
if (isset($_POST['berat'])) {
    $berat = $conn->real_escape_string($_POST['berat']);

    // Simpan ke tabel (misalnya tabel `pakan`)
    $sql = "INSERT INTO pakan (berat, waktu) VALUES ('$berat', NOW())";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No data received"]);
}

$conn->close();
?>
