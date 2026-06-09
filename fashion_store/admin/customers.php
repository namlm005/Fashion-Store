<?php
require "../config/database.php";
require "../includes/auth.php";
requireStaffOrAdmin();

$stmt = $conn->query("
    SELECT 
        users.id,
        users.fullname,
        users.email,
        users.phone,
        users.role,
        users.last_login,
        COUNT(orders.id) AS total_orders,
        COALESCE(SUM(orders.total_price), 0) AS total_spent
    FROM users
    LEFT JOIN orders ON users.id = orders.user_id
    WHERE users.role = 'customer'
    GROUP BY users.id
    ORDER BY users.id DESC
");

$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "_layout_start.php";
?>

<h1 class="text-4xl font-extrabold mb-8">Quản lý khách hàng</h1>

<div class="bg-white rounded-3xl shadow-xl overflow-hidden">
    <table class="w-full text-center">
        <tr class="bg-black text-white">
            <th class="p-4">ID</th>
            <th class="p-4">Khách hàng</th>
            <th class="p-4">Email</th>
            <th class="p-4">SĐT</th>
            <th class="p-4">Số đơn</th>
            <th class="p-4">Tổng mua</th>
            <th class="p-4">Chi tiết</th>
        </tr>

        <?php foreach ($customers as $c): ?>
            <tr class="border-b">
                <td class="p-4">#<?= $c["id"] ?></td>

                <td class="p-4 font-bold">
                    <?= htmlspecialchars($c["fullname"]) ?>
                </td>

                <td class="p-4">
                    <?= htmlspecialchars($c["email"]) ?>
                </td>

                <td class="p-4">
                    <?= htmlspecialchars($c["phone"] ?? "Chưa có") ?>
                </td>

                <td class="p-4">
                    <?= $c["total_orders"] ?>
                </td>

                <td class="p-4 text-red-500 font-bold">
                    <?= number_format($c["total_spent"], 0, ',', '.') ?>đ
                </td>

                <td class="p-4">
                    <a href="customer-detail.php?id=<?= $c["id"] ?>"
                       class="bg-black text-white px-4 py-2 rounded-xl hover:bg-red-500">
                        Xem
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include "_layout_end.php"; ?>