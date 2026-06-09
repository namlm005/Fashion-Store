<?php
require "config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$orderId = $_GET["id"] ?? 0;

$stmt = $conn->prepare("
    SELECT * FROM orders
    WHERE id = ? AND user_id = ?
");
$stmt->execute([$orderId, $_SESSION["user_id"]]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

include "includes/header.php";

if (!$order) {
    echo "<section class='max-w-5xl mx-auto px-6 py-20'>
            <div class='bg-white rounded-3xl shadow-xl p-10 text-center'>
                <h1 class='text-3xl font-bold mb-4'>Không tìm thấy đơn hàng</h1>
                <a href='index.php' class='bg-black text-white px-6 py-3 rounded-xl'>Quay về trang chủ</a>
            </div>
          </section>";
    include "includes/footer.php";
    exit();
}

$stmtItems = $conn->prepare("
    SELECT order_items.*, products.image_url
    FROM order_items
    LEFT JOIN products ON order_items.product_id = products.id
    WHERE order_items.order_id = ?
");
$stmtItems->execute([$orderId]);
$items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

function statusTextOrder($status) {
    if ($status === "pending") return "Chờ xác nhận";
    if ($status === "confirmed") return "Đã xác nhận";
    if ($status === "shipping") return "Đang giao hàng";
    if ($status === "completed") return "Đã giao thành công";
    if ($status === "cancelled") return "Đã hủy";
    return $status;
}
?>

<section class="max-w-6xl mx-auto px-6 py-12">
    <h1 class="text-4xl font-extrabold mb-8">Chi tiết đơn mua</h1>

    <div class="bg-white rounded-3xl shadow-xl p-8 mb-8">
        <div class="flex justify-between border-b pb-6 mb-6">
            <div>
                <h2 class="text-2xl font-bold">Đơn hàng #<?= $order["id"] ?></h2>
                <p class="text-gray-500 mt-2">Người nhận: <?= htmlspecialchars($order["fullname"]) ?></p>
                <p class="text-gray-500">SĐT: <?= htmlspecialchars($order["phone"]) ?></p>
                <p class="text-gray-500">Địa chỉ: <?= htmlspecialchars($order["address"]) ?></p>
            </div>

            <div class="text-right">
                <p class="text-red-500 text-xl font-bold">
                    <?= statusTextOrder($order["status"]) ?>
                </p>
                <p class="text-gray-500 mt-2">
                    Thanh toán: <?= strtoupper($order["payment_method"]) ?>
                </p>
            </div>
        </div>

        <?php foreach ($items as $item): ?>
            <div class="flex items-center gap-5 border-b py-4">
                <img src="<?= htmlspecialchars($item["image_url"] ?? "") ?>"
                     class="w-24 h-24 object-cover rounded-xl border">

                <div class="flex-1">
                    <h3 class="font-bold text-lg">
                        <?= htmlspecialchars($item["product_name"]) ?>
                    </h3>
                    <p class="text-gray-500">Số lượng: <?= $item["quantity"] ?></p>
                    <p class="text-gray-500">
                        Đơn giá: <?= number_format($item["price"], 0, ',', '.') ?>đ
                    </p>
                </div>

                <p class="text-red-500 font-bold">
                    <?= number_format($item["subtotal"], 0, ',', '.') ?>đ
                </p>
            </div>
        <?php endforeach; ?>

        <div class="flex justify-end mt-6">
            <div class="text-right">
                <p class="text-gray-500">Tổng tiền</p>
                <p class="text-3xl text-red-500 font-extrabold">
                    <?= number_format($order["total_price"], 0, ',', '.') ?>đ
                </p>
            </div>
        </div>
    </div>

    <a href="index.php"
       class="bg-black text-white px-6 py-3 rounded-xl hover:bg-red-500">
        Quay về trang chủ
    </a>
</section>

<?php include "includes/footer.php"; ?>