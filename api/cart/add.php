<?php
session_start();

require_once "../../config/database.php";

if(!isset($_SESSION["user_id"])) {
    http_response_code(401);
    
    echojson_encode([
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
$quantity = isset($data["quantity"]) ? (int)$data["quantity"] : 1;

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

$sql = "SELECT id, title, price, quantity, status
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

    exit;
}

$product = $result->fetch_assoc();

$stmt->close();

if ($product["status"] !== "approved") {
    echo json_encode([
        "success" => false,
        "message" => "This product is not available for purchase."
    ]);

    exit;
}

$available_stock = (int)$product["quantity"];

if ($available_stock <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "This product is out of stock."
    ]);

    exit;
}

$sql = "SELECT id, quantity
        FROM cart_items
        WHERE user_id = ? AND product_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $cart_item = $result->fetch_assoc();

    $cart_item_id = $cart_item["id"];
    $current_quantity = (int)$cart_item["quantity"];

    $new_quantity = $current_quantity + $quantity;

    if ($new_quantity > $available_stock) {
        echo json_encode([
            "success" => false,
            "message" => "Only " . $available_stock . " item(s) are available."
        ]);

        $stmt->close();
        exit;
    }

    $stmt->close();

    $sql = "UPDATE cart_items
            SET quantity = ?
            WHERE id = ? AND user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $new_quantity, $cart_item_id, $user_id);

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Product quantity updated in cart.",
            "product_id" => $product_id,
            "quantity" => $new_quantity
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to update cart."
        ]);
    }

}

else {

    $stmt->close();

    if ($quantity > $available_stock) {
        echo json_encode([
            "success" => false,
            "message" => "Only " . $available_stock . " item(s) are available."
        ]);

        exit;
    }

    $sql = "INSERT INTO cart_items
            (user_id, product_id, quantity)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Product added to cart.",
            "product_id" => $product_id,
            "quantity" => $quantity
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to add product to cart."
        ]);
    }
}

$stmt->close();
$conn->close();

?>
