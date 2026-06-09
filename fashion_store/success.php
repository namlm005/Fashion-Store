<?php include "includes/header.php"; ?>

<section class="min-h-[70vh] flex items-center justify-center px-6">
    <div class="bg-white rounded-3xl shadow-xl p-10 max-w-xl text-center">
        <div class="text-6xl mb-5">✅</div>

        <h1 class="text-4xl font-extrabold text-green-600 mb-4">
            Đặt hàng thành công!
        </h1>

        <p class="text-gray-600 mb-8">
            Cảm ơn bạn đã mua hàng tại Fashion Store. Đơn hàng của bạn đã được ghi nhận.
        </p>

        <a href="index.php"
           class="inline-block bg-black text-white px-8 py-3 rounded-xl font-bold hover:bg-red-500">
            Quay về trang chủ
        </a>
    </div>
</section>

<script>
setTimeout(function () {
    window.location.href = "index.php";
}, 3000);
</script>

<?php include "includes/footer.php"; ?>