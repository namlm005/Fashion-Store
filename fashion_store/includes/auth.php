<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('requireLogin')) {
    function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /fashion_store/login.php");
            exit();
        }
    }
}

if (!function_exists('requireAdmin')) {
    function requireAdmin() {
        requireLogin();

        if ($_SESSION['role'] !== 'admin') {
            header("Location: /fashion_store/index.php");
            exit();
        }
    }
}

if (!function_exists('requireStaffOrAdmin')) {
    function requireStaffOrAdmin() {
        requireLogin();

        if (!in_array($_SESSION['role'], ['admin', 'staff'])) {
            header("Location: /fashion_store/index.php");
            exit();
        }
    }
}
?>