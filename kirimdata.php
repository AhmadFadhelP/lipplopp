<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "db_tugasakhir"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_GET['teg_panel']) && isset($_GET['arus_panel']) && isset($_GET['teg_batt']) && isset($_GET['arus_batt']) && isset($_GET['kekeruhan'])) {
    $teg_panel = $_GET['teg_panel'];
    $arus_panel = $_GET['arus_panel'];
    $teg_batt = $_GET['teg_batt'];
    $arus_batt = $_GET['arus_batt'];
    $kekeruhan = $_GET['kekeruhan'];

    $sql = "INSERT INTO monitoring (Tegangan Panel, Arus Panel, Tegangan Baterai, Arus Baterai, Kekeruhan Air)
            VALUES ('$teg_panel', '$arus_panel', '$teg_batt', '$arus_batt', '$kekeruhan')";

    if ($conn->query($sql) === TRUE) {
        echo "Data berhasil disimpan";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Parameter tidak lengkap!";
}

$conn->close();
?>
