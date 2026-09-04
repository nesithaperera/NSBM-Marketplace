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

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}

$conn->begin_transaction();

try {

    $sql = "SELECT
                cart_items.product_id,
                cart_items.quantity AS cart_quantity,
                products.title,
                products.price,
                products.quantity AS available_stock,
                products.user_id AS seller_id,
                products.status
            FROM cart_items
            INNER JOIN products
                ON cart_items.product_id = products.id
            WHERE cart_items.user_id = ?
            FOR UPDATE";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {

        throw new Exception("Your cart is empty.");
    }

    $cart_items = [];
    $total_amount = 0;


    while ($row = $result->fetch_assoc()) {

        $product_id = (int) $row["product_id"];
        $quantity = (int) $row["cart_quantity"];
        $price = (float) $row["price"];
        $available_stock = (int) $row["available_stock"];
        $seller_id = (int) $row["seller_id"];

        if ($row["status"] !== "approved") {

            throw new Exception(
                "Product '" . $row["title"] . "' is no longer available."
            );
        }

        if ($quantity <= 0) {

            throw new Exception(
                "Invalid quantity for '" . $row["title"] . "'."
            );
        }

        if ($quantity > $available_stock) {

            throw new Exception(
                "Not enough stock for '" . $row["title"] .
                "'. Available: " . $available_stock
            );
        }

        $subtotal = $price * $quantity;

        $total_amount += $subtotal;

        $cart_items[] = [
            "product_id" => $product_id,
            "quantity" => $quantity,
            "price" => $price,
            "subtotal" => $subtotal,
            "seller_id" => $seller_id
        ];
    }

    $stmt->close();

    $order_status = "completed";

    $sql = "INSERT INTO orders
            (buyer_id, total_amount, status)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ids",
        $user_id,
        $total_amount,
        $order_status
    );

    if (!$stmt->execute()) {

        throw new Exception("Failed to create order.");
    }

    $order_id = $conn->insert_id;

    $stmt->close();

    $sql = "INSERT INTO order_items
            (order_id, product_id, seller_id, price, quantity, subtotal)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);


    foreach ($cart_items as $item) {

        $stmt->bind_param(
            "iiidid",
            $order_id,
            $item["product_id"],
            $item["seller_id"],
            $item["price"],
            $item["quantity"],
            $item["subtotal"]
        );

        if (!$stmt->execute()) {

            throw new Exception(
                "Failed to create order item."
            );
        }
    }

    $stmt->close();

    $sql = "UPDATE products
            SET quantity = quantity - ?
            WHERE id = ?
            AND quantity >= ?";

    $stmt = $conn->prepare($sql);


    foreach ($cart_items as $item) {

        $quantity = $item["quantity"];
        $product_id = $item["product_id"];

        $stmt->bind_param(
            "iii",
            $quantity,
            $product_id,
            $quantity
        );

        if (!$stmt->execute() || $stmt->affected_rows === 0) {

            throw new Exception(
                "Unable to update product stock."
            );
        }
    }

    $stmt->close();

    $sql = "DELETE FROM cart_items
            WHERE user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if (!$stmt->execute()) {

        throw new Exception(
            "Failed to clear cart."
        );
    }

    $stmt->close();

    $conn->commit();

    echo json_encode([
        "success" => true,
        "message" => "Order created successfully.",
        "order_id" => $order_id,
        "total_amount" => $total_amount
    ]);

} catch (Exception $e) {

    $conn->rollback();

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}


$conn->close();

?>