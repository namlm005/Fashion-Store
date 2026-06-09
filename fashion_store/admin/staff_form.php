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

    <button class="bg-black text-white px-8 py-3 rounded-xl hover:bg-red-500">
        Lưu nhân viên
    </button>
</form>