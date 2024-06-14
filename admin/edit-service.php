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

// Include file konfigurasi database
include("../config.php");

$current_url = $_SERVER['REQUEST_URI'];

$dashboard_class = $current_url == '/project-JWP-2/admin/dashboard.php' ? 'bg-white' : 'text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-70';
$manage_services_class = strpos($current_url, '/project-JWP-2/admin/manage-services.php') !== false ||
                         strpos($current_url, '/project-JWP-2/admin/create-service.php') !== false ||
                         strpos($current_url, '/project-JWP-2/admin/edit-service.php') !== false ?
                         'bg-white text-slate-950' :
                         'text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-70';
$manage_orders_class = $current_url == '/project-JWP-2/admin/manage-orders.php' ? 'bg-white text-slate-950' : 'text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-70';
$order_reports_class = $current_url == '/project-JWP-2/admin/order-reports.php' ? 'bg-white text-slate-950' : 'text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-70';

// Ambil ID layanan yang akan diedit dari parameter URL
if (isset($_GET['id'])) {
    $service_id = $_GET['id'];

    // Query untuk mengambil data layanan berdasarkan ID
    $sql = "SELECT * FROM tb_services WHERE service_id = $service_id";
    $result = $conn->query($sql);

    if ($result) {
        // Periksa jumlah baris hasil query
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $image = $row['image'];
            $package_name = $row['package_name'];
            $description = $row['description'];
            $price = $row['price'];
            $status_publish = $row['status_publish'];
        } else {
            echo "Service not found.";
            exit();
        }
    } else {
        echo "Error retrieving service: " . $conn->error;
        exit();
    }
} else {
    echo "Service ID not provided.";
    exit();
}

// Proses form jika metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan data dari form
    $package_name = $_POST['package_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $status_publish = $_POST['status_publish'];

    // Proses file gambar yang diunggah jika ada perubahan gambar
    if ($_FILES['image']['name'] != '') {
        $target_dir = "../image/";
        $image_name = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

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
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "webp") {
            echo "Sorry, only JPG, JPEG, PNG & WEBP files are allowed.";
            $uploadOk = 0;
        }

        // Check jika $uploadOk bernilai 0
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            // Hapus gambar lama sebelum mengupload yang baru
            if (!empty($image)) {
                $old_image_path = "../image/" . $image;
                if (file_exists($old_image_path)) {
                    unlink($old_image_path); // Hapus gambar lama dari direktori
                }
            }

            // Jika semua valid, upload file baru
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                echo "The file " . htmlspecialchars(basename($_FILES["image"]["name"])) . " has been uploaded.";
                // Update data di database dengan gambar baru
                $sql_update = "UPDATE tb_services SET image='$image_name', package_name='$package_name', description='$description', price='$price', status_publish='$status_publish', updated_at=NOW() WHERE service_id=$service_id";

                if ($conn->query($sql_update) === TRUE) {
                    echo "Service updated successfully";
                    header("Location: manage-services.php");
                    exit();
                } else {
                    echo "Error updating service: " . $conn->error;
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        // Jika tidak ada perubahan gambar, update data tanpa mengubah gambar
        $sql_update = "UPDATE tb_services SET package_name='$package_name', description='$description', price='$price', status_publish='$status_publish', updated_at=NOW() WHERE service_id=$service_id";

        if ($conn->query($sql_update) === TRUE) {
            echo "Service updated successfully";
            header("Location: manage-services.php");
            exit();
        } else {
            echo "Error updating service: " . $conn->error;
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
                <h5 class="font-medium text-lg mb-3">Edit Service</h5>
            <form action="edit-service.php?id=<?php echo $service_id; ?>" method="POST" enctype="multipart/form-data" class="w-full flex flex-col gap-2">
                <!-- Field untuk unggah gambar -->
                <div class="flex flex-col gap-1">
                    <label for="image">Image</label>
                    <input class="rounded-md px-2 py-1 border-slate-300 border bg-white" type="file" id="image" name="image" accept=".png, .jpg, .jpeg, .webp">
                </div>
                <!-- Field lainnya seperti nama paket, deskripsi, harga, dan status publikasi -->
                <div class="flex flex-col gap-1">
                    <label for="package_name">Package Name</label>
                    <input class="rounded-md px-2 py-1 border-slate-300 border" type="text" id="package_name" name="package_name" placeholder="Input Package Name" value="<?php echo htmlspecialchars($package_name); ?>">
                </div>
                <div class="flex flex-col gap-1">
                    <label for="description">Description</label>
                    <textarea class="border rounded-md border-slate-300" id="description" name="description" rows="4" placeholder="Enter Description"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="price">Price</label>
                    <input class="rounded-md px-2 py-1 border-slate-300 border" type="number" id="price" name="price" placeholder="Enter Price" value="<?php echo htmlspecialchars($price); ?>">
                </div>
                <div class="flex flex-col gap-1">
                    <label for="status_publish">Publish Status</label>
                    <select id="status_publish" name="status_publish">
                    <option value="1" <?php if ($status_publish == 1) echo 'selected'; ?>>Published</option>
                        <option value="0" <?php if ($status_publish == 0) echo 'selected'; ?>>Not Published</option>
                    </select>
                </div>
                <div class="flex gap-2 pt-3">
                    <a href="/project-JWP-2/admin/manage-services.php" class="border px-3 py-1 bg-slate-200 border-slate-300 rounded-md">Cancel</a>
                    <input type="submit" value="Update" class="w-fit bg-blue-400 text-white px-3 py-1 rounded-md hover:bg-blue-600">
                </div>
            </form>

            </div>
        </div>
    </div>
</body>
</html>

