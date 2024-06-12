<?php
session_start();
include('config.php');

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

    // Query untuk memeriksa apakah email sudah terdaftar
    $sql_check_email = "SELECT * FROM tb_users WHERE email = '$email'";
    $result_check_email = $conn->query($sql_check_email);

    if ($result_check_email->num_rows > 0) {
        // Email sudah terdaftar
        echo "Email sudah terdaftar.";
    } else {
        // Email belum terdaftar, insert data ke database
        $sql_insert_user = "INSERT INTO tb_users (full_name, phone_number, email, password, created_at, updated_at) 
                            VALUES ('$full_name', '$phone_number', '$email', '$password', NOW(), NOW())";
        if ($conn->query($sql_insert_user) === TRUE) {
            // Pendaftaran berhasil
            echo "Register Berhasil";
        } else {
            // Error saat melakukan pendaftaran
            echo "Error: " . $sql_insert_user . "<br>" . $conn->error;
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
</head>
<body>
    <h2>Register</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="full_name">Full Name:</label><br>
        <input type="text" id="full_name" name="full_name" required><br>
        <label for="phone_number">Phone Number:</label><br>
        <input type="text" id="phone_number" name="phone_number" required><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br>
        <label for="confirm_password">Confirm Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        <input type="submit" value="Register">
    </form>
    <p>Silakan login <a href='login.php'>di sini</a>.</p>
</body>
</html>
