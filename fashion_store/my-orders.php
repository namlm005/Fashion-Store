<?php
require "config/database.php";
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT * FROM orders
    WHERE user_id = ?
    ORDER BY id DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

function statusText($s) {
    return [
        "pending" => "Chờ xác nhận",
        "confirmed" => "Đã xác nhận",
        "shipping" => "Đang giao",
        "completed" => "Đã giao",
        "cancelled" => "Đã hủy"
    ][$s] ?? $s;
}

include "includes/header.php";
?>

<section class="max-w-6xl mx-auto px-6 py-14">
    <h1 class="text-4xl font-extrabold mb-8">Đơn mua của tôi</h1>

    <?php if (empty($orders)): ?>
        <div class="bg-white p-10 rounded-3xl shadow text-center">
            <p class="text-gray-500">Bạn chưa có đơn hàng nào.</p>
        </div>
    <?php endif; ?>

    <div class="space-y-6">
        <?php foreach ($orders as $o): ?>
            <?php
            $stmtItem = $conn->prepare("
             SELECT order_items.*, 
             products.image_url AS image
             FROM order_items
             LEFT JOIN products ON order_items.product_id = products.id
             WHERE order_items.order_id = ?
             LIMIT 1
            ");
            $stmtItem->execute([$o["id"]]);
            $item = $stmtItem->fetch(PDO::FETCH_ASSOC);
            ?>

            <div class="bg-white rounded-3xl shadow-xl p-6 flex items-center justify-between gap-6 hover:shadow-2xl transition">

                <div class="flex items-center gap-5">
                    <img src="<?= htmlspecialchars($item["image"] ?? "") ?>"
                         class="w-28 h-28 object-cover rounded-2xl border bg-gray-100"
                         onerror="this.src='https://via.placeholder.com/120?text=No+Image'">

                    <div>
                        <h2 class="text-xl font-bold">Đơn hàng #<?= $o["id"] ?></h2>
                        <p class="font-semibold mt-1">
                            <?= htmlspecialchars($item["product_name"] ?? "Sản phẩm") ?>
                        </p>
                        <p class="text-gray-500 text-sm">
                            Số lượng: <?= intval($item["quantity"] ?? 0) ?>
                        </p>
                        <p class="text-gray-500 text-sm">
                            Thanh toán: <?= strtoupper($o["payment_method"]) ?>
                        </p>
                    </div>
                </div>

                <div class="text-right">
                    <p class="text-red-500 text-xl font-extrabold">
                        <?= number_format($o["total_price"], 0, ',', '.') ?>đ
                    </p>

                    <p class="font-bold mt-1">
                        <?= statusText($o["status"]) ?>
                    </p>

                    <a href="order-detail.php?id=<?= $o["id"] ?>"
                       class="inline-block mt-4 bg-black text-white px-5 py-2 rounded-xl hover:bg-red-500">
                        Xem chi tiết
                    </a>

                    <?php if ($o["status"] === "cancelled" || $o["status"] === "completed"): ?>
                        <a href="delete-order.php?id=<?= $o["id"] ?>"
                           onclick="return confirm('Bạn có chắc muốn xóa đơn này không?')"
                           class="inline-block mt-3 bg-red-500 text-white px-5 py-2 rounded-xl hover:bg-red-600">
                            Xóa đơn
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include "includes/footer.php"; ?>