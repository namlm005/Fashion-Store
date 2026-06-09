<?php require_once '../config/database.php'; require_once '../includes/auth.php'; requireAdmin(); $staff=$conn->query("SELECT * FROM users WHERE role IN ('admin','staff') ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC); $pageTitle='Quản lý nhân viên'; include '_layout_start.php'; ?>
<div class="flex justify-between mb-8"><h1 class="text-4xl font-black">Quản lý nhân viên</h1><a href="add_staff.php" class="bg-black text-white px-6 py-3 rounded-xl">Thêm nhân viên</a></div><div class="bg-white rounded-3xl shadow-xl overflow-hidden"><table class="w-full"><tr class="bg-black text-white"><th class="p-4">ID</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Vai trò</th><th>Ngày tạo</th><th>Sửa</th></tr><?php foreach($staff as $s): ?><tr class="border-b text-center"><td class="p-4"><?= $s['id'] ?></td><td><?= htmlspecialchars($s['fullname']) ?></td><td><?= htmlspecialchars($s['email']) ?></td><td><?= htmlspecialchars($s['phone']) ?></td><td><?= $s['role'] ?></td><td><?= $s['created_at'] ?></td><td><a class="text-blue-600" href="edit_staff.php?id=<?= $s['id'] ?>">Sửa</a></td></tr><?php endforeach; ?></table></div><?php include '_layout_end.php'; ?>
<?php
if (!isset($user)) {
    $user = [
        "fullname" => "",
        "email" => "",
        "phone" => "",
        "role" => "staff"
    ];
}
?>

<form method="POST" class="bg-white p-8 rounded-3xl shadow-xl max-w-3xl">
    <input name="fullname" required placeholder="Họ và tên"
           value="<?php echo htmlspecialchars($user['fullname']); ?>"
           class="w-full border p-4 rounded-xl mb-4">

    <input type="email" name="email" required placeholder="Email"
           value="<?php echo htmlspecialchars($user['email']); ?>"
           class="w-full border p-4 rounded-xl mb-4">

    <?php if (!isset($isEdit) || !$isEdit): ?>
        <input type="password" name="password" required placeholder="Mật khẩu"
               class="w-full border p-4 rounded-xl mb-4">
    <?php endif; ?>

    <input name="phone" placeholder="Số điện thoại"
           value="<?php echo htmlspecialchars($user['phone']); ?>"
           class="w-full border p-4 rounded-xl mb-4">

    <select name="role" class="w-full border p-4 rounded-xl mb-4">
        <option value="staff" <?php echo $user['role'] === 'staff' ? 'selected' : ''; ?>>
            Nhân viên
        </option>
        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>
            Quản trị viên
        </option>
    </select>

</form>