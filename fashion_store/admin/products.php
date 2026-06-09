<?php
require "../config/database.php";
require "../includes/auth.php";
requireStaffOrAdmin();

$stmt = $conn->query("
    SELECT products.*, categories.name AS category_name
    FROM products
    LEFT JOIN categories ON products.category_id = categories.id
    ORDER BY products.id DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$todayRevenue = $conn->query("
    SELECT COALESCE(SUM(total_price), 0)
    FROM orders
    WHERE DATE(created_at) = CURDATE()
")->fetchColumn();

$monthRevenue = $conn->query("
    SELECT COALESCE(SUM(total_price), 0)
    FROM orders
    WHERE MONTH(created_at) = MONTH(CURDATE())
    AND YEAR(created_at) = YEAR(CURDATE())
")->fetchColumn();

$yearRevenue = $conn->query("
    SELECT COALESCE(SUM(total_price), 0)
    FROM orders
    WHERE YEAR(created_at) = YEAR(CURDATE())
")->fetchColumn();

function productImageAdmin($imageUrl) {
    $imageUrl = trim($imageUrl ?? "");

    if ($imageUrl === "") {
        return "../assets/images/default.png";
    }

    if (preg_match('/^https?:\/\//i', $imageUrl)) {
        return $imageUrl;
    }

    if (str_starts_with($imageUrl, "../")) {
        return $imageUrl;
    }

    if (str_starts_with($imageUrl, "/fashion_store/")) {
        return $imageUrl;
    }

    return "../" . ltrim($imageUrl, "/");
}

include "_layout_start.php";
?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-4xl font-extrabold">Quản lý sản phẩm </h1>
        <p class="text-gray-500 mt-2">Thêm, sửa, xóa và theo dõi sản phẩm trong cửa hàng.</p>
    </div>

    <a href="add_product.php"
       class="bg-black text-white px-6 py-3 rounded-xl hover:bg-red-500">
        + Thêm sản phẩm
    </a>
</div>
<div class="grid md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-3xl shadow-xl">
        <p class="text-gray-500 font-semibold">Doanh thu hôm nay</p>
        <h2 class="text-3xl font-extrabold text-red-500 mt-2">
            <?= number_format($todayRevenue, 0, ",", "."); ?>đ
        </h2>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-xl">
        <p class="text-gray-500 font-semibold">Doanh thu tháng này</p>
        <h2 class="text-3xl font-extrabold text-red-500 mt-2">
            <?= number_format($monthRevenue, 0, ",", "."); ?>đ
        </h2>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-xl">
        <p class="text-gray-500 font-semibold">Doanh thu năm nay</p>
        <h2 class="text-3xl font-extrabold text-red-500 mt-2">
            <?= number_format($yearRevenue, 0, ",", "."); ?>đ
        </h2>
    </div>
</div>
<div class="bg-white rounded-3xl shadow-xl overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="bg-black text-white">
                <th class="p-4 text-left">Ảnh</th>
                <th class="p-4 text-left">Tên sản phẩm</th>
                <th class="p-4 text-left">Danh mục</th>
                <th class="p-4 text-left">Giá bán</th>
                <th class="p-4 text-left">Tồn kho</th>
                <th class="p-4 text-left">Trạng thái</th>
                <th class="p-4 text-center">Thao tác</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($products as $p): ?>
            <?php $img = productImageAdmin($p["image_url"] ?? ""); ?>

            <tr class="border-b hover:bg-gray-50">
                <td class="p-4">
                    <img src="<?= htmlspecialchars($img); ?>"
                         class="w-16 h-16 object-cover rounded-xl border bg-gray-100"
                         onerror="this.onerror=null;this.src='../assets/images/default.png';">
                </td>

                <td class="p-4 font-bold">
                    <?= htmlspecialchars($p["name"]); ?>
                    <p class="text-sm text-gray-500">ID: <?= $p["id"]; ?></p>
                </td>

                <td class="p-4">
                    <?= htmlspecialchars($p["category_name"] ?? "Chưa có"); ?>
                </td>

                <td class="p-4 text-red-500 font-bold">
                    <?= number_format($p["price"], 0, ",", "."); ?>đ
                </td>

                <td class="p-4">
                    <?= $p["quantity"]; ?>
                </td>

                <td class="p-4">
                    <?php if ($p["status"] === "active"): ?>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">
                            Đang bán
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 bg-gray-200 text-gray-600 rounded-full text-sm">
                            Đã ẩn
                        </span>
                    <?php endif; ?>
                </td>

                <td class="p-4 text-center">
                    <a href="edit_product.php?id=<?= $p["id"]; ?>"
                       class="text-blue-600 font-semibold">
                        Sửa
                    </a>

                    <span class="mx-2">|</span>

                    <a href="delete_product.php?id=<?= $p["id"]; ?>"
                       onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')"
                       class="text-red-600 font-semibold">
                        Xóa
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include "_layout_end.php"; ?>