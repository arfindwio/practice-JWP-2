<?php
session_start();

// Periksa apakah pengguna sudah login
if (isset($_SESSION['user_id'])) {
    // Jika sudah, redirect ke halaman home atau halaman lain yang sesuai
    header("Location: index.php");
    exit();
}

include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan nilai yang dikirimkan melalui form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Melindungi dari SQL Injection
    $email = mysqli_real_escape_string($conn, $email);

    // Query untuk mendapatkan password dan role yang sesuai dengan email yang diberikan
    $sql = "SELECT * FROM tb_users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Mendapatkan baris hasil query
        $row = $result->fetch_assoc();
        
        // Verifikasi password
        if (password_verify($password, $row['password'])) {
            // Login berhasil, simpan user_id dan role dalam session
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['role'] = $row['role'];  // Simpan peran pengguna dalam sesi
            echo "Login Berhasil";

            // Redirect ke halaman selanjutnya
            header("Location: index.php");
            exit();
        } else {
            // Login gagal karena password salah
            echo "Email atau password salah.";
        }
    } else {
        // Login gagal karena email tidak ditemukan
        echo "Email atau password salah.";
    }

    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="flex justify-center items-center h-screen">
        <div class= "flex gap-5 flex-col p-5 rounded-md border shadow-lg w-[20%]">           
            <h2 class="text-center font-bold text-2xl">LOGIN</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="flex flex-col gap-2">
                <div class="flex flex-col">
                    <label for="email" class="text-sm">Email:</label>
                    <input type="email" id="email" name="email" class="border rounded-md" required>
                </div>
                <div class="flex flex-col">
                    <label for="password" class="text-sm">Password:</label>
                    <input type="password" id="password" name="password" class="border rounded-md" required>
                </div>
                <input type="submit" value="Login" class="border py-1 rounded-md text-white bg-blue-700">
                <p class="text-sm text-center">Silakan register <a href='register.php' class="text-blue-500">disini</a>.</p>
            </form>
        </div>
    </div>
</body>
</html>
