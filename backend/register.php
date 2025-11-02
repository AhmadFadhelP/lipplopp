<?php
session_start();
include "../database/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // VALIDASI INPUT
    if (strlen($username) < 3) die("Username minimal 3 karakter!");
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) die("Email tidak valid!");
    if (strlen($password) < 8) die("Password minimal 8 karakter!");

    // CEK EMAIL SUDAH ADA BELUM
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) die("Email sudah terdaftar!");

    // HASH PASSWORD
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // INSERT DATA
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $passwordHash);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Register berhasil! Silakan login.";
        header("Location: /frontend/index.php");
        exit;
    } else {
        echo "Register gagal: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
