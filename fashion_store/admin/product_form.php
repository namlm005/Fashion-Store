<form method="POST" enctype="multipart/form-data" class="space-y-4 max-w-xl">

    <input type="text" name="name" placeholder="Tên sản phẩm"
        value="<?= htmlspecialchars($product['name'] ?? '') ?>"
        class="w-full p-3 border rounded-lg">

    <select name="category_id" class="w-full p-3 border rounded-lg">
        <option value="">Chọn danh mục</option>
        <?php
        $categories = $conn->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($categories as $cat):
        ?>
            <option value="<?= $cat['id'] ?>"
                <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="gender" class="w-full p-3 border rounded-lg">
        <option value="male" <?= ($product['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Nam</option>
        <option value="female" <?= ($product['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Nữ</option>
        <option value="unisex" <?= ($product['gender'] ?? 'unisex') == 'unisex' ? 'selected' : '' ?>>Unisex</option>
    </select>

    <input type="number" name="original_price" placeholder="Giá gốc"
        value="<?= htmlspecialchars($product['original_price'] ?? '') ?>"
        class="w-full p-3 border rounded-lg">

    <input type="number" name="price" placeholder="Giá bán"
        value="<?= htmlspecialchars($product['price'] ?? '') ?>"
        class="w-full p-3 border rounded-lg">

    <input type="number" name="quantity" placeholder="Số lượng"
        value="<?= htmlspecialchars($product['quantity'] ?? '') ?>"
        class="w-full p-3 border rounded-lg">

    <input type="hidden" name="old_image_url" value="<?= htmlspecialchars($product['image_url'] ?? '') ?>">

    <div>
        <p class="font-semibold mb-2">Ảnh hiện tại</p>
        <img src="../<?= htmlspecialchars($product['image_url'] ?? 'assets/images/default.png') ?>"
             class="w-32 h-32 object-cover rounded-xl border mb-3"
             onerror="this.src='../assets/images/default.png'">
    </div>

    <div>
        <label class="font-semibold">Chọn ảnh mới</label>
        <input type="file" name="image_file" accept="image/*"
               class="w-full p-3 border rounded-lg">
        <p class="text-sm text-gray-500 mt-1">
            Nếu không chọn ảnh mới, hệ thống sẽ giữ ảnh cũ.
        </p>
    </div>

    <input type="text" name="brand" placeholder="Thương hiệu"
        value="<?= htmlspecialchars($product['brand'] ?? '') ?>"
        class="w-full p-3 border rounded-lg">

    <input type="date" name="manufacture_date"
        value="<?= htmlspecialchars($product['manufacture_date'] ?? '') ?>"
        class="w-full p-3 border rounded-lg">

    <input type="number" step="0.1" name="rating" placeholder="Đánh giá"
        value="<?= htmlspecialchars($product['rating'] ?? '') ?>"
        class="w-full p-3 border rounded-lg">

    <select name="status" class="w-full p-3 border rounded-lg">
        <option value="active" <?= ($product['status'] ?? '') == 'active' ? 'selected' : '' ?>>Đang bán</option>
        <option value="hidden" <?= ($product['status'] ?? '') == 'hidden' ? 'selected' : '' ?>>Ẩn sản phẩm</option>
    </select>

    <textarea name="description" placeholder="Mô tả"
        class="w-full p-3 border rounded-lg"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>

    <button type="submit"
        class="bg-black text-white px-6 py-3 rounded-lg hover:bg-red-500">
        Lưu sản phẩm
    </button>

</form>