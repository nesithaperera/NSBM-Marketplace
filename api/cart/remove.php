<?php

session_start();

require_once"../..//config/database.php";

if(!isset($_SESSION["user_id"])) {
    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);
    exit;
}

$user_id = $_SESSION["user_id"];

if($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"),true);
$product_id = isset($data["product_id"])? (int)$data["product_id"]:0;

if($product_id <= 0){
    echo json_encode([
        "success" => false,
        "message" => "Invalid product ID."
    ]);
    exit;
}

$sql = "DELETE FROM cart_items
        WHERE user_id = ? AND product_id = ?";
        
 $stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        echo json_encode([
            "success" => true,
            "message" => "Product removed from cart."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Product was not found in your cart."
        ]);
    }

} else {

    echo json_encode([
        "success" => false,
        "message" => "Failed to remove product from cart."
    ]);
}

$stmt->close();
$conn->close();

?>