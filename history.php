<?php
session_start();
// Redirect ke halaman login jika pengguna belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include('./config.php');

// Query untuk mendapatkan data pesanan berdasarkan user_id
$user_id = $_SESSION['user_id'];
$sql = "SELECT o.*, s.package_name, s.price 
        FROM tb_order o
        JOIN tb_services s ON o.service_id = s.service_id
        WHERE o.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Periksa jika query berhasil dieksekusi
if (!$result) {
    echo "Error: " . $conn->error;
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="w-full flex items-center px-10 py-3 bg-white border-b shadow-md fixed z-10">
        <a href="index.php" class="font-bold text-3xl text-[#B3E2A7]">JeWePe</a>

         <!-- Tautan Logout jika pengguna sudah login -->
         <?php if (isset($_SESSION['user_id'])): ?>
            <div class="ml-auto flex gap-5">
                <a href="history.php" class="text-slate-400 hover:text-[#B3E2A7] font-semibold text-lg">History</a>
                <a href="logout.php" class="text-slate-400 hover:text-[#B3E2A7] font-semibold text-lg">Logout</a>
            </div>
        <?php else: ?>
        <!-- Tautan Login jika pengguna belum login -->
            <div class="ml-auto">
                <a href="login.php" class="text-slate-400 hover:text-[#B3E2A7] font-semibold text-lg">Login</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="mx-auto pt-20 px-10">
        <h2 class="text-2xl font-bold mb-4">Order History</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wedding Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            <?php
                $index = 0; // Initialize index outside the loop
                while ($row = $result->fetch_assoc()) {
                    $index++; // Increment index for each row
                    echo "<tr>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . $index . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['package_name']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>IDR " . number_format($row['price'], 0, ',', '.') . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . date('d F Y', strtotime($row["wedding_date"])) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap font-medium " . ($row['status'] === "requested" ? "text-red-500" : "text-green-500") . "'>" . htmlspecialchars($row['status']) . "</td>";

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php
// Tutup statement dan koneksi
$stmt->close();
$conn->close();
?>
