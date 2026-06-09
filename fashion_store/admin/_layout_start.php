<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER["PHP_SELF"]);

function activeMenu($page, $currentPage) {
    return $page === $currentPage
        ? "bg-white text-black"
        : "hover:bg-white hover:text-black";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin - Fashion Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="flex min-h-screen">
    <aside class="w-[280px] bg-black text-white fixed top-0 left-0 bottom-0 p-6 overflow-y-auto">
        <div class="flex flex-col h-full">

            <div>
                <h1 class="text-3xl font-extrabold mb-2">ADMIN</h1>

                <p class="text-gray-400 mb-8">
                    <?php echo htmlspecialchars($_SESSION["fullname"] ?? "Quản trị viên"); ?>
                </p>

                <nav class="space-y-2">
                    <a href="products.php"
                       class="block px-4 py-3 rounded-xl <?php echo activeMenu('products.php', $currentPage); ?>">
                        Quản lý sản phẩm
                    </a>

                    <a href="add_product.php"
                       class="block px-4 py-3 rounded-xl <?php echo activeMenu('add_product.php', $currentPage); ?>">
                        Thêm sản phẩm
                    </a>

                    <a href="customers.php"
                       class="block px-4 py-3 rounded-xl <?php echo activeMenu('customers.php', $currentPage); ?>">
                        Khách hàng
                    </a>

                    <a href="orders.php"
                       class="block px-4 py-3 rounded-xl <?php echo activeMenu('orders.php', $currentPage); ?>">
                        Đơn hàng
                    </a>

                    <a href="feedback.php"
                       class="block px-4 py-3 rounded-xl <?php echo activeMenu('feedback.php', $currentPage); ?>">
                        Phản hồi
                    </a>
                </nav>
            </div>

            <div class="mt-auto space-y-2 pt-6">
                <a href="../index.php"
                   class="block px-4 py-3 rounded-xl bg-yellow-400 text-black hover:bg-yellow-300">
                    Về website
                </a>

                <a href="../logout.php"
                   class="block px-4 py-3 rounded-xl bg-red-600 text-white hover:bg-red-700">
                    Đăng xuất
                </a>
            </div>

        </div>
    </aside>

    <main class="ml-[280px] p-10 w-full">