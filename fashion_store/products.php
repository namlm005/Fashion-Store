<?php
include "config/database.php";
include "includes/header.php";

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM products WHERE status = 'active'";
$params = [];

if ($search !== '') {
    $sql .= " AND name LIKE ?";
    $params[] = "%$search%";
}

if ($category !== '') {
    $sql .= " AND category_id = ?";
    $params[] = $category;
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtAll = $conn->query("SELECT id, name, price, image_url FROM products WHERE status = 'active' ORDER BY id DESC");
$allProducts = $stmtAll->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="max-w-7xl mx-auto px-6 py-10">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold">Sản phẩm</h1>
            <p class="text-gray-500 mt-1">Tìm kiếm và lọc sản phẩm theo danh mục.</p>
        </div>

        <form method="GET" class="flex flex-col md:flex-row gap-3">

            <div class="relative w-full md:w-64">
                <input 
                    type="text" 
                    name="search" 
                    id="searchInput"
                    autocomplete="off"
                    value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Tìm sản phẩm..."
                    class="border px-4 py-3 rounded-xl w-full"
                >

                <div id="searchDropdown"
                     class="hidden absolute top-full left-0 right-0 bg-white border rounded-xl shadow-xl mt-2 z-50 max-h-80 overflow-y-auto">
                </div>
            </div>

            <select name="category" class="border px-4 py-3 rounded-xl w-full md:w-48">
                <option value="">Tất cả danh mục</option>

                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"
                        <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="bg-black text-white px-6 py-3 rounded-xl hover:bg-red-500">
                Lọc
            </button>

            <a href="products.php" class="bg-gray-200 px-6 py-3 rounded-xl text-center hover:bg-gray-300">
                Xóa lọc
            </a>
        </form>
    </div>

    <?php if (empty($products)): ?>
        <div class="bg-white rounded-2xl shadow p-10 text-center">
            <h2 class="text-2xl font-bold mb-3">Không tìm thấy sản phẩm</h2>
            <p class="text-gray-500">Hãy thử từ khóa hoặc danh mục khác.</p>
        </div>
    <?php else: ?>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

            <?php foreach($products as $p): ?>
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden h-full flex flex-col">

                <div class="w-full h-52 overflow-hidden bg-gray-100 flex items-center justify-center">
                    <img src="<?= htmlspecialchars($p['image_url'] ?: 'assets/images/default.png') ?>"
                         class="w-full h-full object-cover transition duration-300 hover:scale-105"
                         onerror="this.src='https://via.placeholder.com/300x200'"
                         alt="<?= htmlspecialchars($p['name']) ?>">
                </div>

                <div class="p-3 flex flex-col flex-1">
                    <h3 class="font-semibold text-sm truncate">
                        <?= htmlspecialchars($p['name']) ?>
                    </h3>

                    <p class="text-red-500 font-bold text-sm mt-1">
                        <?= number_format($p['price'], 0, ',', '.') ?>đ
                    </p>

                    <div class="mt-3 flex gap-2">
                        <a href="product-detail.php?id=<?= $p['id'] ?>"
                           class="flex-1 text-center bg-black text-white py-2 rounded text-sm hover:bg-red-500 transition">
                            Xem
                        </a>

                        <a href="cart.php?add=<?= $p['id'] ?>"
                           class="flex-1 text-center border py-2 rounded text-sm hover:bg-gray-100 transition">
                            Thêm vào giỏ
                        </a>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<script>
const productsData = <?= json_encode(array_map(function($p) {
    return [
        "id" => $p["id"],
        "name" => $p["name"],
        "price" => number_format($p["price"], 0, ",", ".") . "đ",
        "image" => $p["image_url"] ?: "assets/images/default.png"
    ];
}, $allProducts), JSON_UNESCAPED_UNICODE); ?>;

const searchInput = document.getElementById("searchInput");
const searchDropdown = document.getElementById("searchDropdown");

if (searchInput && searchDropdown) {
    searchInput.addEventListener("input", function () {
        const keyword = this.value.toLowerCase().trim();
        searchDropdown.innerHTML = "";

        if (keyword === "") {
            searchDropdown.classList.add("hidden");
            return;
        }

        const results = productsData.filter(p =>
            p.name.toLowerCase().includes(keyword)
        ).slice(0, 8);

        if (results.length === 0) {
            searchDropdown.innerHTML = `
                <div class="p-4 text-gray-500 text-sm">
                    Không tìm thấy sản phẩm phù hợp
                </div>
            `;
            searchDropdown.classList.remove("hidden");
            return;
        }

        results.forEach(p => {
            searchDropdown.innerHTML += `
                <a href="product-detail.php?id=${p.id}"
                   class="flex items-center gap-3 p-3 hover:bg-gray-100 border-b">
                    <img src="${p.image}"
                         class="w-12 h-12 rounded-lg object-cover border"
                         onerror="this.src='https://via.placeholder.com/80'">
                    <div>
                        <p class="font-semibold text-sm">${p.name}</p>
                        <p class="text-red-500 text-sm font-bold">${p.price}</p>
                    </div>
                </a>
            `;
        });

        searchDropdown.classList.remove("hidden");
    });

    document.addEventListener("click", function (e) {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.classList.add("hidden");
        }
    });
}
</script>

<?php include "includes/footer.php"; ?>