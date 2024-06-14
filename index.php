<?php
session_start();
include('./config.php');

// Periksa apakah ada pengiriman formulir POST untuk pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_id'], $_POST['wedding_date'])) {
    // Ambil data dari POST
    $service_id = $_POST['service_id'];
    $wedding_date = $_POST['wedding_date'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Pastikan service_id adalah integer
    $service_id = intval($service_id);

    // Insert ke dalam tb_order
    $sql = "INSERT INTO tb_order (service_id, user_id, wedding_date, created_at, updated_at) 
            VALUES (?, ?, ?, NOW(), NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $service_id, $user_id, $wedding_date);

    if ($stmt->execute()) {
        // Pesanan berhasil ditempatkan
        header("Location: index.php");
        exit();
    } else {
        // Ada kesalahan saat mengeksekusi query SQL
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

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
    <title>Home</title>
    <style>
        .truncate-2-lines {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    
    
    <div class="w-full flex items-center px-10 py-3 bg-transparent fixed z-10">
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

    <!-- Hero Section -->
    <div class="bg-[url('./image/imagebg.jpg')] h-screen bg-cover bg-center text-white grayscale-[85%] flex">
        <h1 class="m-auto font-bold text-6xl w-[50%] leading-relaxed text-center">Your Dream Wedding Made Perfect</h1>
    </div>

    <!-- About Us Section -->
    <div class="py-10 flex flex-col gap-7 w-[60%] mx-auto">
        <div class="flex flex-col text-center">

            <h2 class="font-medium text-lg text-[#80B9AD]">- About Us -</h2>
            <h1 class="font-bold text-4xl">Who We Are</h1>
        </div>
        <div class="flex items-center justify-between w-full">
            <img src="./image/imgAboutUs.jpg" alt="About Us" class="rounded-md w-fit">
            <div class="flex flex-col gap-2 w-[65%]">
                <h5 class="text-xl font-bold">The Essence of Wedding Organizers</h5>
                <p class="text-sm">A wedding organizer plays a crucial role in turning your dream wedding into a flawless reality. From meticulous planning to seamless execution, they ensure every detail, from venue selection to decor and scheduling, is tailored to perfection. Their expertise and dedication allow you to cherish every moment, knowing your special day is in capable hands. Trust a wedding organizer to transform your vision into an unforgettable celebration, leaving you free to savor every joyful moment with loved ones.</p>
            </div>
        </div>
    </div>
    
    <!-- Service Section -->
    <div class="py-10 flex flex-col gap-7 w-[60%] h-full mx-auto">
        <div class="flex flex-col text-center">
            <h2 class="font-medium text-lg text-[#80B9AD]">- Services -</h2>
            <h1 class="font-bold text-4xl">What We Offer</h1>
        </div>
        <div class="grid grid-cols-2 gap-4">
        <?php
            if ($result->num_rows > 0) {
                $index = 0; // Initialize index outside the loop
                while ($row = $result->fetch_assoc()) {
                    $description = $row['description'];
                    $short_description = mb_strimwidth($description, 0, 400, '...');
                    $order_link = isset($_SESSION['user_id']) ? "index.php" : "login.php";
                    ?>
                   <div class="border rounded-2xl shadow-sm flex gap-2 flex-col bg-slate-200 overflow-hidden p-4">
                        <img src='./image/<?php echo $row['image']; ?>' alt='<?php echo $row['package_name']; ?>'  class="object-cover w-full h-[13rem]  rounded-md">
                        <div class="border-2 flex flex-col flex-wrap h-fit gap-2 border-slate-100 p-2">
                            <h4 class="text-lg font-medium"><?php echo htmlspecialchars($row['package_name']); ?></h4>
                            <h3 class="text-xl font-bold text-[#6295A2]">IDR <?php echo number_format($row['price'], 0, ',', '.'); ?></h3>
                            <p class="text-sm truncate-2-lines text-justify text-wrap" id="short-description-<?php echo $index; ?>"><?php echo htmlspecialchars($short_description); ?></p>
                            <p class="text-sm hidden text-wrap" id="full-description-<?php echo $index; ?>"><?php echo htmlspecialchars($description); ?></p>
                            <button class="text-sm text-blue-500 hover:underline" onclick="toggleDescription(<?php echo $index; ?>)">Show More</button>
                        </div>
                        <form action="<?php echo $order_link; ?>" method="post" class="w-full mt-auto flex flex-col flex-wrap">
                            <input type="hidden" name="service_id" value="<?php echo $row['service_id']; ?>">
                            <label for="wedding_date">Wedding Date</label>
                            <input type="date" name="wedding_date" class="w-full py-2 px-4 bg-gray-100 rounded-lg">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button type="submit" class="w-full py-2 bg-[#80B9AD] rounded-lg text-white mt-2" onclick="return confirmOrder()">ORDER NOW</button>
                            <?php else: ?>
                                <a href="login.php" class="w-full py-2 bg-[#80B9AD] rounded-lg text-white mt-2 block text-center">LOGIN TO ORDER</a>
                            <?php endif; ?>
                        </form>
                    </div>

                    <?php
                    $index++;
                }
            }
        ?>
        </div>
    </div>

    <!-- Contact Us Section -->
    <div class="py-10 flex flex-col gap-7 w-[60%] min-h-full mx-auto">
        <div class="flex flex-col text-center">
            <h2 class="font-medium text-lg text-[#80B9AD]">- Contact Us -</h2>
            <h1 class="font-bold text-4xl">Our Contact Information</h1>
        </div>
        <div class="flex justify-around h-full w-full flex-wrap">
            <div class="flex flex-col gap-1 border p-4 bg-white shadow-md w-1/4 text-center justify-center items-center rounded-lg">
                <img src="./image/location-icon.png" alt="Location Icon" class="w-10 h-10 object-cover">
                <h5 class="text-lg font-bold">Our Location</h5>
                <p>123 Main Street, New York City, United States, 10001</p>
            </div>
            <div class="flex flex-col gap-1 border p-4 bg-white shadow-md w-1/4 text-center justify-center items-center rounded-lg">
                <img src="./image/phone-icon.png" alt="Phone Icon" class="w-10 h-10 object-cover">
                <h5 class="text-lg font-bold">Phone Number</h5>
                <p>+62 8123 4567 890</p>
            </div>
            <div class="flex flex-col gap-1 border p-4 bg-white shadow-md w-1/4 text-center justify-center items-center rounded-lg">
                <img src="./image/email-icon.png" alt="Email Icon" class="w-10 h-10 object-cover">
                <h5 class="text-lg font-bold">Email</h5>
                <p class="break-all">arfindwioctavianto@gmail.com</p>
            </div>
        </div>
    </div>
    
    <script>
    function toggleDescription(index) {
        const shortDescription = document.getElementById('short-description-' + index);
        const fullDescription = document.getElementById('full-description-' + index);
        const button = event.target;

        if (shortDescription.classList.contains('hidden')) {
            shortDescription.classList.remove('hidden');
            fullDescription.classList.add('hidden');
            button.textContent = 'Show More';
        } else {
            shortDescription.classList.add('hidden');
            fullDescription.classList.remove('hidden');
            button.textContent = 'Show Less';
        }
    }
</script>
</body>
</html>
