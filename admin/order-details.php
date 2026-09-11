<?php

require_once "../includes/admin-auth.php";
require_once "../config/database.php";


// ======================================================
// CHECK ORDER ID
// ======================================================

if (!isset($_GET["order_id"])) {
    die("Order not found.");
}

$order_id = intval($_GET["order_id"]);

if ($order_id <= 0) {
    die("Invalid order ID.");
}


// ======================================================
// GET ORDER INFORMATION
// ======================================================

$order_sql = "SELECT
                  orders.id,
                  orders.buyer_id,
                  orders.total_amount,
                  orders.status,
                  orders.created_at,
                  users.name AS buyer_name,
                  users.email AS buyer_email,
                  users.phone AS buyer_phone
              FROM orders
              INNER JOIN users
                  ON orders.buyer_id = users.id
              WHERE orders.id = ?";

$order_stmt = $conn->prepare($order_sql);

$order_stmt->bind_param(
    "i",
    $order_id
);

$order_stmt->execute();

$order_result = $order_stmt->get_result();


// Check whether order exists

if ($order_result->num_rows == 0) {
    die("Order not found.");
}

$order = $order_result->fetch_assoc();

$order_stmt->close();


// ======================================================
// GET ORDER ITEMS
// ======================================================

$items_sql = "SELECT
                  order_items.id,
                  order_items.product_id,
                  order_items.seller_id,
                  order_items.price,
                  order_items.quantity,
                  order_items.subtotal,
                  products.title AS product_title,
                  users.name AS seller_name
              FROM order_items
              INNER JOIN products
                  ON order_items.product_id = products.id
              INNER JOIN users
                  ON order_items.seller_id = users.id
              WHERE order_items.order_id = ?
              ORDER BY order_items.id ASC";

$items_stmt = $conn->prepare($items_sql);

$items_stmt->bind_param(
    "i",
    $order_id
);

$items_stmt->execute();

