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

$current_url = $_SERVER['REQUEST_URI'];

$dashboard_class = $current_url == '/project-JWP-2/admin/dashboard.php' ? 'bg-white' : 'text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-70';
$manage_services_class = strpos($current_url, '/project-JWP-2/admin/manage-services.php') !== false ||
                         strpos($current_url, '/project-JWP-2/admin/create-service.php') !== false ||
                         strpos($current_url, '/project-JWP-2/admin/edit-service.php') !== false ?
                         'bg-white text-slate-950' :
                         'text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-70';
$manage_orders_class = $current_url == '/project-JWP-2/admin/manage-orders.php' ? 'bg-white text-slate-950' : 'text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-70';
$order_reports_class = $current_url == '/project-JWP-2/admin/order-reports.php' ? 'bg-white text-slate-950' : 'text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-70';

// Include file konfigurasi database
include("../config.php");

// Proses form jika metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan data dari form
    $package_name = $_POST['package_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $status_publish = $_POST['status_publish'];

    // Proses file gambar yang diunggah
    $target_dir = "../image/"; // Direktori tempat menyimpan file gambar
    $image_name = uniqid() . '_' . basename($_FILES["image"]["name"]); // Nama file acak
    $target_file = $target_dir . $image_name; // Path lengkap file yang akan diupload
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // Mendapatkan tipe file

    // Check jika file gambar valid
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check ukuran file
    if ($_FILES["image"]["size"] > 1000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Izinkan format tertentu
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "webp") {
        echo "Sorry, only JPG, JPEG, PNG & WEBP files are allowed.";
        $uploadOk = 0;
    }

    // Check jika $uploadOk bernilai 0
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // Jika semua valid, upload file
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars($image_name) . " has been uploaded.";

            // Query untuk menyimpan data ke database
            $sql = "INSERT INTO tb_services (image, package_name, description, price, status_publish, created_at, updated_at) 
                    VALUES ('$image_name', '$package_name', '$description', '$price', '$status_publish', NOW(), NOW())";

            // Eksekusi query
            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
                header("Location: manage-services.php"); // Redirect ke halaman manage services setelah sukses
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    // Tutup koneksi
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    
    <div class="flex w-full">
        <div class="w-[22%] flex flex-col bg-[#6295A2] text-white h-screen">
            <h1 class="text-3xl font-bold text-center pt-8">JeWePe</h1>
            <div class="pt-4 flex flex-col">
            <a href="/project-JWP-2/admin/dashboard.php" class="px-6 text-xl py-3 font-bold <?php echo $dashboard_class; ?>">Dashboard</a>
            <a href="/project-JWP-2/admin/manage-services.php" class="px-6 text-xl py-3 font-bold <?php echo $manage_services_class; ?>">Manage Services</a>
            <a href="/project-JWP-2/admin/manage-orders.php" class="px-6 text-xl py-3 font-bold <?php echo $manage_orders_class; ?>">Manage Orders</a>
            <a href="/project-JWP-2/admin/order-reports.php" class="px-6 text-xl py-3 font-bold <?php echo $order_reports_class; ?>">Order Reports</a>
            <a href="../logout.php" class="px-6 text-xl py-3 font-bold text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-50">Logout</a>
            </div>
        </div>
        <div class="flex flex-col gap-8 w-[78%] bg-slate-100">
            <div class="w-full border-b shadow-md bg-[#80B9AD] bg-opacity-20">
                <h1 class="font-bold text-lg py-3 px-6">Hi, Admin</h1>
            </div>
            <div class="flex flex-col gap-1 flex-wrap px-5">
                <h5 class="font-medium text-lg mb-3">Create New Service</h5>
            <form action="create-service.php" method="POST" enctype="multipart/form-data" class="w-full flex flex-col gap-2">
                <!-- Field untuk unggah gambar -->
                <div class="flex flex-col gap-1">
                    <label for="image">Image</label>
                    <input class="rounded-md px-2 py-1 border-slate-300 border bg-white" type="file" id="image" name="image" accept=".png, .jpg, .jpeg, .webp">
                </div>
                <!-- Field lainnya seperti nama paket, deskripsi, harga, dan status publikasi -->
                <div class="flex flex-col gap-1">
                    <label for="package_name">Package Name</label>
                    <input class="rounded-md px-2 py-1 border-slate-300 border" type="text" id="package_name" name="package_name" placeholder="Input Package Name">
                </div>
                <div class="flex flex-col gap-1">
                    <label for="description">Description</label>
                    <textarea class="border rounded-md border-slate-300 px-2 py-1" id="description" name="description"></textarea>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="price">Price</label>
                    <input class="rounded-md px-2 py-1 border-slate-300 border" type="number" id="price" name="price" placeholder="Input Price">
                </div>
                <div class="flex flex-col gap-1">
                    <label for="status_publish">Publish Status</label>
                    <select id="status_publish" name="status_publish">
                        <option value="1">Published</option>
                        <option value="0">Not Published</option>
                    </select>
                </div>
                <div class="flex gap-2 pt-3">
                    <a href="/project-JWP-2/admin/manage-services.php" class="border px-3 py-1 bg-slate-200 border-slate-300 rounded-md">Cancel</a>
                    <input type="submit" value="Create" class="w-fit bg-blue-400 text-white px-3 py-1 rounded-md hover:bg-blue-600">
                </div>
            </form>

            </div>
        </div>
    </div>
</body>
</html>
