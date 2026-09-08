<?php

include "../config/database.php";
include "../includes/auth.php";

$user_id = $_SESSION["user_id"];

// Check if product ID was provided
if (!isset($_GET["id"])) {
    die("Product not found.");
}

$product_id = $_GET["id"];

// Check that the product belongs to the logged-in user
$sql = "SELECT id, title
        FROM products
        WHERE id = ? AND user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Product not found or you do not have permission to delete this product.");
}

$product = $result->fetch_assoc();

$stmt->close();


// Delete the product
$delete_sql = "DELETE FROM products
               WHERE id = ? AND user_id = ?";

$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("ii", $product_id, $user_id);

if ($delete_stmt->execute()) {

    header("Location: mylisting.php");
    exit;

} else {

    echo "Error deleting product.";

}

$delete_stmt->close();

?>