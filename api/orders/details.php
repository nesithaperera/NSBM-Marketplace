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

$user_id = (int) $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}

$order_id = isset($_GET["order_id"]) ? (int) $_GET["order_id"] : 0;

if ($order_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid order ID."
    ]);

    exit;
}


$sql = "SELECT
            id,
            buyer_id,
            total_amount,
            status,
            created_at
        FROM orders
        WHERE id = ? AND buyer_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Order not found."
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

$order = $result->fetch_assoc();

$stmt->close();

$sql = "SELECT
            order_items.id AS order_item_id,
            order_items.product_id,
            order_items.seller_id,
            order_items.price,
            order_items.quantity,
            order_items.subtotal,
            products.title,
            products.image
        FROM order_items
        INNER JOIN products
            ON order_items.product_id = products.id
        WHERE order_items.order_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();

$result = $stmt->get_result();

$items = [];

while ($row = $result->fetch_assoc()) {

    $items[] = [
        "order_item_id" => (int) $row["order_item_id"],
        "product_id" => (int) $row["product_id"],
        "seller_id" => (int) $row["seller_id"],
        "title" => $row["title"],
        "price" => (float) $row["price"],
        "quantity" => (int) $row["quantity"],
        "subtotal" => (float) $row["subtotal"],
        "image" => $row["image"]
    ];
}

$stmt->close();

echo json_encode([
    "success" => true,

    "order" => [
        "id" => (int) $order["id"],
        "buyer_id" => (int) $order["buyer_id"],
        "total_amount" => (float) $order["total_amount"],
        "status" => $order["status"],
        "created_at" => $order["created_at"]
    ],

    "items" => $items
]);


$conn->close();

?>