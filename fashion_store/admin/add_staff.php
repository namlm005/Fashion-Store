<?php
require "../config/database.php";
require "../includes/auth.php";
requireAdmin();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$_POST["email"]]);

    if ($stmt->fetch()) {
        $error = "Email này đã tồn tại!";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO users(fullname, email, password, phone, role)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $_POST["fullname"],
            $_POST["email"],
            $_POST["password"],
            $_POST["phone"],
            $_POST["role"]
        ]);

        header("Location: staff.php");
        exit();
    }
}

include "_layout_start.php";
?>

<h1 class="text-4xl font-extrabold mb-8">Thêm nhân viên</h1>

<?php if ($error): ?>
    <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-6">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<form method="POST" class="bg-white p-8 rounded-3xl shadow-xl max-w-3xl">
    <input name="fullname" required placeholder="Họ và tên"
           class="w-full border p-4 rounded-xl mb-4">

    <input type="email" name="email" required placeholder="Email"
           class="w-full border p-4 rounded-xl mb-4">

    <input type="password" name="password" required placeholder="Mật khẩu"
           class="w-full border p-4 rounded-xl mb-4">

    <input name="phone" placeholder="Số điện thoại"
           class="w-full border p-4 rounded-xl mb-4">

    <select name="role" class="w-full border p-4 rounded-xl mb-4">
        <option value="staff">Nhân viên</option>
        <option value="admin">Quản trị viên</option>
    </select>

    <button class="bg-black text-white px-8 py-3 rounded-xl hover:bg-red-500">
        Thêm nhân viên
    </button>
</form>

<?php include "_layout_end.php"; ?>