<?php
require "config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["fullname"] = $user["fullname"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["role"] = $user["role"];

        if ($user["role"] === "admin" || $user["role"] === "staff") {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Email hoặc mật khẩu không đúng!";
    }
}

include "includes/header.php";
?>

<section class="min-h-[80vh] flex items-center justify-center px-6 py-16">
    <div class="bg-white w-full max-w-md p-8 rounded-3xl shadow-xl">
        <h1 class="text-3xl font-extrabold text-center mb-2">Đăng nhập</h1>
        <p class="text-center text-gray-500 mb-8">
            Đăng nhập để mua hàng và quản lý tài khoản
        </p>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-5">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-5">
                <label class="font-semibold">Email</label>
                <input type="email" name="email" required
                       class="w-full border p-4 rounded-xl mt-2 focus:outline-none focus:ring-2 focus:ring-black"
                       placeholder="Nhập email">
            </div>

            <div class="mb-6">
                <label class="font-semibold">Mật khẩu</label>
                <input type="password" name="password" required
                       class="w-full border p-4 rounded-xl mt-2 focus:outline-none focus:ring-2 focus:ring-black"
                       placeholder="Nhập mật khẩu">
            </div>

            <button class="w-full bg-black text-white p-4 rounded-xl font-bold hover:bg-red-500">
                Đăng nhập
            </button>
        </form>

        <div class="mt-6 bg-gray-100 p-4 rounded-xl text-sm">
            <p class="font-bold mb-2">Tài khoản demo:</p>
            <p>Admin: <b>admin@gmail.com</b> / <b>123456</b></p>
            <p>Khách: <b>customer@gmail.com</b> / <b>123456</b></p>
        </div>

        <p class="text-center mt-6 text-sm">
            Chưa có tài khoản?
            <a href="register.php" class="text-red-500 font-bold">Đăng ký ngay</a>
        </p>
    </div>
</section>

<?php include "includes/footer.php"; ?>