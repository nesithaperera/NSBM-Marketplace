<?php

session_start();

require_once "../../config/database.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);

    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}

$user_id = (int) $_SESSION["user_id"];

$sql = "SELECT
            cart_items.id AS cart_item_id,
            cart_items.product_id,
            cart_items.quantity,
            products.title,
            products.price,
            products.quantity AS available_stock,
            products.image,
            products.status
        FROM cart_items
        INNER JOIN products
            ON cart_items.product_id = products.id
        WHERE cart_items.user_id = ?
        ORDER BY cart_items.id DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to prepare cart query."
    ]);

    exit;
}

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {

    $price = (float) $row["price"];
    $quantity = (int) $row["quantity"];
    $available_stock = (int) $row["available_stock"];

    $subtotal = $price * $quantity;

    $items[] = [
        "cart_item_id" => (int) $row["cart_item_id"],
        "product_id" => (int) $row["product_id"],
        "title" => $row["title"],
        "price" => $price,
        "quantity" => $quantity,
        "available_stock" => $available_stock,
        "image" => $row["image"],
        "status" => $row["status"],
        "subtotal" => $subtotal
    ];

    $total += $subtotal;
}

$stmt->close();
$conn->close();

echo json_encode([
    "success" => true,
    "items" => $items,
    "total" => $total
]);

?>