<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$current_url = $_SERVER['REQUEST_URI'];

$dashboard_class = $current_url == '/arfindwio/admin/dashboard.php' ? 'bg-white' : 'text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-70';
$manage_services_class = strpos($current_url, '/arfindwio/admin/manage-services.php') !== false ||
                         strpos($current_url, '/arfindwio/admin/create-service.php') !== false ||
                         strpos($current_url, '/arfindwio/admin/edit-service.php') !== false ?
                         'bg-white text-slate-950' :
                         'text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-70';
$manage_orders_class = $current_url == '/arfindwio/admin/manage-orders.php' ? 'bg-white text-slate-950' : 'text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-70';
$order_reports_class = $current_url == '/arfindwio/admin/order-reports.php' ? 'bg-white text-slate-950' : 'text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-70';

include('../config.php');

// Get the selected month from GET request
$selected_month = isset($_GET['month']) ? (int)$_GET['month'] : date('n'); // Default to current month if not set
$selected_year = date('Y'); // Default to current year

// Generate SQL queries with the month filter
$month_condition = $selected_month ? "AND MONTH(tb_order.created_at) = $selected_month" : '';

// Ensure that the table name prefix is used to avoid ambiguity
$sql_requested_count = "SELECT COUNT(*) AS total_requested 
                        FROM tb_order 
                        WHERE status = 'requested' $month_condition";
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

$sql_approved_count = "SELECT COUNT(*) AS total_approved 
                       FROM tb_order 
                       WHERE status = 'approved' $month_condition";
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

$sql_requested_price = "SELECT SUM(tb_services.price) AS total_requested_price
                        FROM tb_order
                        JOIN tb_services ON tb_order.service_id = tb_services.service_id
                        WHERE tb_order.status = 'requested' $month_condition";
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

$sql_approved_price = "SELECT SUM(tb_services.price) AS total_approved_price
                       FROM tb_order
                       JOIN tb_services ON tb_order.service_id = tb_services.service_id
                       WHERE tb_order.status = 'approved' $month_condition";
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
        <div class="hidden w-[22%] md:flex flex-col bg-[#6295A2] text-white h-screen">
            <h1 class="text-3xl font-bold text-center pt-8">JeWePe</h1>
            <div class="pt-4 flex flex-col">
                <a href="/arfindwio/admin/dashboard.php" class="px-6 text-xl py-3 font-bold <?php echo $dashboard_class; ?>">Dashboard</a>
                <a href="/arfindwio/admin/manage-services.php" class="px-6 text-xl py-3 font-bold <?php echo $manage_services_class; ?>">Manage Services</a>
                <a href="/arfindwio/admin/manage-orders.php" class="px-6 text-xl py-3 font-bold <?php echo $manage_orders_class; ?>">Manage Orders</a>
                <a href="/arfindwio/admin/order-reports.php" class="px-6 text-xl py-3 font-bold <?php echo $order_reports_class; ?>">Order Reports</a>
                <a href="../logout.php" class="px-6 text-xl py-3 font-bold text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-50">Logout</a>
            </div>
        </div>
        <div class="flex flex-col h-screen w-auto gap-8 md:w-[78%] bg-slate-100">
            <div class="w-full flex border-b shadow-md bg-[#80B9AD] bg-opacity-20 px-6">
                <h1 class="font-bold text-lg py-3">Hi, Admin</h1>
                <div class="flex gap-2 md:hidden ml-auto items-center">
                    <a href="/arfindwio/admin/dashboard.php" class="text-sm py-3 font-bold text-white">Dashboard</a>
                    <a href="/arfindwio/admin/manage-services.php" class="text-sm py-3 font-bold text-white">Services</a>
                    <a href="/arfindwio/admin/manage-orders.php" class="text-sm py-3 font-bold text-white ">Orders</a>
                    <a href="/arfindwio/admin/order-reports.php" class="text-sm py-3 font-bold text-white">Reports</a>
                    <a href="../logout.php" class="text-sm py-3 font-bold text-white">Logout</a>
                </div>
            </div>
            <div class="flex flex-col gap-4 flex-wrap px-5">
                <h5 class="font-medium text-lg mb-3">Order Reports</h5>
                <form method="GET" class="flex gap-4 mb-6">
                    <label for="month" class="flex items-center">Filter by Month:</label>
                    <select name="month" id="month" class="p-2 border rounded">
                        <?php
                        // Generate options for the months
                        for ($i = 1; $i <= 12; $i++) {
                            $month = date('F', mktime(0, 0, 0, $i, 10));
                            $selected = ($selected_month == $i) ? 'selected' : '';
                            echo "<option value=\"$i\" $selected>$month</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" class="p-2 bg-blue-500 text-white rounded">Filter</button>
                </form>
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
