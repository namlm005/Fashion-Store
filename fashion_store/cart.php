<?php
require "config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

/* Thêm sản phẩm */
if (isset($_GET["add"])) {
    $id = intval($_GET["add"]);

    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $found = false;

        foreach ($_SESSION["cart"] as &$item) {
            if ($item["id"] == $product["id"] && ($item["size"] ?? "") == "M") {
                $item["quantity"] += 1;
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
                "size" => "M",
                "quantity" => 1
            ];
        }
    }

    header("Location: cart.php");
    exit();
}

/* Xóa toàn bộ */
if (isset($_GET["clear"])) {
    $_SESSION["cart"] = [];
    header("Location: cart.php");
    exit();
}

/* Tăng số lượng */
if (isset($_GET["plus"])) {
    $index = intval($_GET["plus"]);

    if (isset($_SESSION["cart"][$index])) {
        $_SESSION["cart"][$index]["quantity"] += 1;
    }

    header("Location: cart.php");
    exit();
}

/* Giảm số lượng */
if (isset($_GET["minus"])) {
    $index = intval($_GET["minus"]);

    if (isset($_SESSION["cart"][$index])) {
        $_SESSION["cart"][$index]["quantity"] -= 1;

        if ($_SESSION["cart"][$index]["quantity"] <= 0) {
            unset($_SESSION["cart"][$index]);
            $_SESSION["cart"] = array_values($_SESSION["cart"]);
        }
    }

    header("Location: cart.php");
    exit();
}

/* Xóa từng sản phẩm */
if (isset($_GET["remove"])) {
    $index = intval($_GET["remove"]);

    if (isset($_SESSION["cart"][$index])) {
        unset($_SESSION["cart"][$index]);
        $_SESSION["cart"] = array_values($_SESSION["cart"]);
    }

    header("Location: cart.php");
    exit();
}

$total = 0;

include "includes/header.php";
?>

<section class="max-w-6xl mx-auto px-6 py-16">
    <h1 class="text-4xl font-extrabold mb-10">Giỏ hàng</h1>

    <?php if (empty($_SESSION["cart"])): ?>

        <div class="bg-white p-10 rounded-3xl shadow-xl text-center">
            <h2 class="text-2xl font-bold mb-4">Giỏ hàng đang trống</h2>
            <p class="text-gray-500 mb-6">Bạn chưa thêm sản phẩm nào.</p>

            <a href="products.php"
               class="bg-black text-white px-8 py-3 rounded-xl hover:bg-red-500">
                Mua sắm ngay
            </a>
        </div>

    <?php else: ?>

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">

            <?php foreach ($_SESSION["cart"] as $index => $item): ?>
                <?php 
                    $subtotal = $item["price"] * $item["quantity"];
                    $total += $subtotal;
                ?>

                <div class="grid grid-cols-[100px_1fr_200px] gap-6 items-center p-6 border-b">

                    <img src="<?= htmlspecialchars($item["image_url"]) ?>"
                         class="w-20 h-20 object-cover rounded-xl border">

                    <div>
                        <h3 class="text-lg font-bold">
                            <?= htmlspecialchars($item["name"]) ?>
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Size:
                            <b><?= htmlspecialchars($item["size"] ?? "M") ?></b>
                        </p>

                        <div class="flex items-center gap-3 mt-2">
                            <span class="text-gray-500 text-sm">Số lượng:</span>

                            <a href="cart.php?minus=<?= $index ?>"
                               class="w-8 h-8 flex items-center justify-center bg-gray-200 rounded-lg hover:bg-gray-300 font-bold">
                                -
                            </a>

                            <span class="font-bold">
                                <?= $item["quantity"] ?>
                            </span>

                            <a href="cart.php?plus=<?= $index ?>"
                               class="w-8 h-8 flex items-center justify-center bg-gray-200 rounded-lg hover:bg-gray-300 font-bold">
                                +
                            </a>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-red-500 font-bold text-xl">
                            <?= number_format($subtotal, 0, ',', '.') ?>đ
                        </p>

                        <a href="cart.php?remove=<?= $index ?>"
                           class="inline-block mt-2 px-4 py-2 text-sm font-semibold text-white bg-red-500 rounded-lg hover:bg-red-600"
                           onclick="return confirm('Xóa sản phẩm này?')">
                            Xóa
                        </a>
                    </div>

                </div>

            <?php endforeach; ?>

            <div class="p-6 flex flex-col md:flex-row justify-between items-center gap-5 bg-gray-50">

                <h2 class="text-2xl font-extrabold">
                    Tổng tiền:
                    <span class="text-red-500">
                        <?= number_format($total, 0, ',', '.') ?>đ
                    </span>
                </h2>

                <div class="flex gap-4">

                    <a href="cart.php?clear=1"
                       onclick="return confirm('Xóa toàn bộ giỏ hàng?')"
                       class="bg-red-500 text-white px-6 py-3 rounded-xl font-bold">
                        Xóa tất cả
                    </a>

                    <a href="checkout.php"
                       class="bg-black text-white px-8 py-3 rounded-xl font-bold hover:bg-red-500">
                        Thanh toán
                    </a>

                </div>
            </div>

        </div>

    <?php endif; ?>
</section>
<?php include "includes/footer.php"; ?>