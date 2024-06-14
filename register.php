<?php
session_start();
include('config.php');

// Periksa apakah pengguna sudah login
if (isset($_SESSION['user_id'])) {
    // Jika sudah, redirect ke halaman home atau halaman lain yang sesuai
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan nilai yang dikirimkan melalui form
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Melindungi dari SQL Injection
    $full_name = mysqli_real_escape_string($conn, $full_name);
    $phone_number = mysqli_real_escape_string($conn, $phone_number);
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);
    $confirm_password = mysqli_real_escape_string($conn, $confirm_password);

    // Periksa apakah password dan konfirmasi password sesuai
    if ($password !== $confirm_password) {
        echo "Password dan konfirmasi password tidak cocok.";
        exit();
    }

    // Enkripsi password sebelum menyimpannya ke dalam database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Query untuk memeriksa apakah email sudah terdaftar
    $sql_check_email = "SELECT * FROM tb_users WHERE email = '$email'";
    $result_check_email = $conn->query($sql_check_email);

    if ($result_check_email->num_rows > 0) {
        // Email sudah terdaftar
        echo "Email sudah terdaftar.";
    } else {
        // Email belum terdaftar, insert data ke database
        $sql_insert_user = "INSERT INTO tb_users (full_name, phone_number, email, password, created_at, updated_at) 
                            VALUES ('$full_name', '$phone_number', '$email', '$hashed_password', NOW(), NOW())";
        if ($conn->query($sql_insert_user) === TRUE) {
            // Pendaftaran berhasil
            echo "Register Berhasil";

            header("Location: login.php");
            exit();
        } else {
            // Error saat melakukan pendaftaran
            echo "Error: " . $sql_insert_user . "" . $conn->error;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div class="flex justify-center items-center h-screen">
        <div class= "flex gap-5 flex-col p-5 rounded-md border shadow-lg w-[35%]"> 
        <h2 class="text-center font-bold text-2xl">REGISTER</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="flex flex-col gap-2">
        <div class="flex flex-col">
            <label class="text-sm" for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" class="border rounded-md" required>
        </div>
        <label class="text-sm" for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" class="border rounded-md" required>
        <div class="flex flex-col">
        <label class="text-sm" for="email">Email:</label>
        <input type="email" id="email" name="email" class="border rounded-md" required>
        </div>
        <div class="flex flex-col">
        <label class="text-sm" for="password">Password:</label>
        <input type="password" id="password" name="password" class="border rounded-md" required>
     </div>
        <div class="flex flex-col">
        <label class="text-sm" for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" class="border rounded-md" required>
     </div>
        <input type="submit" value="Register"  class="border py-1 rounded-md text-white bg-blue-700">
        <p class="text-sm text-center">Silakan login <a href='login.php' class="text-blue-500">di sini</a>.</p>
    </form>
    </div>
    </div>
</body>
</html>
