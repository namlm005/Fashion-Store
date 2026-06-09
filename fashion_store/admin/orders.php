<?php
require "../config/database.php";
require "../includes/auth.php";
requireStaffOrAdmin();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->execute([
        $_POST["status"],
        $_POST["order_id"]
    ]);

    header("Location: orders.php");
    exit();
}

$stmt = $conn->query("
    SELECT orders.*, users.email 
    FROM orders 
    LEFT JOIN users ON orders.user_id = users.id
    ORDER BY orders.id DESC
");

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "_layout_start.php";
?>

<h1 class="text-4xl font-extrabold mb-8">Quản lý đơn hàng</h1>

<div class="bg-white rounded-3xl shadow-xl overflow-hidden">
    <table class="w-full text-center">
        <tr class="bg-black text-white">
            <th class="p-4">ID</th>
            <th class="p-4">Người nhận</th>
            <th class="p-4">SĐT</th>
            <th class="p-4">Địa chỉ</th>
            <th class="p-4">Tổng tiền</th>
            <th class="p-4">Thanh toán</th>
            <th class="p-4">Trạng thái</th>
            <th class="p-4">Lưu</th>
        </tr>

        <?php foreach ($orders as $o): ?>
            <tr class="border-b">
                <td class="p-4">#<?= $o['id'] ?></td>

                <td class="p-4">
                    <b><?= htmlspecialchars($o['fullname']) ?></b><br>
                    <span class="text-sm text-gray-500">
                        <?= htmlspecialchars($o['email'] ?? 'Khách') ?>
                    </span>
                </td>

                <td class="p-4"><?= htmlspecialchars($o['phone']) ?></td>
                <td class="p-4"><?= htmlspecialchars($o['address']) ?></td>

                <td class="p-4 text-red-500 font-bold">
                    <?= number_format($o['total_price'], 0, ',', '.') ?>đ
                </td>

                <td class="p-4"><?= strtoupper($o['payment_method']) ?></td>

                <td class="p-4">
                    <!-- FORM ĐÚNG -->
                    <form method="POST" class="flex gap-2 justify-center">
                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">

                        <select name="status" class="border p-2 rounded-xl">
                            <option value="pending" <?= $o['status']=='pending'?'selected':'' ?>>Chờ xác nhận</option>
                            <option value="confirmed" <?= $o['status']=='confirmed'?'selected':'' ?>>Đã xác nhận</option>
                            <option value="shipping" <?= $o['status']=='shipping'?'selected':'' ?>>Đang giao</option>
                            <option value="completed" <?= $o['status']=='completed'?'selected':'' ?>>Đã giao</option>
                            <option value="cancelled" <?= $o['status']=='cancelled'?'selected':'' ?>>Đã hủy</option>
                        </select>
                </td>

                <td class="p-4">
                        <button class="bg-black text-white px-4 py-2 rounded-xl hover:bg-red-500">
                            Lưu
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include "_layout_end.php"; ?>