<?php
require "config/database.php";
include "includes/header.php";

$stmt = $conn->query("SELECT * FROM products WHERE status='active' ORDER BY id DESC LIMIT 8");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="relative h-[560px] bg-black">
    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=1600"
         class="w-full h-full object-cover opacity-60">

    <div class="absolute inset-0 flex items-center justify-center text-center text-white px-6">
        <div>
            <h1 class="text-5xl md:text-7xl font-extrabold mb-6">
                Fashion Store
            </h1>
            <p class="text-xl md:text-2xl mb-8">
                Thời trang hiện đại, phong cách trẻ trung
            </p>
            <a href="products.php"
               class="bg-white text-black px-8 py-4 rounded-2xl font-bold hover:bg-red-500 hover:text-white">
                Khám phá sản phẩm
            </a>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-6 py-16">
    <h2 class="text-4xl font-extrabold text-center mb-4">
        Sản phẩm nổi bật
    </h2>
    <p class="text-center text-gray-500 mb-10">
        Những mẫu thời trang mới nhất dành cho bạn
    </p>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <?php foreach ($products as $p): ?>
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden hover:-translate-y-2 transition">
                <img src="<?php echo $p['image_url']; ?>"
                     class="w-full h-64 object-cover">

                <div class="p-5">
                    <h3 class="text-xl font-bold">
                        <?php echo $p['name']; ?>
                    </h3>

                    <p class="text-red-500 text-xl font-bold mt-2">
                        <?php echo number_format($p['price'], 0, ',', '.'); ?>đ
                    </p>

                    <a href="product-detail.php?id=<?php echo $p['id']; ?>"
                       class="block mt-5 bg-black text-white text-center py-3 rounded-xl hover:bg-red-500">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-8 text-center">
        <div class="p-8 rounded-3xl bg-gray-100">
            <h3 class="text-2xl font-bold mb-3">Giao hàng nhanh</h3>
            <p class="text-gray-600">Hỗ trợ giao hàng trong ngày và giao hàng dự kiến.</p>
        </div>

        <div class="p-8 rounded-3xl bg-gray-100">
            <h3 class="text-2xl font-bold mb-3">Thanh toán linh hoạt</h3>
            <p class="text-gray-600">Hỗ trợ COD, chuyển khoản, Visa, VNPay.</p>
        </div>
</section>

<?php include "includes/footer.php"; ?>