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

// Query SQL untuk menghitung total order dengan status 'requested'
$sql_requested_count = "SELECT COUNT(*) AS total_requested FROM tb_order WHERE status = 'requested'";
$result_requested_count = $conn->query($sql_requested_count);

if ($result_requested_count === false) {
    echo "Error: " . $conn->error;
    exit();
}

if ($result_requested_count->num_rows > 0) {
    $row_requested_count = $result_requested_count->fetch_assoc();
    $total_requested = $row_requested_count['total_requested'];
} else {
    $total_requested = 0;
}

// Query SQL untuk menghitung total order dengan status 'approved'
$sql_approved_count = "SELECT COUNT(*) AS total_approved FROM tb_order WHERE status = 'approved'";
$result_approved_count = $conn->query($sql_approved_count);

if ($result_approved_count === false) {
    echo "Error: " . $conn->error;
    exit();
}

if ($result_approved_count->num_rows > 0) {
    $row_approved_count = $result_approved_count->fetch_assoc();
    $total_approved = $row_approved_count['total_approved'];
} else {
    $total_approved = 0;
}

// Query SQL untuk menghitung total harga dari order dengan status 'requested'
$sql_requested_price = "SELECT SUM(tb_services.price) AS total_requested_price
                        FROM tb_order
                        JOIN tb_services ON tb_order.service_id = tb_services.service_id
                        WHERE tb_order.status = 'requested'";
$result_requested_price = $conn->query($sql_requested_price);

if ($result_requested_price === false) {
    echo "Error: " . $conn->error;
    exit();
}

if ($result_requested_price->num_rows > 0) {
    $row_requested_price = $result_requested_price->fetch_assoc();
    $total_requested_price = $row_requested_price['total_requested_price'];
} else {
    $total_requested_price = 0;
}

// Query SQL untuk menghitung total harga dari order dengan status 'approved'
$sql_approved_price = "SELECT SUM(tb_services.price) AS total_approved_price
                       FROM tb_order
                       JOIN tb_services ON tb_order.service_id = tb_services.service_id
                       WHERE tb_order.status = 'approved'";
$result_approved_price = $conn->query($sql_approved_price);

if ($result_approved_price === false) {
    echo "Error: " . $conn->error;
    exit();
}

if ($result_approved_price->num_rows > 0) {
    $row_approved_price = $result_approved_price->fetch_assoc();
    $total_approved_price = $row_approved_price['total_approved_price'];
} else {
    $total_approved_price = 0;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Reports</title>
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
            <div class="flex flex-col gap-4 flex-wrap px-5">
                <h5 class="font-medium text-lg mb-3">Order Reports</h5>
                <div class="flex flex-col gap-6">
                    <div class="flex justify-center flex-col gap-2">
                        <h5 class="text-center font-medium">Total Orders (Requested):</h5>
                        <div class="flex justify-center border-2 rounded-md shadow-sm">
                            <div class="flex flex-col w-1/2 text-center border px-4 justify-center">
                                <p>Quantity</p>
                                <p class="text-slate-400"><?php echo $total_requested; ?></p>
                            </div>
                            <div class="flex flex-col w-1/2 text-center border px-4 justify-center">
                                <p>Total Price</p>
                                <p class="text-slate-400">IDR <?php echo number_format($total_requested_price, 0, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center flex-col gap-2">
                        <h5 class="text-center font-medium">Total Orders (Approved):</h5>
                        <div class="flex justify-center border-2 rounded-md shadow-sm">
                            <div class="flex flex-col w-1/2 text-center border px-4 justify-center">
                                <p>Quantity</p>
                                <p class="text-slate-400"><?php echo $total_approved; ?></p>
                            </div>
                            <div class="flex flex-col w-1/2 text-center border px-4 justify-center">
                                <p>Total Price</p>
                                <p class="text-slate-400">IDR <?php echo number_format($total_approved_price, 0, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
