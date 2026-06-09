<?php
session_start();
include "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$cart = $_SESSION["cart"] ?? [];

if (empty($cart)) {
    header("Location: cart.php");
    exit();
}

$total = 0;
foreach ($cart as $item) {
    $total += $item["price"] * $item["quantity"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $payment = $_POST["payment"];

    $sql = "INSERT INTO orders 
            (user_id, receiver_name, receiver_phone, receiver_address, total_price, payment_method, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $user_id,
        $name,
        $phone,
        $address,
        $total,
        $payment
    ]);

    $order_id = $conn->lastInsertId();

    foreach ($cart as $item) {
        $subtotal = $item["price"] * $item["quantity"];

        $productNameWithSize = $item["name"] . " - Size " . ($item["size"] ?? "M");

        $sql_item = "INSERT INTO order_items 
            (order_id, product_id, product_name, price, quantity, subtotal)
            VALUES (?, ?, ?, ?, ?, ?)";

        $stmt_item = $conn->prepare($sql_item);
        $stmt_item->execute([
            $order_id,
            $item["id"],
            $productNameWithSize,
            $item["price"],
            $item["quantity"],
            $subtotal
        ]);
    }

    unset($_SESSION["cart"]);

    header("Location: success.php");
    exit();
}
?>

<?php include "includes/header.php"; ?>

<section class="max-w-6xl mx-auto px-6 py-16 grid md:grid-cols-2 gap-8 items-start">

    <div class="bg-white p-6 rounded-2xl shadow self-start">
        <h2 class="text-2xl font-bold mb-6">Thanh toán</h2>

        <form method="POST">
            <input type="text" name="name" required placeholder="Tên người nhận"
                class="w-full border p-3 rounded mb-4">

            <input type="text" name="phone" required placeholder="Số điện thoại"
                class="w-full border p-3 rounded mb-4">

            <textarea name="address" required placeholder="Địa chỉ"
                class="w-full border p-3 rounded mb-4"></textarea>

            <select name="payment" id="paymentSelect" class="w-full border p-3 rounded mb-4">
                <option value="cod">Thanh toán khi nhận hàng</option>
                <option value="bank">Chuyển khoản ngân hàng</option>
            </select>

            <div id="bankInfo" class="hidden border rounded-xl p-4 mb-4 bg-gray-50 text-center">
                <h3 class="font-bold text-lg mb-2">Thanh toán chuyển khoản</h3>

                <p><b>Ngân hàng:</b> Vietcombank</p>
                <p><b>Chủ tài khoản:</b> LAM MINH NAM</p>
                <p><b>Số tài khoản:</b> 1039564439</p>
                <p class="text-sm text-gray-500 mb-3">
                    Nội dung: Thanh toan don hang FashionStore
                </p>

                <img src="assets/img/qr-bank.jpg"
                    class="mx-auto w-48 h-48 object-contain border rounded-xl"
                    alt="QR chuyển khoản">
            </div>

            <button class="w-full bg-black text-white p-3 rounded font-bold hover:bg-red-500">
                Xác nhận đặt hàng
            </button>
        </form>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow self-start">
        <h2 class="text-2xl font-bold mb-6">Tóm tắt đơn hàng</h2>

        <?php foreach ($cart as $item): ?>
            <?php
                $image = $item["image_url"] ?? $item["image"] ?? "assets/no-image.png";
                $size = $item["size"] ?? "M";
            ?>

            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <img src="<?= htmlspecialchars($image) ?>"
                         class="w-16 h-16 object-cover rounded border"
                         onerror="this.src='https://via.placeholder.com/80'">

                    <div>
                        <p class="font-semibold"><?= htmlspecialchars($item["name"]) ?></p>

                        <p class="text-sm text-gray-500">
                            Size:
                            <b><?= htmlspecialchars($size) ?></b>
                        </p>

                        <p>Số lượng: <?= intval($item["quantity"]) ?></p>
                    </div>
                </div>

                <p class="text-red-500 font-bold">
                    <?= number_format($item["price"] * $item["quantity"], 0, ',', '.') ?>đ
                </p>
            </div>
        <?php endforeach; ?>

        <hr class="my-4">

        <div class="flex justify-between text-xl font-bold">
            <span>Tổng tiền:</span>
            <span class="text-red-500"><?= number_format($total, 0, ',', '.') ?>đ</span>
        </div>
    </div>

</section>

<script>
const paymentSelect = document.getElementById("paymentSelect");
const bankInfo = document.getElementById("bankInfo");

if (paymentSelect && bankInfo) {
    paymentSelect.addEventListener("change", function () {
        if (this.value === "bank") {
            bankInfo.classList.remove("hidden");
        } else {
            bankInfo.classList.add("hidden");
        }
    });
}
</script>

<?php include "includes/footer.php"; ?>