<?php
session_start();
include('config.php');

$errors = []; // Array untuk menyimpan pesan kesalahan

// Periksa apakah pengguna sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Validasi phone_number hanya angka
    if (!ctype_digit($phone_number)) {
        $errors['phone_number'] = "Nomor telepon hanya boleh berisi angka.";
    }

    if ($password !== $confirm_password) {
        $errors['password'] = "Password dan konfirmasi password tidak cocok.";
    }

    // Validasi email
    $sql_check_email = "SELECT * FROM tb_users WHERE email = '$email'";
    $result_check_email = $conn->query($sql_check_email);

    if ($result_check_email->num_rows > 0) {
        $errors['email'] = "Email sudah terdaftar.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql_insert_user = "INSERT INTO tb_users (full_name, phone_number, email, password, created_at, updated_at) 
                            VALUES ('$full_name', '$phone_number', '$email', '$hashed_password', NOW(), NOW())";
        if ($conn->query($sql_insert_user) === TRUE) {
            header("Location: login.php");
            exit();
        } else {
            $errors['db'] = "Error: " . $sql_insert_user . " " . $conn->error;
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
    <div class="flex gap-5 flex-col p-5 rounded-md border shadow-lg w-[35%]">
        <h2 class="text-center font-bold text-2xl">REGISTER</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="flex flex-col gap-2">
            <div class="flex flex-col">
                <label class="text-sm" for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" class="border rounded-md px-2" value="<?php echo htmlspecialchars($_POST['full_name'] ?? '', ENT_QUOTES); ?>" required>
            </div>

            <label class="text-sm" for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" class="border rounded-md px-2" value="<?php echo htmlspecialchars($_POST['phone_number'] ?? '', ENT_QUOTES); ?>" required pattern="\d+" title="Hanya angka yang diperbolehkan">
            <?php if (isset($errors['phone_number'])): ?>
                <p class="text-red-500 text-xs"><?php echo htmlspecialchars($errors['phone_number'], ENT_QUOTES); ?></p>
            <?php endif; ?>

            <div class="flex flex-col">
                <label class="text-sm" for="email">Email:</label>
                <input type="email" id="email" name="email" class="border rounded-md px-2" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <p class="text-red-500 text-xs"><?php echo htmlspecialchars($errors['email'], ENT_QUOTES); ?></p>
                <?php endif; ?>
            </div>

            <div class="flex flex-col">
                <label class="text-sm" for="password">Password:</label>
                <input type="password" id="password" name="password" class="border rounded-md px-2" required>
            </div>

            <div class="flex flex-col">
                <label class="text-sm" for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" class="border rounded-md px-2" required>
                <?php if (isset($errors['password'])): ?>
                    <p class="text-red-500 text-xs"><?php echo htmlspecialchars($errors['password'], ENT_QUOTES); ?></p>
                <?php endif; ?>
            </div>

            <input type="submit" value="Register" class="border py-1 rounded-md text-white bg-blue-700">

            <p class="text-sm text-center">Silakan login <a href='login.php' class="text-blue-500">di sini</a>.</p>
        </form>
    </div>
</div>
</body>
</html>

