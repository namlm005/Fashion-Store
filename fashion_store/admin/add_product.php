<?php
require "../config/database.php";
require "../includes/auth.php";
requireStaffOrAdmin();

$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $image_url = "";

    if (!empty($_FILES["image_file"]["name"])) {
        $uploadDir = "../assets/images/products/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $allowedExt = ["jpg", "jpeg", "png", "webp"];
        $ext = strtolower(pathinfo($_FILES["image_file"]["name"], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            $error = "Chỉ cho phép upload ảnh JPG, JPEG, PNG hoặc WEBP.";
        } else {
            $fileName = time() . "_" . uniqid() . "." . $ext;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $targetPath)) {
                $image_url = "assets/images/products/" . $fileName;
            } else {
                $error = "Upload ảnh thất bại.";
            }
        }
    }

    if (empty($image_url)) {
        $image_url = "assets/images/default.png";
    }

    if ($error === "") {
        $stmt = $conn->prepare("
            INSERT INTO products
            (name, category_id, gender, original_price, price, quantity, image_url, description, brand, manufacture_date, rating, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $_POST["name"],
            $_POST["category_id"] ?: null,
            $_POST["gender"] ?? "unisex",
            $_POST["original_price"] ?: 0,
            $_POST["price"],
            $_POST["quantity"] ?: 0,
            $image_url,
            $_POST["description"],
            $_POST["brand"],
            $_POST["manufacture_date"] ?: null,
            $_POST["rating"] ?: 5,
            $_POST["status"]
        ]);

        header("Location: products.php");
        exit();
    }
}

include "_layout_start.php";
?>

<h1 class="text-4xl font-extrabold mb-8">Thêm sản phẩm</h1>

<?php if (!empty($error)): ?>
    <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-5 font-semibold">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-3xl shadow-xl max-w-6xl">
    <div class="grid md:grid-cols-2 gap-5">

        <div>
            <label class="font-semibold">Tên sản phẩm</label>
            <input name="name" required class="w-full border p-4 rounded-xl mt-2"
                   placeholder="Nhập tên sản phẩm">
        </div>

        <div>
            <label class="font-semibold">Danh mục</label>
            <select name="category_id" class="w-full border p-4 rounded-xl mt-2">
                <option value="">Chọn danh mục</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="font-semibold">Giới tính</label>
            <select name="gender" class="w-full border p-4 rounded-xl mt-2">
                <option value="male">Nam</option>
                <option value="female">Nữ</option>
                <option value="unisex" selected>Unisex</option>
            </select>
        </div>

        <div>
            <label class="font-semibold">Giá bán</label>
            <input type="number" name="price" required class="w-full border p-4 rounded-xl mt-2"
                   placeholder="199000">
        </div>

        <div>
            <label class="font-semibold">Số lượng</label>
            <input type="number" name="quantity" class="w-full border p-4 rounded-xl mt-2"
                   placeholder="50">
        </div>

        <div>
            <label class="font-semibold">Thương hiệu</label>
            <input name="brand" class="w-full border p-4 rounded-xl mt-2"
                   placeholder="Fashion Store">
        </div>

        <div>
            <label class="font-semibold">Ngày sản xuất</label>
            <input type="date" name="manufacture_date"
                   class="w-full border p-4 rounded-xl mt-2">
        </div>

        <div>
            <label class="font-semibold">Đánh giá</label>
            <input type="number" step="0.1" min="1" max="5" name="rating" value="5"
                   class="w-full border p-4 rounded-xl mt-2">
        </div>

        <div class="md:col-span-2">
            <label class="font-semibold">Ảnh sản phẩm</label>
            <input type="file" name="image_file" accept="image/*"
                   class="w-full border p-4 rounded-xl mt-2">
            <p class="text-sm text-gray-500 mt-2">
                Chọn ảnh từ máy. Ảnh sẽ tự lưu vào thư mục assets/images/products/.
            </p>
        </div>

        <div>
            <label class="font-semibold">Trạng thái</label>
            <select name="status" class="w-full border p-4 rounded-xl mt-2">
                <option value="active">Đang bán</option>
                <option value="hidden">Ẩn sản phẩm</option>
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="font-semibold">Mô tả</label>
            <textarea name="description" rows="5"
                      class="w-full border p-4 rounded-xl mt-2"
                      placeholder="Nhập mô tả sản phẩm"></textarea>
        </div>
    </div>

    <div class="mt-8 flex gap-4">
        <button class="bg-black text-white px-8 py-3 rounded-xl hover:bg-red-500">
            Lưu sản phẩm
        </button>

        <a href="products.php" class="bg-gray-200 px-8 py-3 rounded-xl">
            Quay lại
        </a>
    </div>
</form>

<?php include "_layout_end.php"; ?>