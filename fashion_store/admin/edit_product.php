<?php
require_once "../config/database.php";
require_once "../includes/auth.php";
requireStaffOrAdmin();

$id = $_GET["id"] ?? 0;

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: products.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $image_url = $_POST["old_image_url"] ?? "";

    if (!empty($_FILES["image_file"]["name"])) {
        $uploadDir = "../assets/images/products/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $allowedExt = ["jpg", "jpeg", "png", "webp"];
        $ext = strtolower(pathinfo($_FILES["image_file"]["name"], PATHINFO_EXTENSION));

        if (in_array($ext, $allowedExt)) {
            $fileName = time() . "_" . uniqid() . "." . $ext;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $targetPath)) {
                $image_url = "assets/images/products/" . $fileName;
            }
        }
    }

    $stmt = $conn->prepare("
        UPDATE products SET
            name = ?,
            category_id = ?,
            gender = ?,
            original_price = ?,
            price = ?,
            quantity = ?,
            image_url = ?,
            description = ?,
            brand = ?,
            manufacture_date = ?,
            rating = ?,
            status = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $_POST["name"],
        $_POST["category_id"] ?: null,
        $_POST["gender"] ?? "unisex",
        $_POST["original_price"],
        $_POST["price"],
        $_POST["quantity"],
        $image_url,
        $_POST["description"],
        $_POST["brand"],
        $_POST["manufacture_date"] ?: null,
        $_POST["rating"],
        $_POST["status"],
        $id
    ]);

    header("Location: products.php");
    exit();
}

include "_layout_start.php";
?>

<h1 class="text-4xl font-extrabold mb-8">Sửa sản phẩm</h1>

<?php include "product_form.php"; ?>

<?php include "_layout_end.php"; ?>