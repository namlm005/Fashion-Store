<?php
require "config/database.php";
include "includes/header.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $phone = $_POST["phone"];

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        $message = "Email đã tồn tại!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users(fullname,email,password,phone,role) VALUES(?,?,?,?, 'customer')");
        $stmt->execute([$fullname, $email, $password, $phone]);
        header("Location: login.php");
        exit();
    }
}
?>

<section class="max-w-md mx-auto bg-white p-8 rounded-3xl shadow-xl mt-12">
    <h1 class="text-3xl font-bold text-center mb-6">Đăng ký</h1>

    <?php if ($message): ?>
        <p class="bg-red-100 text-red-600 p-3 rounded-xl mb-4"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <input name="fullname" required placeholder="Họ và tên" class="w-full border p-4 rounded-xl">
        <input type="email" name="email" required placeholder="Email" class="w-full border p-4 rounded-xl">
        <input type="password" name="password" required placeholder="Mật khẩu" class="w-full border p-4 rounded-xl">
        <input name="phone" placeholder="Số điện thoại" class="w-full border p-4 rounded-xl">

        <button class="w-full bg-black text-white p-4 rounded-xl hover:bg-red-500">
            Đăng ký
        </button>
    </form>
</section>

<?php include "includes/footer.php"; ?>