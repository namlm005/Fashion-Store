<?php
require "config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = $_GET["id"] ?? 0;

$stmt = $conn->prepare("
    SELECT products.*, categories.name AS category_name
    FROM products
    LEFT JOIN categories ON products.category_id = categories.id
    WHERE products.id = ?
");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    include "includes/header.php";
    echo "<div class='max-w-5xl mx-auto p-10 text-center text-2xl font-bold'>Không tìm thấy sản phẩm.</div>";
    include "includes/footer.php";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit();
    }

    if (!isset($_SESSION["cart"])) {
        $_SESSION["cart"] = [];
    }

    $quantity = intval($_POST["quantity"] ?? 1);
    $size = $_POST["size"] ?? "";

    if ($quantity < 1) {
        $quantity = 1;
    }

    if ($size == "") {
        echo "<script>
            alert('Vui lòng chọn size sản phẩm!');
            window.history.back();
        </script>";
        exit();
    }

    if ($quantity > intval($product["quantity"])) {
        echo "<script>
            alert('Số lượng trong kho không đủ!');
            window.history.back();
        </script>";
        exit();
    }

    $stmtUpdateStock = $conn->prepare("
        UPDATE products   
        SET quantity = quantity - ?  
        WHERE id = ? AND quantity >= ?
    ");

    $stmtUpdateStock->execute([
        $quantity,
        $product["id"],
        $quantity
    ]);

    if ($stmtUpdateStock->rowCount() == 0) {
        echo "<script>
            alert('Sản phẩm không đủ tồn kho!');
            window.history.back();
        </script>";
        exit();
    }

    $found = false;

    foreach ($_SESSION["cart"] as &$item) {
        if ($item["id"] == $product["id"] && ($item["size"] ?? "") == $size) {
            $item["quantity"] += $quantity;
            $found = true;
            break;
        }
    }
    unset($item);

    if (!$found) {
        $_SESSION["cart"][] = [
            "id" => $product["id"],
            "name" => $product["name"],
            "price" => $product["price"],
            "image_url" => $product["image_url"],
            "size" => $size,
            "quantity" => $quantity
        ];
    }

    if (($_POST["action"] ?? "") === "buy_now") {
        header("Location: checkout.php");
    } else {
        header("Location: cart.php");
    }

    exit();
}

include "includes/header.php";
?>

<section class="max-w-7xl mx-auto px-6 py-16">
    <div class="bg-white p-8 rounded-3xl shadow-xl grid md:grid-cols-2 gap-10">

        <img src="<?php echo htmlspecialchars($product['image_url'] ?: 'assets/images/default.png'); ?>"
             class="w-full h-[520px] object-cover rounded-3xl"
             onerror="this.src='assets/images/default.png'">

        <div class="flex flex-col justify-center">

            <h1 class="text-5xl font-extrabold mb-5">
                <?php echo htmlspecialchars($product['name']); ?>
            </h1>

            <p class="text-red-500 text-5xl font-extrabold mb-8">
                <?php echo number_format($product['price'], 0, ',', '.'); ?>đ
            </p>

            <div class="space-y-4 text-lg">
                <p>Thương hiệu: <b><?php echo htmlspecialchars($product['brand']); ?></b></p>
                <p>Số lượng: <b><?php echo $product['quantity']; ?></b></p>
                <p>Đánh giá: <b><?php echo $product['rating']; ?> / 5 ⭐</b></p>
            </div>

            <p class="mt-8 text-gray-600 text-lg">
                <?php echo htmlspecialchars($product['description']); ?>
            </p>

            <form method="POST" class="mt-10 flex flex-wrap gap-4 items-center">

                <div class="w-full">
                    <label class="block mb-2 font-bold text-lg">
                        Chọn size:
                    </label>

                    <select name="size"
                            required
                            class="border-2 border-gray-300 rounded-xl px-5 py-4 w-48 outline-none">
                        <option value="">-- Chọn size --</option>
                        <option value="S">Size S</option>
                        <option value="M">Size M</option>
                        <option value="L">Size L</option>
                        <option value="XL">Size XL</option>
                        <option value="XXL">Size XXL</option>
                    </select>
                </div>

                <div class="flex items-center border-2 border-gray-300 rounded-xl overflow-hidden bg-white">
                    <button type="button" onclick="decreaseQty()" class="px-5 py-4 bg-gray-100">-</button>

                    <input type="number"
                           name="quantity"
                           id="qty"
                           value="1"
                           min="1"
                           max="<?php echo $product['quantity']; ?>"
                           class="w-20 text-center outline-none font-bold text-xl">

                    <button type="button"
                            onclick="increaseQty(<?php echo $product['quantity']; ?>)"
                            class="px-5 py-4 bg-gray-100">
                        +
                    </button>
                </div>

                <?php if (isset($_SESSION["user_id"])): ?>

                    <button type="submit"
                            name="action"
                            value="add_cart"
                            class="bg-black text-white px-8 py-4 rounded-xl font-bold hover:bg-red-500">
                        Thêm vào giỏ hàng
                    </button>

                    <button type="submit"
                            name="action"
                            value="buy_now"
                            class="bg-red-500 text-white px-8 py-4 rounded-xl font-bold hover:bg-black">
                        Mua ngay
                    </button>

                <?php else: ?>

                    <a href="login.php"
                       onclick="alert('Vui lòng đăng nhập để mua hàng!')"
                       class="bg-gray-400 text-white px-8 py-4 rounded-xl font-bold">
                        Đăng nhập để mua
                    </a>

                <?php endif; ?>

            </form>
        </div>
    </div>
</section>

<script>
function increaseQty(maxQty) {
    const qty = document.getElementById("qty");
    let value = parseInt(qty.value) || 1;
    if (value < maxQty) qty.value = value + 1;
}

function decreaseQty() {
    const qty = document.getElementById("qty");
    let value = parseInt(qty.value) || 1;
    if (value > 1) qty.value = value - 1;
}
</script>

<?php include "includes/footer.php"; ?>