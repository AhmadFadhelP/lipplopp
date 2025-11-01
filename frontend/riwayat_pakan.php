<?php
session_start();
// cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: /frontend/login.php");
    exit();
}

// Koneksi database
include '../database/config.php';

// Ambil data pakan
$sql = "SELECT * FROM pakan ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Pakan</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #EEEEEE;
    }
    header {
      background-color: #648DDB;
      padding: 15px;
      color: white;
      font-size: 20px;
      font-weight: bold;
    }
    .container {
      padding: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }
    th {
      background-color: #648DDB;
      color: white;
    }
    tr:hover {
      background-color: #f5f5f5;
    }
    .btn-back {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 15px;
      background: #648DDB;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <header>
    Riwayat Pakan
  </header>

  <div class="container">
    <table>
      <tr>
        <th>ID</th>
        <th>Berat Pakan</th>
      </tr>
      <?php
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>".$row['id']."</td>
                      <td>".$row['berat']."</td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='2'>Belum ada data pakan</td></tr>";
      }
      ?>
    </table>

    <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
  </div>
</body>
</html>
<?php $conn->close(); ?>
