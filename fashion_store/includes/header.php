<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['clear_notify'])) {
    $_SESSION['hide_order_notify'] = true;
    $_SESSION['hide_feedback_notify'] = true;
    header("Location: /fashion_store/index.php");
    exit();
}

if (isset($_GET['demo_role'])) {
    if ($_GET['demo_role'] === 'admin') {
        $_SESSION['user_id'] = 1;
        $_SESSION['fullname'] = 'Quản trị viên';
        $_SESSION['role'] = 'admin';
        unset($_SESSION['hide_order_notify']);
        unset($_SESSION['hide_feedback_notify']);
        header("Location: /fashion_store/admin/dashboard.php");
        exit();
    }

    if ($_GET['demo_role'] === 'customer') {
        $_SESSION['user_id'] = 2;
        $_SESSION['fullname'] = 'Khách hàng';
        $_SESSION['role'] = 'customer';
        unset($_SESSION['hide_order_notify']);
        unset($_SESSION['hide_feedback_notify']);
        header("Location: /fashion_store/index.php");
        exit();
    }

    if ($_GET['demo_role'] === 'logout') {
        session_destroy();
        header("Location: /fashion_store/index.php");
        exit();
    }
}

$cartCount = 0;
if (!empty($_SESSION["cart"])) {
    foreach ($_SESSION["cart"] as $item) {
        $cartCount += intval($item["quantity"]);
    }
}

$orderNotify = null;
$orderItemsNotify = [];
$recentOrders = [];
$feedbackNotify = [];

