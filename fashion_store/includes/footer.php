</main>

<footer class="bg-black text-white mt-20">
    <div class="max-w-7xl mx-auto px-6 py-12 grid md:grid-cols-4 gap-8">

        <div>
            <h2 class="text-3xl font-extrabold">
                Fashion<span class="text-red-500">Store</span>
            </h2>
            <p class="mt-4 text-gray-400">
                Website bán quần áo thời trang, hiện đại.
            </p>
        </div>

        <div>
            <h3 class="text-lg font-bold mb-4">Danh mục</h3>
            <ul class="space-y-2 text-gray-400">
                <li><a href="/fashion_store/products.php" class="hover:text-white">Áo</a></li>
                <li><a href="/fashion_store/products.php" class="hover:text-white">Quần</a></li>
                <li><a href="/fashion_store/products.php" class="hover:text-white">Mũ</a></li>
                <li><a href="/fashion_store/products.php" class="hover:text-white">Phụ kiện</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-lg font-bold mb-4">Hỗ trợ</h3>
            <ul class="space-y-2 text-gray-400">
                <li><a href="/fashion_store/contact.php" class="hover:text-white">Liên hệ</a></li>
                <li><a href="/fashion_store/cart.php" class="hover:text-white">Giỏ hàng</a></li>
                <li><a href="/fashion_store/checkout.php" class="hover:text-white">Thanh toán</a></li>
                <li><a href="/fashion_store/login.php" class="hover:text-white">Đăng nhập</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-lg font-bold mb-4">Thông tin liên hệ</h3>
            <p class="text-gray-400">Email: support@fashionstore.vn</p>
            <p class="text-gray-400 mt-2">Hotline: 0856492791</p>
            <p class="text-gray-400 mt-2">Địa chỉ: Hà Nội, Việt Nam</p>
        </div>

    </div>

    <div class="border-t border-gray-800 text-center py-5 text-gray-400">
        © 2026 Fashion Store. All rights reserved.
    </div>
</footer>

<div class="fixed right-5 bottom-6 z-50 flex flex-col items-center gap-4">

    <a href="https://zalo.me/0856492791" target="_blank"
       class="w-14 h-14 bg-white rounded-full shadow-lg flex items-center justify-center hover:scale-110 transition">
        <img src="https://upload.wikimedia.org/wikipedia/commons/9/91/Icon_of_Zalo.svg"
             class="w-8 h-8 object-contain">
    </a>

    <a href="https://m.me/namlam" target="_blank"
       class="w-14 h-14 bg-white rounded-full shadow-lg flex items-center justify-center hover:scale-110 transition">
        <img src="https://upload.wikimedia.org/wikipedia/commons/b/be/Facebook_Messenger_logo_2020.svg"
             class="w-8 h-8 object-contain">
    </a>

    <button onclick="window.scrollTo({top:0, behavior:'smooth'})"
            class="w-14 h-14 bg-black text-white rounded-full shadow-lg flex items-center justify-center hover:scale-110 transition">
        ↑
    </button>

</div>

<script>
const paymentSelect = document.querySelector('select[name="payment"]');
const bankInfo = document.getElementById('bankInfo');

if (paymentSelect && bankInfo) {
    paymentSelect.addEventListener('change', function () {
        if (this.value === 'bank') {
            bankInfo.classList.remove('hidden');
        } else {
            bankInfo.classList.add('hidden');
        }
    });

    if (paymentSelect.value === 'bank') {
        bankInfo.classList.remove('hidden');
    }
}
</script>

</body>
</html>