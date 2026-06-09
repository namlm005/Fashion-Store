<?php
require "config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "includes/header.php";

$userId = $_SESSION["user_id"] ?? null;
$email = $_SESSION["email"] ?? "";

$orders = [];
$contacts = [];

if ($userId) {
    $stmt = $conn->prepare("
        SELECT * FROM orders
        WHERE user_id = ?
        ORDER BY id DESC
    ");
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (!empty($email)) {
    $stmt = $conn->prepare("
        SELECT * FROM contacts
        WHERE email = ?
        ORDER BY id DESC
    ");
    $stmt->execute([$email]);
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function statusText($status) {
    if ($status === "pending") return "Chờ xác nhận";
    if ($status === "confirmed") return "Đã xác nhận";
    if ($status === "shipping") return "Đang giao";
    if ($status === "completed") return "Đã giao";
    if ($status === "cancelled") return "Đã hủy";
    if ($status === "new") return "Chưa phản hồi";
    if ($status === "replied") return "Đã phản hồi";
    return $status;
}
?>

<section class="max-w-6xl mx-auto px-6 py-16">
    <h1 class="text-4xl font-extrabold mb-10">Thông báo của tôi</h1>

    <div class="grid md:grid-cols-2 gap-8">

        <div class="bg-white rounded-3xl shadow-xl p-8">
            <h2 class="text-2xl font-bold mb-6">Trạng thái đơn hàng</h2>

            <?php if (empty($orders)): ?>
                <p class="text-gray-500">Bạn chưa có đơn hàng nào.</p>
            <?php endif; ?>

            <?php foreach ($orders as $o): ?>
                <div class="border rounded-2xl p-5 mb-4">
                    <p class="font-bold">Đơn hàng #<?= $o["id"] ?></p>
                    <p class="text-sm text-gray-500">
                        Thanh toán: <?= strtoupper($o["payment_method"]) ?>
                    </p>

                    <p class="font-bold text-red-500 mt-2">
                        <?= number_format($o["total_price"], 0, ',', '.') ?>đ
                    </p>

                    <p class="font-semibold">
                        <?= statusText($o["status"]) ?>
                    </p>

                    <a href="order-detail.php?id=<?= $o["id"] ?>"
                       class="inline-block mt-4 bg-black text-white px-5 py-2 rounded-xl hover:bg-red-500">
                        Xem chi tiết đơn hàng
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8">
            <h2 class="text-2xl font-bold mb-6">Phản hồi liên hệ từ Admin</h2>

            <?php if (empty($contacts)): ?>
                <p class="text-gray-500">Bạn chưa gửi liên hệ nào.</p>
            <?php endif; ?>

            <?php foreach ($contacts as $c): ?>
                <div class="border rounded-2xl p-5 mb-4">
                    <p class="font-bold">
                        <?= htmlspecialchars($c["subject"] ?? "Không có tiêu đề") ?>
                    </p>

                    <p class="text-sm text-gray-500 mt-2">
                        Nội dung bạn gửi:
                    </p>

                    <p><?= nl2br(htmlspecialchars($c["message"] ?? "")) ?></p>

                    <hr class="my-4">

                    <?php if (!empty($c["reply"])): ?>
                        <p class="font-bold text-green-600">Admin đã phản hồi:</p>
                        <p><?= nl2br(htmlspecialchars($c["reply"])) ?></p>
                    <?php else: ?>
                        <p class="text-yellow-600 font-semibold">
                            Admin chưa phản hồi.
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<?php include "includes/footer.php"; ?>