if (isset($_SESSION["user_id"])) {
    require_once __DIR__ . "/../config/database.php";

    $stmtRecent = $conn->prepare("
        SELECT * FROM orders
        WHERE user_id = ?
        ORDER BY id DESC
        LIMIT 5
    ");
    $stmtRecent->execute([$_SESSION["user_id"]]);
    $recentOrders = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($recentOrders)) {
        $orderNotify = $recentOrders[0];

        if (!empty($_SESSION['hide_order_notify'])) {
            $orderNotify = null;
            $orderItemsNotify = [];
        } else {
            $stmtItems = $conn->prepare("
                SELECT order_items.*, products.image_url
                FROM order_items
                LEFT JOIN products ON order_items.product_id = products.id
                WHERE order_items.order_id = ?
            ");
            $stmtItems->execute([$recentOrders[0]["id"]]);
            $orderItemsNotify = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    $email = $_SESSION["email"] ?? "";

    if (empty($email)) {
        $stmtUser = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $stmtUser->execute([$_SESSION["user_id"]]);
        $userEmail = $stmtUser->fetch(PDO::FETCH_ASSOC);
        $email = $userEmail["email"] ?? "";
    }

    if (!empty($email) && empty($_SESSION['hide_feedback_notify'])) {
        $stmtFeedback = $conn->prepare("
            SELECT * FROM contacts
            WHERE email = ?
            AND reply IS NOT NULL
            AND reply != ''
            ORDER BY id DESC
            LIMIT 5
        ");
        $stmtFeedback->execute([$email]);
        $feedbackNotify = $stmtFeedback->fetchAll(PDO::FETCH_ASSOC);
    }
}

function orderStatusText($status) {
    if ($status === "pending") return "Chờ xác nhận";
    if ($status === "confirmed") return "Đã xác nhận";
    if ($status === "shipping") return "Đang giao";
    if ($status === "completed") return "Đã giao";
    if ($status === "cancelled") return "Đã hủy";
    return $status;
}

function orderStatusColor($status) {
    if ($status === "pending") return "text-yellow-600";
    if ($status === "confirmed") return "text-blue-600";
    if ($status === "shipping") return "text-purple-600";
    if ($status === "completed") return "text-green-600";
    if ($status === "cancelled") return "text-red-600";
    return "text-gray-600";
}

$notifyCount = ($orderNotify ? 1 : 0) + count($feedbackNotify);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Fashion Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-800">

<header class="bg-white shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

        <a href="/fashion_store/index.php" class="text-2xl font-extrabold">
            Fashion<span class="text-red-500">Store</span>
        </a>

        <nav class="hidden md:flex gap-8 font-medium items-center">
            <a href="/fashion_store/index.php" class="hover:text-red-500">Trang chủ</a>
            <a href="/fashion_store/products.php" class="hover:text-red-500">Sản phẩm</a>
            <a href="/fashion_store/contact.php" class="hover:text-red-500">Liên hệ</a>
            <?php if (isset($_SESSION["user_id"])): ?>
        <a href="/fashion_store/history.php" class="hover:text-red-500">Lịch sử mua hàng</a>
    <?php endif; ?>
        </nav>

        <div class="flex items-center gap-3">

            <div class="relative group">
                <a href="/fashion_store/cart.php"
                   class="relative flex items-center justify-center w-11 h-11 rounded-xl bg-gray-100 hover:bg-black hover:text-white transition">
                    🛒
                    <?php if ($cartCount > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-6 h-6 rounded-full flex items-center justify-center">
                            <?= $cartCount ?>
                        </span>
                    <?php endif; ?>
                </a>

                <div class="absolute right-0 top-full mt-0 pt-3 w-96 hidden group-hover:block z-50">
                    <div class="bg-white rounded-2xl shadow-2xl p-4">
                        <h3 class="font-bold text-lg mb-3">Giỏ hàng</h3>

                        <?php if (empty($_SESSION["cart"])): ?>
                            <p class="text-gray-500 text-sm">Giỏ hàng trống.</p>
                        <?php else: ?>
                            <div class="space-y-3 max-h-72 overflow-y-auto">
                                <?php foreach ($_SESSION["cart"] as $item): ?>
                                    <div class="flex items-center gap-3 border-b pb-3">
                                        <img src="<?= htmlspecialchars($item["image_url"] ?? "") ?>"
                                             class="w-14 h-14 object-cover rounded-xl border"
                                             onerror="this.src='https://via.placeholder.com/80'">

                                        <div class="flex-1">
                                            <p class="text-sm font-semibold truncate">
                                                <?= htmlspecialchars($item["name"]) ?>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                SL: <?= intval($item["quantity"]) ?>
                                            </p>
                                        </div>

                                        <p class="text-red-500 text-sm font-bold">
                                            <?= number_format($item["price"] * $item["quantity"], 0, ',', '.') ?>đ
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <a href="/fashion_store/cart.php"
                               class="block mt-3 bg-black text-white text-center py-2 rounded-xl hover:bg-red-500">
                                Xem giỏ hàng
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="relative group">
                <button type="button"
                        class="relative flex items-center justify-center w-11 h-11 rounded-xl bg-gray-100 hover:bg-black hover:text-white transition">
                    📦
                    <?php if (!empty($recentOrders)): ?>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                            <?= count($recentOrders) ?>
                        </span>
                    <?php endif; ?>
                </button>

                <div class="absolute right-0 top-full mt-0 pt-3 w-[430px] hidden group-hover:block z-50">
                    <div class="bg-white rounded-2xl shadow-2xl p-5">
                        <h3 class="font-bold text-xl mb-4">Đơn mua gần đây</h3>

                        <?php if (empty($recentOrders)): ?>
                            <p class="text-gray-500 text-sm">Bạn chưa có đơn hàng nào.</p>
                        <?php else: ?>
                            <div class="space-y-3 max-h-80 overflow-y-auto">
                                <?php foreach ($recentOrders as $od): ?>
                                    <?php
                                    $stmtFirstItem = $conn->prepare("
                                        SELECT order_items.*, products.image_url
                                        FROM order_items
                                        LEFT JOIN products ON order_items.product_id = products.id
                                        WHERE order_items.order_id = ?
                                        LIMIT 1
                                    ");
                                    $stmtFirstItem->execute([$od["id"]]);
                                    $firstItem = $stmtFirstItem->fetch(PDO::FETCH_ASSOC);
                                    ?>

                                    <a href="/fashion_store/order-detail.php?id=<?= $od["id"] ?>"
                                       class="flex items-center gap-3 border rounded-2xl p-3 hover:bg-gray-50">
                                        <img src="<?= htmlspecialchars($firstItem["image_url"] ?? "") ?>"
                                             class="w-14 h-14 rounded-xl object-cover border"
                                             onerror="this.src='https://via.placeholder.com/80'">

                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-sm">Đơn hàng #<?= $od["id"] ?></p>
                                            <p class="text-sm truncate">
                                                <?= htmlspecialchars($firstItem["product_name"] ?? "Sản phẩm") ?>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Số lượng: <?= intval($firstItem["quantity"] ?? 1) ?>
                                            </p>
                                        </div>

                                        <div class="text-right">
                                            <p class="font-bold <?= orderStatusColor($od["status"]) ?>">
                                                <?= orderStatusText($od["status"]) ?>
                                            </p>
                                            <p class="text-red-500 font-bold text-sm">
                                                <?= number_format($od["total_price"], 0, ',', '.') ?>đ
                                            </p>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>

                            <a href="/fashion_store/my-orders.php"
                               class="block mt-4 bg-black text-white text-center py-2 rounded-xl hover:bg-red-500">
                                Xem tất cả đơn mua
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="relative">
                <button type="button"
                        onclick="toggleOrderPopup()"
                        class="relative flex items-center justify-center w-11 h-11 rounded-xl bg-gray-100 hover:bg-black hover:text-white transition">
                    🔔
                    <?php if ($notifyCount > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                            <?= $notifyCount ?>
                        </span>
                    <?php endif; ?>
                </button>

                <div id="orderPopup"
                     class="hidden absolute right-0 top-full mt-3 w-[430px] bg-white rounded-2xl shadow-2xl p-5 z-50">
                    <h3 class="font-bold text-xl mb-3">Thông báo</h3>

                    <?php if (!$orderNotify && empty($feedbackNotify)): ?>
                        <p class="text-gray-500 text-sm">Không có thông báo mới.</p>
                    <?php else: ?>

                        <?php foreach ($feedbackNotify as $fb): ?>
                            <div class="border rounded-xl p-3 mb-3 bg-green-50">
                                <p class="font-bold text-green-600">Admin đã phản hồi liên hệ</p>
                                <p class="text-sm text-gray-500">
                                    Về: <?= htmlspecialchars($fb["subject"] ?? "Không có tiêu đề") ?>
                                </p>
                                <p class="mt-2 text-sm">
                                    <?= nl2br(htmlspecialchars($fb["reply"] ?? "")) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>

                        <?php if ($orderNotify): ?>
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">Đơn hàng #<?= $orderNotify["id"]; ?></p>
                                <p class="font-bold <?= orderStatusColor($orderNotify["status"]) ?>">
                                    <?= orderStatusText($orderNotify["status"]); ?>
                                </p>
                            </div>

                            <div class="space-y-3 max-h-72 overflow-y-auto">
                                <?php foreach ($orderItemsNotify as $item): ?>
                                    <div class="flex items-center gap-3 border-b pb-3">
                                        <img src="<?= htmlspecialchars($item["image_url"] ?? ""); ?>"
                                             class="w-16 h-16 object-cover rounded-xl border"
                                             onerror="this.src='https://via.placeholder.com/80'">

                                        <div class="flex-1">
                                            <p class="font-semibold text-sm line-clamp-2">
                                                <?= htmlspecialchars($item["product_name"]); ?>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Số lượng: <?= intval($item["quantity"]); ?>
                                            </p>
                                        </div>

                                        <p class="text-red-500 font-bold text-sm">
                                            <?= number_format($item["subtotal"], 0, ',', '.'); ?>đ
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="mt-4 flex justify-between items-center">
                                <span class="font-bold">Tổng tiền:</span>
                                <span class="text-red-500 font-extrabold">
                                    <?= number_format($orderNotify["total_price"], 0, ',', '.'); ?>đ
                                </span>
                            </div>

                            <a href="/fashion_store/order-detail.php?id=<?= $orderNotify["id"] ?>"
                               class="block mt-4 text-center bg-black text-white py-2 rounded-xl hover:bg-red-500">
                                Xem chi tiết đơn mua
                            </a>
                        <?php endif; ?>

                        <a href="/fashion_store/index.php?clear_notify=1"
                           class="block mt-3 text-center bg-red-500 text-white py-2 rounded-xl hover:bg-red-600">
                            Xóa thông báo
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="hidden md:block text-sm">
                    Xin chào, <b><?= htmlspecialchars($_SESSION['fullname']); ?></b>
                </span>
                <?php if (isset($_SESSION["role"]) && ($_SESSION["role"] === "admin" || $_SESSION["role"] === "staff")): ?>
    <a href="/fashion_store/admin/dashboard.php"
       class="bg-yellow-400 text-black px-4 py-2 rounded-xl font-semibold hover:bg-yellow-300">
        Admin
    </a>
<?php endif; ?>

                <a href="/fashion_store/logout.php"
                   class="bg-red-500 text-white px-4 py-2 rounded-xl hover:bg-red-600">
                    Đăng xuất
                </a>
            <?php else: ?>
                <a href="/fashion_store/login.php" class="hover:text-red-500">Đăng nhập</a>
                <a href="/fashion_store/register.php"
                   class="bg-black text-white px-4 py-2 rounded-xl hover:bg-gray-800">
                    Đăng ký
                </a>
            <?php endif; ?>

        </div>
    </div>
</header>

<main class="min-h-screen">

<script>
function toggleOrderPopup() {
    const popup = document.getElementById("orderPopup");
    if (popup) {
        popup.classList.toggle("hidden");
    }
}
</script>