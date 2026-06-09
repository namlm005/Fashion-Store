<?php
require "../config/database.php";
require "../includes/auth.php";
requireAdmin();

$id = $_GET["id"] ?? 0;

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role IN ('admin','staff')");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: staff.php");
    exit();
}

$isEdit = true;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("
        UPDATE users SET fullname=?, email=?, phone=?, role=?
        WHERE id=?
    ");

    $stmt->execute([
        $_POST["fullname"],
        $_POST["email"],
        $_POST["phone"],
        $_POST["role"],
        $id
    ]);

    header("Location: staff.php");
    exit();
}

include "_layout_start.php";
?>

<h1 class="text-4xl font-extrabold mb-8">Sửa nhân viên</h1>

<?php include "staff_form.php"; ?>

<?php include "_layout_end.php"; ?>