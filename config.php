<?php
// Informasi koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$database = "arfin_jwp";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}
?>
