<?php
require "config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$id = $_GET["id"] ?? null;
$userId = $_SESSION["user_id"];

if ($id) {
    $stmt = $conn->prepare("
        DELETE FROM orders
        WHERE id = ?
        AND user_id = ?
        AND status IN ('completed', 'cancelled')
    ");
    $stmt->execute([$id, $userId]);
}

header("Location: my-orders.php");
exit();