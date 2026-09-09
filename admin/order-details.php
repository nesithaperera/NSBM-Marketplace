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
        Order #<?php echo $order["id"]; ?> - Order Details
    </title>

</head>


<body>

    <h1>
        Order Details
    </h1>


    <!-- ==================================================
         ADMIN NAVIGATION
    =================================================== -->

    <p>

        <a href="dashboard.php">
            Dashboard
        </a>

        |

        <a href="users.php">
            Users
        </a>

        |

        <a href="products.php">
            Products
        </a>

        |

        <a href="categories.php">
            Categories
        </a>

        |

        <a href="orders.php">
            Orders
        </a>

    </p>


    <hr>


    <!-- ==================================================
         ORDER INFORMATION
    =================================================== -->

    <h2>
        Order #<?php echo (int) $order["id"]; ?>
    </h2>


    <p>
        <strong>Status:</strong>

        <?php
        echo htmlspecialchars(
            ucfirst($order["status"])
        );
        ?>
    </p>


    <p>
        <strong>Order Date:</strong>

        <?php
        echo htmlspecialchars(
            $order["created_at"]
        );
        ?>
    </p>


    <hr>


    <!-- ==================================================
         BUYER INFORMATION
    =================================================== -->

    <h2>
        Buyer Information
    </h2>


    <p>

        <strong>Name:</strong>

        <?php
        echo htmlspecialchars(
            $order["buyer_name"]
        );
        ?>

    </p>


    <p>

        <strong>Email:</strong>

        <?php
        echo htmlspecialchars(
            $order["buyer_email"]
        );
        ?>

    </p>


    <p>

        <strong>Phone:</strong>

        <?php

        if (!empty($order["buyer_phone"])) {

            echo htmlspecialchars(
                $order["buyer_phone"]
            );

        } else {

            echo "Not provided";

        }

        ?>

    </p>


    <hr>


    <!-- ==================================================
         ORDER ITEMS
    =================================================== -->

    <h2>
        Products in This Order
    </h2>


    <?php if ($items_result->num_rows > 0) { ?>

        <table
            border="1"
            cellpadding="10"
            cellspacing="0"
        >

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

                            <?php
                            echo htmlspecialchars(
                                $item["product_title"]
                            );
                            ?>

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
                                (float) $item["price"],
                                2
                            );
                            ?>

                        </td>


                        <!-- QUANTITY -->

                        <td>

                            <?php
                            echo (int) $item["quantity"];
                            ?>

                        </td>


                        <!-- SUBTOTAL -->

                        <td>

                            Rs.

                            <?php
                            echo number_format(
                                (float) $item["subtotal"],
                                2
                            );
                            ?>

                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>


        <br>


        <!-- ==================================================
             TOTAL
        =================================================== -->

        <h2>

            Total:

            Rs.

            <?php
            echo number_format(
                (float) $order["total_amount"],
                2
            );
            ?>

        </h2>


    <?php } else { ?>

        <p>
            No products found for this order.
        </p>

    <?php } ?>


    <br>


    <a href="orders.php">
        ← Back to Orders
    </a>


</body>

</html>

<?php

$items_stmt->close();
$conn->close();

?>