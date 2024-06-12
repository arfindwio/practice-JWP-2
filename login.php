<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan nilai yang dikirimkan melalui form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Melindungi dari SQL Injection
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Query untuk memeriksa apakah email dan password cocok
    $sql = "SELECT * FROM tb_users WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Login berhasil, simpan user_id dalam session
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id'];
        
        echo "Login Berhasil";

        // // Redirect ke halaman selanjutnya (misalnya dashboard)
        // header("Location: dashboard.php");
        exit();
    } else {
        // Login gagal
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
</head>
<body>
    <h2>Login</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
