<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum, redirect ke halaman login
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h2>Welcome to Home</h2>
    <!-- Di sini Anda dapat menampilkan informasi pengguna, menampilkan data, atau menambahkan fungsi lainnya -->
    <p>Ini adalah halaman home.</p>
    <a href="logout.php">Logout</a>
</body>
</html>
