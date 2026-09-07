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
            id,
            total_amount,
            status,
            created_at
        FROM orders
        WHERE buyer_id = ?
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$orders = [];

while ($row = $result->fetch_assoc()) {

    $orders[] = [
        "order_id" => (int) $row["id"],
        "total_amount" => (float) $row["total_amount"],
        "status" => $row["status"],
        "created_at" => $row["created_at"]
    ];
}

$stmt->close();

echo json_encode([
    "success" => true,
    "orders" => $orders,
    "order_count" => count($orders)
]);


$conn->close();

?>