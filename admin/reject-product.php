<?php

require_once "../includes/admin-auth.php";
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: products.php");
    exit;
}

$product_id = isset($_POST["product_id"]) ? intval($_POST["product_id"]) : 0;

if ($product_id <= 0) {
    header("Location: products.php");
    exit;
}

$sql = "UPDATE products 
        SET status = 'rejected'
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    header("Location: products.php?message=rejected");
    exit;
}

$stmt->close();
$conn->close();

header("Location: products.php?message=error");
exit;
?>