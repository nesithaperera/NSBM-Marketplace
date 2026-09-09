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

$sql = "SELECT
            orders.id AS order_id,
            orders.total_amount,
            orders.status,
            orders.created_at,
            COUNT(order_items.id) AS order_count
        FROM orders
        LEFT JOIN order_items
            ON orders.id = order_items.order_id
        WHERE orders.buyer_id = ?
        GROUP BY
            orders.id,
            orders.total_amount,
            orders.status,
            orders.created_at
        ORDER BY orders.created_at DESC";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$orders = [];

while ($row = $result->fetch_assoc()) {

    $orders[] = [
        "order_id" => (int) $row["order_id"],
        "total_amount" => $row["total_amount"],
        "status" => $row["status"],
        "created_at" => $row["created_at"],
        "order_count" => (int) $row["order_count"]
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    "success" => true,
    "orders" => $orders
]);

?>