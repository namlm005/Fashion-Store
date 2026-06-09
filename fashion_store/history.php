<?php
session_start();
require "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["user_id"];

// XÓA TOÀN BỘ LỊCH SỬ
if (isset($_GET["delete_all"])) {
    $stmt = $conn->prepare("
        UPDATE orders 
        SET history_deleted = 1 
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);

    header("Location: history.php");
    exit();
}

// XÓA 1 ĐƠN KHỎI LỊCH SỬ
if (isset($_GET["delete"])) {
    $orderId = $_GET["delete"];

    $stmt = $conn->prepare("
        UPDATE orders 
        SET history_deleted = 1 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$orderId, $userId]);

    header("Location: history.php");
    exit();
}

// MUA LẠI
if (isset($_GET["rebuy"])) {
    $orderId = $_GET["rebuy"];

    $stmt = $conn->prepare("
        SELECT oi.*, p.image_url
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        JOIN orders o ON oi.order_id = o.id
        WHERE oi.order_id = ? AND o.user_id = ?
    ");
    $stmt->execute([$orderId, $userId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $_SESSION["cart"] = [];

    foreach ($items as $item) {
        $_SESSION["cart"][] = [
            "id" => $item["product_id"],
            "name" => $item["product_name"],
            "price" => $item["price"],
            "quantity" => $item["quantity"],
            "image_url" => $item["image_url"] ?? ""
        ];
    }

    header("Location: checkout.php");
    exit();
}

$stmt = $conn->prepare("
    SELECT * FROM orders
    WHERE user_id = ?
    AND status = 'completed'
    AND (history_deleted IS NULL OR history_deleted = 0)
    ORDER BY created_at DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "includes/header.php";
?>

<section class="max-w-6xl mx-auto px-6 py-16">

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-extrabold">Lịch sử mua hàng</h1>

        <?php if (!empty($orders)): ?>
            <a href="history.php?delete_all=1"
               onclick="return confirm('Bạn có chắc muốn xóa toàn bộ lịch sử mua hàng không?')"
               class="bg-red-500 text-white px-5 py-3 rounded-xl hover:bg-red-600">
                Xóa lịch sử 
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($orders)): ?>
        <div class="bg-white p-10 rounded-2xl shadow text-center text-gray-500">
            Lịch sửa mua hàng trống.
        </div>
    <?php endif; ?>

    <div class="space-y-6">
        <?php foreach ($orders as $o): ?>
            <?php
            $stmtItems = $conn->prepare("
                SELECT oi.*, p.image_url
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $stmtItems->execute([$o["id"]]);
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <div class="bg-white p-6 rounded-2xl shadow">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 class="text-xl font-bold">Đơn hàng #<?= $o["id"] ?></h2>
                        <p class="text-gray-500">Ngày mua: <?= $o["created_at"] ?></p>
                    </div>

                    <div class="text-right">
                        <p class="text-red-500 font-bold text-xl">
                            <?= number_format($o["total_price"], 0, ',', '.') ?>đ
                        </p>
                        <p class="font-semibold"><?= htmlspecialchars($o["status"]) ?></p>
                    </div>
                </div>

                <?php foreach ($items as $item): ?>
                    <div class="flex items-center gap-4 border-t py-4">
                        <img src="<?= htmlspecialchars($item["image_url"] ?? "assets/images/default.png") ?>"
                             class="w-20 h-20 object-cover rounded-xl border"
                             onerror="this.src='assets/images/default.png'">

                        <div class="flex-1">
                            <p class="font-bold"><?= htmlspecialchars($item["product_name"]) ?></p>
                            <p class="text-gray-500">Số lượng: <?= $item["quantity"] ?></p>
                        </div>

                        <p class="text-red-500 font-bold">
                            <?= number_format($item["subtotal"], 0, ',', '.') ?>đ
                        </p>
                    </div>
                <?php endforeach; ?>

                <div class="flex justify-end gap-3 mt-4">
                    <a href="history.php?rebuy=<?= $o["id"] ?>"
                       class="bg-black text-white px-6 py-3 rounded-xl hover:bg-red-500">
                        Mua lại
                    </a>

                    <a href="history.php?delete=<?= $o["id"] ?>"
                       onclick="return confirm('Bạn có chắc muốn xóa đơn này khỏi lịch sử không?')"
                       class="bg-red-500 text-white px-6 py-3 rounded-xl hover:bg-red-600">
                        Xóa
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include "includes/footer.php"; ?>