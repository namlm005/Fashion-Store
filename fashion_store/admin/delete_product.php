<?php
require "../config/database.php";

$conn->prepare("DELETE FROM products WHERE id=?")
->execute([$_GET['id']]);

header("Location: products.php");