<?php
session_start();

// Redirect ke halaman login jika pengguna belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Redirect ke halaman tidak diizinkan jika pengguna bukan admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Periksa apakah parameter id service ada dan merupakan bilangan bulat
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Include file konfigurasi database
    include('../config.php');

    // Escape parameter id untuk mencegah SQL Injection
    $service_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Query SQL untuk mengambil data layanan berdasarkan service_id
    $sql_select = "SELECT * FROM tb_services WHERE service_id = '$service_id'";
    $result = $conn->query($sql_select);

    if ($result) {
        // Periksa apakah layanan ditemukan
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $image = $row['image'];

            // Hapus gambar terkait jika ada
            if (!empty($image)) {
                $image_path = "../image/" . $image;
                if (file_exists($image_path)) {
                    unlink($image_path); // Hapus gambar dari direktori
                }
            }

            // Query SQL untuk menghapus layanan berdasarkan service_id
            $sql_delete = "DELETE FROM tb_services WHERE service_id = '$service_id'";

            if ($conn->query($sql_delete) === true) {
                // Jika penghapusan berhasil, redirect kembali ke halaman manage-services.php
                header("Location: manage-services.php");
                exit();
            } else {
                // Jika terjadi kesalahan dalam query hapus
                echo "Error deleting service: " . $conn->error;
            }
        } else {
            // Jika layanan tidak ditemukan
            echo "Service not found.";
        }
    } else {
        // Jika terjadi kesalahan dalam query ambil data layanan
        echo "Error: " . $conn->error;
    }

    // Tutup koneksi database
    $conn->close();
} else {
    // Jika parameter id tidak ada atau tidak valid
    echo "Invalid service id.";
}
?>
