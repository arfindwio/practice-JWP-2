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

include('../config.php');

// Handle status update if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['order_id']) && isset($_POST['status'])) {
        $order_id = $_POST['order_id'];
        $status = $_POST['status'];

        // Update status in database
        $update_sql = "UPDATE tb_order SET status = '$status' WHERE order_id = '$order_id'";

        if ($conn->query($update_sql) === TRUE) {
            header("Location: /project-JWP-2/admin/manage-orders.php");
            exit();
        } else {
            echo "Error updating status: " . $conn->error;
        }
    }
}

// Fetch data
$sql = "SELECT 
            o.order_id,
            s.package_name,
            s.price,
            u.full_name,
            u.email,
            u.phone_number,
            o.wedding_date,
            o.status
        FROM 
            tb_order o
        JOIN 
            tb_services s ON o.service_id = s.service_id
        JOIN 
            tb_users u ON o.user_id = u.user_id";

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
            <a href="/project-JWP-2/admin/order-reports.php" class="px-6 text-xl py-3 font-bold <?php echo $order_reports_class; ?>">Order Reports</a>
            <a href="../logout.php" class="px-6 text-xl py-3 font-bold text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-50">Logout</a>
            </div>
        </div>
        <div class="flex flex-col gap-8 w-[78%] bg-slate-100">
            <div class="w-full border-b shadow-md bg-[#80B9AD] bg-opacity-20">
                <h1 class="font-bold text-lg py-3 px-6">Hi, Admin</h1>
            </div>
            <div class="flex flex-col gap-1 flex-wrap px-5">
                <h5 class="font-medium text-lg mb-3">Manage Orders</h5>
                <table class="min-w-full border border-collapse">
                    <thead class="bg-slate-300">
                        <tr>
                            <th class="py-2 border">No</th>
                            <th class="py-2 border">Package Name</th>
                            <th class="py-2 border">Price</th>
                            <th class="py-2 border">Name</th>
                            <th class="py-2 border">Email</th>
                            <th class="py-2 border">Phone Number </th>
                            <th class="py-2 border">Wedding Date</th>
                            <th class="py-2 border">Status</th>
                        </tr>
                    </thead>
                    <tbody class="border border-collapse">
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $index = 0;
                                ?>
                                <tr>
                                    <td class='py-2 border px-2 text-center'><?php echo $index+1; ?></td>
                                    <td class='py-2 border px-2 text-center'><?php echo htmlspecialchars($row["package_name"]) ?></td>
                                    <td class="py-2 border px-2 text-center">IDR <?php echo number_format($row["price"], 0, ',', '.') ?></td>
                                    <td class="py-2 border px-2 text-center"><?php echo htmlspecialchars($row["full_name"]) ?></td>
                                    <td class="py-2 border px-2 text-center"><?php echo htmlspecialchars($row["email"]) ?></td>
                                    <td class="py-2 border px-2 text-center"><?php echo htmlspecialchars($row["phone_number"]) ?></td>
                                    <td class="py-2 border px-2 text-center"><?php echo date('d F Y', strtotime($row["wedding_date"])); ?></td>
                                    <td class="py-2 border px-2 text-center">
                                    <form method="post">
                                    <input type="hidden" name="order_id" value="<?php echo $row["order_id"]; ?>">
                                        <select name="status" onchange="this.form.submit()" class="px-2 py-1 rounded-md">
                                            <option value="requested" <?php echo ($row["status"] == "requested" ? 'selected' : ''); ?> >Requested</option>
                                            <option value="approved" <?php echo ($row["status"] == "approved" ? 'selected' : ''); ?> >Approved</option>
                                            <option value="cancelled" <?php echo ($row["status"] == "cancelled" ? 'selected' : ''); ?> >Cancelled</option>
                                        </select>
                                     </form>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            // Tampilkan pesan jika tidak ada layanan yang ditemukan
                            echo "<tr><td colspan='7' class='text-center py-2'>No orders found</td></tr>";
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
