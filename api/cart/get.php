<?php

session_start();

require_once "../../config/database.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);

    exit;
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}

$sql = "SELECT 
            cart_items.id AS cart_item_id,
            cart_items.product_id,
            cart_items.quantity AS cart_quantity,
            products.title,
            products.price,
            products.image,
            products.quantity AS available_stock,
            products.status
        FROM cart_items
        INNER JOIN products 
            ON cart_items.product_id = products.id
        WHERE cart_items.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();


$cart_items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {

    $price = (float)$row["price"];
    $quantity = (int)$row["cart_quantity"];

    $subtotal = $price * $quantity;

    $total += $subtotal;

    $cart_items[] = [
        "cart_item_id" => (int)$row["cart_item_id"],
        "product_id" => (int)$row["product_id"],
        "title" => $row["title"],
        "price" => $price,
        "quantity" => $quantity,
        "available_stock" => (int)$row["available_stock"],
        "image" => $row["image"],
        "status" => $row["status"],
        "subtotal" => $subtotal
    ];
}

echo json_encode([
    "success" => true,
    "items" => $cart_items,
    "total" => $total,
    "item_count" => count($cart_items)
]);


$stmt->close();
$conn->close();

?>