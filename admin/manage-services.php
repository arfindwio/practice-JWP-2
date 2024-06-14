<?php
session_start();

// Redirect ke halaman login jika pengguna belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Redirect ke halaman tidak diizinkan jika pengguna bukan admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../home.php");
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

include('../config.php');

// Query untuk mendapatkan semua layanan dari database
$sql = "SELECT * FROM tb_services";
$result = $conn->query($sql);

// Periksa apakah query berhasil
if ($result === false) {
    echo "Error: " . $conn->error;
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services</title>
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
            <a href="../logout.php" class="px-6 text-xl py-3 font-bold text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-50">Logout</a>
            </div>
        </div>
        <div class="flex flex-col gap-8 w-[78%] bg-slate-100">
            <div class="w-full border-b shadow-md bg-[#80B9AD] bg-opacity-20">
                <h1 class="font-bold text-lg py-3 px-6">Hi, Admin</h1>
            </div>
            <div class="flex flex-col gap-1 flex-wrap px-5">
                <h5 class="font-medium text-lg mb-3">Manage Services</h5>
                <a href="/project-JWP-2/admin/create-service.php" class="px-4 py-2 bg-green-400 w-fit rounded-md hover:bg-green-600 text-white">Create Service</a>
                <table class="min-w-full border border-collapse">
                    <thead class="bg-slate-300">
                        <tr>
                            <th class="py-2 border">No</th>
                            <th class="py-2 border">Image</th>
                            <th class="py-2 border">Package Name</th>
                            <th class="py-2 border">Description</th>
                            <th class="py-2 border">Price</th>
                            <th class="py-2 border">Status Publish</th>
                            <th class="py-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border border-collapse">
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $index = 0;
                                ?>
                                <tr>
                                    <td class='py-2 border px-2'><?php echo $index+1; ?></td>
                                    <td class='py-2 border px-2'><img src='../image/<?php echo $row['image']; ?>' alt='<?php echo $row['package_name']; ?>' class='w-16 h-16 object-cover'></td>
                                    <td class='py-2 border px-2'><?php echo $row['package_name']; ?></td>
                                    <td class='py-2 border px-2'><?php echo $row['description']; ?></td>
                                    <td class='py-2 border px-2'>Rp. <?php echo $row['price']; ?></td>
                                    <td class='py-2 border px-2'><?php echo $row['status_publish'] ? 'Published' : 'Unpublished'; ?></td>
                                    <td class='py-2 px-2 border'>
                                    <div class='mr-1 mb-1 px-3 py-1 bg-yellow-400 hover:bg-yellow-600 rounded-md text-white w-fit'>

                                        <a href='edit-service.php?id=<?php echo $row['service_id']; ?>' >Edit</a>
                                    </div>    
                                    <div class='text-white w-fit bg-red-400 hover:bg-red-600 px-3 py-1 rounded-md'  onclick='return confirm("Are you sure you want to delete this service?")'>    
                                        <a href='delete-service.php?id=<?php echo $row['service_id']; ?>' >Delete</a>
                                    </div>    
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            // Tampilkan pesan jika tidak ada layanan yang ditemukan
                            echo "<tr><td colspan='7' class='text-center py-2'>No services found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
