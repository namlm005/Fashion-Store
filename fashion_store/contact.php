<?php
require "config/database.php";
include "includes/header.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("
        INSERT INTO contacts(fullname, email, phone, subject, message)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST["fullname"],
        $_POST["email"],
        $_POST["phone"],
        $_POST["subject"],
        $_POST["message"]
    ]);

    $message = "Gửi liên hệ thành công!";
}
?>

<section class="max-w-6xl mx-auto px-6 py-16">
    <h1 class="text-4xl font-extrabold text-center mb-10">Liên hệ</h1>

    <?php if ($message): ?>
        <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-6">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="grid md:grid-cols-2 gap-8">
        <div class="bg-white p-8 rounded-3xl shadow-xl">
            <h2 class="text-3xl font-bold mb-6">Thông tin liên hệ</h2>
            <p>Email: support@fashionstore.vn</p>
            <p class="mt-3">Hotline: 0123 456 789</p>
            <p class="mt-3">Địa chỉ: Hà Nội, Việt Nam</p>
        </div>

        <form method="POST" class="bg-white p-8 rounded-3xl shadow-xl">
            <input name="fullname" required placeholder="Họ tên"
                   class="w-full border p-4 rounded-xl mb-4">

            <input type="email" name="email" required placeholder="Email"
                   class="w-full border p-4 rounded-xl mb-4">

            <input name="phone" placeholder="Số điện thoại"
                   class="w-full border p-4 rounded-xl mb-4">

            <input name="subject" placeholder="Tiêu đề"
                   class="w-full border p-4 rounded-xl mb-4">

            <textarea name="message" required placeholder="Nội dung"
                      class="w-full border p-4 rounded-xl mb-4"></textarea>

            <button class="w-full bg-black text-white p-4 rounded-xl hover:bg-red-500">
                Gửi liên hệ
            </button>
        </form>
    </div>
</section>

<?php include "includes/footer.php"; ?>