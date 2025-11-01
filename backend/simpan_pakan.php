<?php
header('Content-Type: application/json');

// Koneksi database
include '../database/config.php';

// Ambil data POST
if (isset($_POST['berat'])) {
    $berat = $_POST['berat'];

    // Simpan ke tabel (misalnya tabel `pakan`)
    $stmt = $conn->prepare("INSERT INTO pakan (berat, waktu) VALUES (?, NOW())");
    $stmt->bind_param("s", $berat);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "No data received"]);
}

$conn->close();
?>
