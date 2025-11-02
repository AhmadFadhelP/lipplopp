<?php
session_start();
include "../database/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // QUERY AMAN
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true); // aman
            $_SESSION['username'] = $row['username'];
            header("Location: /frontend/dashboard.php"); // arahkan ke dashboard
            exit;
        } else {
            $_SESSION['error_message'] = "Password salah!";
            header("Location: /frontend/index.php");
            exit;
        }
    } else {
        $_SESSION['error_message'] = "User tidak ditemukan!";
        header("Location: /frontend/index.php");
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>
