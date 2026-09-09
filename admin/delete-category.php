<?php

require_once "../includes/admin-auth.php";
require_once "../config/database.php";

$category_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($category_id <= 0) {
    header("Location: categories.php");
    exit;
}

// Check whether category exists
$sql = "SELECT id, name
        FROM categories
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    $stmt->close();

    header("Location: categories.php?message=notfound");
    exit;
}

$category = $result->fetch_assoc();

$stmt->close();


// Check whether products use this category
$check_sql = "SELECT COUNT(*) AS product_count
              FROM products
              WHERE category_id = ?";

$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $category_id);
$check_stmt->execute();

$check_result = $check_stmt->get_result();
$row = $check_result->fetch_assoc();

$product_count = (int) $row["product_count"];

$check_stmt->close();


// Don't delete if products exist
if ($product_count > 0) {

    header(
        "Location: categories.php?message=has_products"
    );

    exit;
}


// Delete category
$delete_sql = "DELETE FROM categories
               WHERE id = ?";

$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $category_id);

if ($delete_stmt->execute()) {

    header(
        "Location: categories.php?message=deleted"
    );

    exit;

} else {

    header(
        "Location: categories.php?message=error"
    );

    exit;
}

?>