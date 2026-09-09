<?php

require_once "../includes/admin-auth.php";
require_once "../config/database.php";


// ======================================================
// ONLY ALLOW POST REQUEST
// ======================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: orders.php");
    exit;
}


// ======================================================
// GET ORDER ID AND STATUS
// ======================================================

$order_id = isset($_POST["order_id"])
    ? intval($_POST["order_id"])
    : 0;

$status = isset($_POST["status"])
    ? trim($_POST["status"])
    : "";


// ======================================================
// VALIDATE ORDER ID
// ======================================================

if ($order_id <= 0) {
    header("Location: orders.php?message=invalid_order");
    exit;
}


// ======================================================
// ALLOWED ORDER STATUSES
// ======================================================

$allowed_statuses = [
    "pending",
    "completed",
    "cancelled"
];


// ======================================================
// VALIDATE STATUS
// ======================================================

if (!in_array($status, $allowed_statuses, true)) {
    header("Location: order-details.php?order_id="
        . $order_id
        . "&message=invalid_status");
    exit;
}


// ======================================================
// CHECK THAT ORDER EXISTS
// ======================================================

$check_sql = "SELECT id
              FROM orders
              WHERE id = ?";

$check_stmt = $conn->prepare($check_sql);

$check_stmt->bind_param(
    "i",
    $order_id
);

$check_stmt->execute();

$check_result = $check_stmt->get_result();

if ($check_result->num_rows == 0) {

    $check_stmt->close();
    $conn->close();

    header("Location: orders.php?message=order_not_found");
    exit;
}

$check_stmt->close();


// ======================================================
// UPDATE ORDER STATUS
// ======================================================

$update_sql = "UPDATE orders
               SET status = ?
               WHERE id = ?";

$update_stmt = $conn->prepare($update_sql);

$update_stmt->bind_param(
    "si",
    $status,
    $order_id
);


if ($update_stmt->execute()) {

    $update_stmt->close();
    $conn->close();

    header(
        "Location: order-details.php?order_id="
        . $order_id
        . "&message=status_updated"
    );

    exit;

} else {

    $update_stmt->close();
    $conn->close();

    header(
        "Location: order-details.php?order_id="
        . $order_id
        . "&message=update_error"
    );

    exit;
}

?>