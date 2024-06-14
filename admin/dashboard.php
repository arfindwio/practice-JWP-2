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

$dashboard_class = $current_url == '/project-JWP-2/admin/dashboard.php' ? 'bg-white text-slate-950' : 'text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-70';
$manage_services_class = strpos($current_url, '/project-JWP-2/admin/manage-services.php') !== false ||
                         strpos($current_url, '/project-JWP-2/admin/create-service.php') !== false ||
                         strpos($current_url, '/project-JWP-2/admin/edit-service.php') !== false ?
                         'bg-white text-slate-950' :
                         'text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-70';
$manage_orders_class = $current_url == '/project-JWP-2/admin/manage-orders.php' ? 'bg-white text-slate-950' : 'text-white hover:bg-white hover:text-slate-950 hover:bg-opacity-70';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
            <div class="flex gap-4 flex-wrap justify-center">
                    <div class="flex w-fit gap-3 px-6 py-4 bg-[#B3E2A7] text-white items-center text-lg rounded-lg">
                        <img src="../image/orders_logo.webp" alt="" class="w-20 h-20 object-cover">
                        <div class="flex text-2xl flex-col">
                            <p>4</p>
                            <p>Orders</p>
                        </div>
                    </div>
                    <div class="flex w-fit gap-3 px-6 py-4 bg-[#B3E2A7] text-white items-center text-lg rounded-lg">
                        <img src="../image/services_logo.png" alt="" class="w-20 h-20 object-cover">
                        <div class="flex text-2xl flex-col">
                            <p>4</p>
                            <p>Services</p>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</body>
</html>