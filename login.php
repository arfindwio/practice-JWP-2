<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include('config.php');

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $email = mysqli_real_escape_string($conn, $email);

    $sql = "SELECT * FROM tb_users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {

        $row = $result->fetch_assoc();
        
        if (password_verify($password, $row['password'])) {
    
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['role'] = $row['role'];

            header("Location: index.php");
            exit();
        } else {
    
            $errors['password'] = "Email atau password salah.";
        }
    } else {

        $errors['email'] = "Email atau password salah.";
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
        <div class="flex gap-5 flex-col p-5 rounded-md border shadow-lg w-[20%]">
            <h2 class="text-center font-bold text-2xl">LOGIN</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="flex flex-col gap-2">
                <div class="flex flex-col">
                    <label for="email" class="text-sm">Email:</label>
                    <input type="email" id="email" name="email" class="border rounded-md px-2" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <p class="text-red-500 text-xs"><?php echo htmlspecialchars($errors['email'], ENT_QUOTES); ?></p>
                    <?php endif; ?>
                </div>
                <div class="flex flex-col">
                    <label for="password" class="text-sm">Password:</label>
                    <input type="password" id="password" name="password" class="border rounded-md px-2" required>
                    <?php if (isset($errors['password'])): ?>
                        <p class="text-red-500 text-xs"><?php echo htmlspecialchars($errors['password'], ENT_QUOTES); ?></p>
                    <?php endif; ?>
                </div>
                <input type="submit" value="Login" class="border py-1 rounded-md text-white bg-blue-700">
                <p class="text-sm text-center">Silakan register <a href='register.php' class="text-blue-500">disini</a>.</p>
            </form>
        </div>
    </div>
</body>
</html>

