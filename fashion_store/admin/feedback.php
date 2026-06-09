<?php
require "../config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

// XÓA THÔNG BÁO
if (isset($_GET["delete"])) {
    $id = $_GET["delete"];

    $stmt = $conn->prepare("
        UPDATE contacts 
        SET admin_deleted = 1 
        WHERE id = ?
    ");
    $stmt->execute([$id]);

    header("Location: feedback.php");
    exit();
}

// GỬI PHẢN HỒI
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $reply = trim($_POST["reply"]);

    $stmt = $conn->prepare("
        UPDATE contacts 
        SET reply = ?, status = 'replied'
        WHERE id = ?
    ");
    $stmt->execute([$reply, $id]);

    header("Location: feedback.php");
    exit();
}

// LẤY DANH SÁCH PHẢN HỒI CHƯA XÓA
$stmt = $conn->query("
    SELECT * FROM contacts
    WHERE admin_deleted IS NULL OR admin_deleted = 0
    ORDER BY created_at DESC
");
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include "_layout_start.php"; ?>

<h1 class="text-3xl font-bold mb-6">Phản hồi khách hàng</h1>

<?php if (empty($contacts)): ?>
    <div class="bg-white p-10 rounded-2xl shadow text-center">
        <div class="text-5xl mb-4">📭</div>
        <h2 class="text-2xl font-bold mb-2">Không có phản hồi mới</h2>
        <p class="text-gray-500">
            Khách hàng chưa gửi liên hệ mới hoặc bạn đã xóa hết thông báo.
        </p>
    </div>
<?php else: ?>

    <div class="space-y-6">
        <?php foreach ($contacts as $c): ?>
            <div class="bg-white p-6 rounded-2xl shadow">

                <p><b>Họ tên:</b> <?= htmlspecialchars($c["fullname"] ?? "") ?></p>
                <p><b>Email:</b> <?= htmlspecialchars($c["email"] ?? "") ?></p>
                <p><b>SĐT:</b> <?= htmlspecialchars($c["phone"] ?? "") ?></p>
                <p><b>Tiêu đề:</b> <?= htmlspecialchars($c["subject"] ?? "") ?></p>
                <p><b>Nội dung:</b> <?= htmlspecialchars($c["message"] ?? "") ?></p>

                <p class="mt-2">
                    <b>Trạng thái:</b>
                    <?php if (($c["status"] ?? "") === "replied"): ?>
                        <span class="text-green-600 font-semibold">Đã phản hồi</span>
                    <?php else: ?>
                        <span class="text-red-600 font-semibold">Chưa phản hồi</span>
                    <?php endif; ?>
                </p>

                <form method="POST" class="mt-4">
                    <input type="hidden" name="id" value="<?= $c["id"] ?>">

                    <textarea name="reply"
                              placeholder="Nhập phản hồi..."
                              class="w-full border p-3 rounded-xl mb-3"
                              rows="4"
                              required><?= htmlspecialchars($c["reply"] ?? "") ?></textarea>

                    <div class="flex gap-3">
                        <button class="bg-black text-white px-5 py-2 rounded-xl hover:bg-red-500">
                            Gửi phản hồi
                        </button>

                        <a href="feedback.php?delete=<?= $c["id"] ?>"
                           onclick="return confirm('Bạn có chắc muốn xóa thông báo này không?')"
                           class="bg-red-500 text-white px-5 py-2 rounded-xl hover:bg-red-600">
                            Xóa thông báo
                        </a>
                    </div>
                </form>

            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>

<?php include "_layout_end.php"; ?>