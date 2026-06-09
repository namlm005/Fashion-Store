<?php
require "../config/database.php";
require "../includes/auth.php";
requireStaffOrAdmin();

$customerId = $_GET["id"] ?? 0;

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$customerId]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    include "_layout_start.php";
    echo "<h1 class='text-3xl font-bold'>Không tìm thấy khách hàng</h1>";
    include "_layout_end.php";
    exit();
}

$stmtOrders = $conn->prepare("
    SELECT * FROM orders 
    WHERE user_id = ?
    ORDER BY id DESC
");
$stmtOrders->execute([$customerId]);
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

include "_layout_start.php";
?>

<h1 class="text-4xl font-extrabold mb-6">Chi tiết khách hàng</h1>

<div class="bg-white rounded-3xl shadow-xl p-8 mb-8">
    <h2 class="text-2xl font-bold mb-3">
        <?= htmlspecialchars($customer["fullname"]) ?>
    </h2>

    <p>Email: <b><?= htmlspecialchars($customer["email"]) ?></b></p>
    <p>SĐT: <b><?= htmlspecialchars($customer["phone"] ?? "Chưa có") ?></b></p>
    <p>Vai trò: <b><?= htmlspecialchars($customer["role"]) ?></b></p>
</div>

<?php foreach ($orders as $order): ?>
    <div class="bg-white rounded-3xl shadow-xl p-6 mb-6">
        <div class="flex justify-between border-b pb-4 mb-4">
            <div>
                <h3 class="text-xl font-bold">Đơn hàng #<?= $order["id"] ?></h3>
                <p class="text-gray-500">
                    Địa chỉ: <?= htmlspecialchars($order["address"]) ?>
                </p>
            </div>

            <div class="text-right">
                <p class="text-red-500 font-bold">
                    <?= number_format($order["total_price"], 0, ',', '.') ?>đ
                </p>
                <p class="text-gray-500"><?= htmlspecialchars($order["status"]) ?></p>
            </div>
        </div>

        <?php
        $stmtItems = $conn->prepare("
            SELECT order_items.*, products.image_url
            FROM order_items
            LEFT JOIN products ON order_items.product_id = products.id
            WHERE order_items.order_id = ?
        ");
        $stmtItems->execute([$order["id"]]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php foreach ($items as $item): ?>
            <div class="flex items-center gap-4 border-b py-3">
                <img src="<?= htmlspecialchars($item["image_url"] ?? "") ?>"
                     class="w-20 h-20 rounded-xl object-cover border">

                <div class="flex-1">
                    <p class="font-bold">
                        <?= htmlspecialchars($item["product_name"]) ?>
                    </p>
                    <p class="text-gray-500">
                        Số lượng: <?= $item["quantity"] ?>
                    </p>
                </div>

                <p class="text-red-500 font-bold">
                    <?= number_format($item["subtotal"], 0, ',', '.') ?>đ
                </p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<?php include "_layout_end.php"; ?>