$items_result = $items_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Order #<?php echo (int) $order["id"]; ?> - NSBM Marketplace
    </title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <?php include "../includes/header.php"; ?>


    <main class="admin-order-details-page">

        <div class="admin-order-details-container">


            <!-- =========================================
                 PAGE HEADER
            ========================================== -->

            <div class="order-details-header">

                <div>

                    <span class="admin-order-label">
                        ORDER MANAGEMENT
                    </span>

                    <h1>
                        Order #<?php
                        echo (int) $order["id"];
                        ?>
                    </h1>

                    <p>
                        View order information, buyer details,
                        products and update the order status.
                    </p>

                </div>


                <a
                    href="orders.php"
                    class="back-orders-btn"
                >
                    ← Back to Orders
                </a>

            </div>


            <!-- =========================================
                 ORDER SUMMARY
            ========================================== -->

            <div class="order-summary-grid">


                <!-- ORDER STATUS -->

                <div class="order-summary-card">

                    <span class="summary-label">
                        ORDER STATUS
                    </span>

                    <div>

                        <span
                            class="order-status
                            order-status-<?php
                                echo htmlspecialchars(
                                    strtolower(
                                        $order["status"]
                                    )
                                );
                            ?>"
                        >

                            <?php
                            echo htmlspecialchars(
                                ucfirst(
                                    $order["status"]
                                )
                            );
                            ?>

                        </span>

                    </div>

                </div>


                <!-- ORDER DATE -->

                <div class="order-summary-card">

                    <span class="summary-label">
                        ORDER DATE
                    </span>

                    <strong class="summary-value">
                        <?php
                        echo htmlspecialchars(
                            $order["created_at"]
                        );
                        ?>
                    </strong>

                </div>


                <!-- TOTAL -->

                <div class="order-summary-card">

                    <span class="summary-label">
                        TOTAL AMOUNT
                    </span>

                    <strong class="summary-total">
                        Rs.
                        <?php
                        echo number_format(
                            (float) $order["total_amount"],
                            2
                        );
                        ?>
                    </strong>

                </div>

            </div>


            <!-- =========================================
                 STATUS UPDATE
            ========================================== -->

            <section class="admin-detail-card">

                <div class="detail-card-header">

                    <div>

                        <span class="detail-section-label">
                            ORDER MANAGEMENT
                        </span>

                        <h2>
                            Update Order Status
                        </h2>

                    </div>

                </div>


                <form
                    action="update-order-status.php"
                    method="POST"
                    class="order-status-form"
                >

                    <input
                        type="hidden"
                        name="order_id"
                        value="<?php
                            echo (int) $order["id"];
                        ?>"
                    >


                    <div class="status-form-group">

                        <label for="status">
                            Change Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            required
                        >

                            <option
                                value="pending"
                                <?php
                                if (
                                    $order["status"] ===
                                    "pending"
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >
                                Pending
                            </option>


                            <option
                                value="completed"
                                <?php
                                if (
                                    $order["status"] ===
                                    "completed"
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >
                                Completed
                            </option>


                            <option
                                value="cancelled"
                                <?php
                                if (
                                    $order["status"] ===
                                    "cancelled"
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >
                                Cancelled
                            </option>

                        </select>

                    </div>


                    <button
                        type="submit"
                        class="update-status-btn"
                    >
                        Update Status
                    </button>

                </form>

            </section>


            <!-- =========================================
                 BUYER INFORMATION
            ========================================== -->

            <section class="admin-detail-card">

                <div class="detail-card-header">

                    <div>

                        <span class="detail-section-label">
                            CUSTOMER
                        </span>

                        <h2>
                            Buyer Information
                        </h2>

                    </div>

                </div>


                <div class="buyer-info-grid">


                    <div class="buyer-info-item">

                        <span class="buyer-info-label">
                            Name
                        </span>

                        <strong>
                            <?php
                            echo htmlspecialchars(
                                $order["buyer_name"]
                            );
                            ?>
                        </strong>

                    </div>


                    <div class="buyer-info-item">

                        <span class="buyer-info-label">
                            Email
                        </span>

                        <strong>
                            <?php
                            echo htmlspecialchars(
                                $order["buyer_email"]
                            );
                            ?>
                        </strong>

                    </div>


                    <div class="buyer-info-item">

                        <span class="buyer-info-label">
                            Phone
                        </span>

                        <strong>

                            <?php

                            if (
                                !empty(
                                    $order["buyer_phone"]
                                )
                            ) {

                                echo htmlspecialchars(
                                    $order["buyer_phone"]
                                );

                            } else {

                                echo "Not provided";

                            }

                            ?>

                        </strong>

                    </div>

                </div>

            </section>


            <!-- =========================================
                 ORDER ITEMS
            ========================================== -->

            <section class="admin-detail-card">

                <div class="detail-card-header">

                    <div>

                        <span class="detail-section-label">
                            ORDER CONTENTS
                        </span>

                        <h2>
                            Products in This Order
                        </h2>

                    </div>

                </div>


                <?php if ($items_result->num_rows > 0) { ?>


                    <div class="order-items-table-wrapper">

                        <table class="order-items-table">

                            <thead>

                                <tr>

                                    <th>Product</th>

                                    <th>Seller</th>

                                    <th>Price</th>

                                    <th>Quantity</th>

                                    <th>Subtotal</th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php

                                while (
                                    $item =
                                    $items_result->fetch_assoc()
                                ) {

                                ?>

                                    <tr>


                                        <!-- PRODUCT -->

                                        <td>

                                            <strong class="item-product-name">

                                                <?php
                                                echo htmlspecialchars(
                                                    $item[
                                                        "product_title"
                                                    ]
                                                );
                                                ?>

                                            </strong>

                                        </td>


                                        <!-- SELLER -->

                                        <td>

                                            <?php
                                            echo htmlspecialchars(
                                                $item["seller_name"]
                                            );
                                            ?>

                                        </td>


                                        <!-- PRICE -->

                                        <td>

                                            Rs.
                                            <?php
                                            echo number_format(
                                                (float)
                                                $item["price"],
                                                2
                                            );
                                            ?>

                                        </td>


                                        <!-- QUANTITY -->

                                        <td>

                                            <span class="item-quantity">

                                                <?php
                                                echo (int)
                                                    $item["quantity"];
                                                ?>

                                            </span>

                                        </td>


                                        <!-- SUBTOTAL -->

                                        <td>

                                            <strong class="item-subtotal">

                                                Rs.
                                                <?php
                                                echo number_format(
                                                    (float)
                                                    $item["subtotal"],
                                                    2
                                                );
                                                ?>

                                            </strong>

                                        </td>

                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>


                    <!-- =================================
                         ORDER TOTAL
                    ================================== -->

                    <div class="order-total-box">

                        <span>
                            Order Total
                        </span>

                        <strong>

                            Rs.
                            <?php
                            echo number_format(
                                (float)
                                $order["total_amount"],
                                2
                            );
                            ?>

                        </strong>

                    </div>


                <?php } else { ?>

                    <div class="no-order-items">

                        <p>
                            No products found for this order.
                        </p>

                    </div>

                <?php } ?>

            </section>


            <!-- =========================================
                 BACK LINK
            ========================================== -->

            <div class="order-details-footer">

                <a href="orders.php">
                    ← Back to Orders
                </a>

            </div>

        </div>

    </main>


    <?php include "../includes/footer.php"; ?>


</body>

</html>

<?php

$items_stmt->close();

$conn->close();

?>