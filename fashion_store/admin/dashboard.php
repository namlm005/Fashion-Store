<?php
require "../config/database.php";
require "../includes/auth.php";
requireStaffOrAdmin();

$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $category_id = $_POST["category_id"] ?: null;
    $original_price = $_POST["original_price"] ?: 0;
    $price = $_POST["price"];
    $quantity = $_POST["quantity"] ?: 0;
    $image_url = $_POST["image_url"];
    $description = $_POST["description"];
    $brand = $_POST["brand"];
    $manufacture_date = $_POST["manufacture_date"] ?: null;
    $rating = $_POST["rating"] ?: 5;
    $status = $_POST["status"];

    if ($name === "" || $price === "") {
        $error = "Vui lòng nhập tên sản phẩm và giá bán.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO products
            (name, category_id, original_price, price, quantity, image_url, description, brand, manufacture_date, rating, status)
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $name,
            $category_id,
            $original_price,
            $price,
            $quantity,
            $image_url,
            $description,
            $brand,
            $manufacture_date,
            $rating,
            $status
        ]);

        header("Location: products.php");
        exit();
    }
}

include "_layout_start.php";
?>

<h1 class="text-4xl font-extrabold mb-8">Thêm sản phẩm</h1>

<?php if ($error): ?>
    <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-6">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<form method="POST" class="bg-white p-8 rounded-3xl shadow-xl max-w-5xl">
    <div class="grid md:grid-cols-2 gap-5">

        <div>
            <label class="font-semibold">Tên sản phẩm</label>
            <input name="name" required
                   class="w-full border p-4 rounded-xl mt-2"
                   placeholder="Ví dụ: Áo thun nam basic">
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
            <label class="font-semibold">Giá gốc</label>
            <input type="number" name="original_price"
                   class="w-full border p-4 rounded-xl mt-2"
                   placeholder="Ví dụ: 250000">
        </div>

        <div>
            <label class="font-semibold">Giá bán</label>
            <input type="number" name="price" required
                   class="w-full border p-4 rounded-xl mt-2"
                   placeholder="Ví dụ: 199000">
        </div>

        <div>
            <label class="font-semibold">Số lượng</label>
            <input type="number" name="quantity"
                   class="w-full border p-4 rounded-xl mt-2"
                   placeholder="Ví dụ: 50">
        </div>

        <div>
            <label class="font-semibold">Thương hiệu</label>
            <input name="brand"
                   class="w-full border p-4 rounded-xl mt-2"
                   placeholder="Ví dụ: Fashion Store">
        </div>

        <div>
            <label class="font-semibold">Ngày sản xuất</label>
            <input type="date" name="manufacture_date"
                   class="w-full border p-4 rounded-xl mt-2">
        </div>

        <div>
            <label class="font-semibold">Đánh giá</label>
            <input type="number" step="0.1" min="1" max="5" name="rating"
                   value="5"
                   class="w-full border p-4 rounded-xl mt-2">
        </div>

        <div class="md:col-span-2">
            <label class="font-semibold">URL ảnh sản phẩm</label>
            <input name="image_url"
                   class="w-full border p-4 rounded-xl mt-2"
                   placeholder="Dán link ảnh sản phẩm">
        </div>

        <div>
            <label class="font-semibold">Trạng thái</label>
            <select name="status" class="w-full border p-4 rounded-xl mt-2">
                <option value="active">Đang bán</option>
                <option value="hidden">Ẩn sản phẩm</option>
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="font-semibold">Mô tả sản phẩm</label>
            <textarea name="description"
                      class="w-full border p-4 rounded-xl mt-2"
                      rows="5"
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