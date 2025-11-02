<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
      width: 300px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    input {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      width: 100%;
      padding: 10px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }
    button:hover {
      background: #0056b3;
    }
    p {
      text-align: center;
      margin-top: 15px;
    }
    a {
      color: #007bff;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
    .error-message {
        text-align: center;
        color: red;
        margin-bottom: 15px;
        font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Login</h2>
    <?php
    // Periksa apakah ada pesan error di session
    if (isset($_SESSION['error_message'])) {
        echo "<p class='error-message'>" . $_SESSION['error_message'] . "</p>";
        // Hapus pesan error dari session agar tidak muncul lagi
        unset($_SESSION['error_message']);
    }
    // Periksa apakah ada pesan sukses di session
    if (isset($_SESSION['success_message'])) {
        echo "<p style='text-align: center; color: green; margin-bottom: 15px; font-weight: bold;'>" . $_SESSION['success_message'] . "</p>";
        // Hapus pesan sukses dari session agar tidak muncul lagi
        unset($_SESSION['success_message']);
    }
    ?>
    <form method="POST" action="../backend/login.php">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p>Belum punya akun? <a href="signup.php">Register</a></p>
  </div>
</body>
</html>