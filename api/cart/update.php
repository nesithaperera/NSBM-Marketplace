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

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$product_id = isset($data["product_id"]) ? (int)$data["product_id"] : 0;
$quantity = isset($data["quantity"]) ? (int)$data["quantity"] : 0;

if ($product_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid product ID."
    ]);

    exit;
}

if ($quantity <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Quantity must be at least 1."
    ]);

    exit;
}

$sql = "SELECT id, title, quantity, status
        FROM products
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Product not found."
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

$product = $result->fetch_assoc();

$stmt->close();

if ($product["status"] !== "approved") {

    echo json_encode([
        "success" => false,
        "message" => "This product is not available."
    ]);

    $conn->close();
    exit;
}

$available_stock = (int)$product["quantity"];

if ($available_stock <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "This product is out of stock."
    ]);

    $conn->close();
    exit;
}

if ($quantity > $available_stock) {

    echo json_encode([
        "success" => false,
        "message" => "Only " . $available_stock . " item(s) are available."
    ]);

    $conn->close();
    exit;
}

$sql = "SELECT id
        FROM cart_items
        WHERE user_id = ? AND product_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Product is not in your cart."
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

$stmt->close();

$sql = "UPDATE cart_items
        SET quantity = ?
        WHERE user_id = ? AND product_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $quantity, $user_id, $product_id);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Cart quantity updated successfully.",
        "product_id" => $product_id,
        "quantity" => $quantity,
        "available_stock" => $available_stock
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Failed to update cart quantity."
    ]);
}

$stmt->close();
$conn->close();

?>