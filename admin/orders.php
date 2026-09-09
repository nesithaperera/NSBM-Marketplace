<?php

require_once "../includes/admin-auth.php";
require_once "../config/database.php";


// ======================================================
// GET ALL ORDERS
// ======================================================

$sql = "SELECT
            orders.id AS order_id,
            orders.total_amount,
            orders.status,
            orders.created_at,
            users.name AS buyer_name,
            users.email AS buyer_email
        FROM orders
        INNER JOIN users
            ON orders.buyer_id = users.id
        ORDER BY orders.created_at DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manage Orders</title>

</head>

<body>

    <h1>Manage Orders</h1>

    <p>
        View and manage customer orders.
    </p>


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


    <!-- ==================================================
         ORDERS TABLE
    =================================================== -->

    <?php if ($result && $result->num_rows > 0) { ?>

        <table
            border="1"
            cellpadding="10"
            cellspacing="0"
        >

            <thead>

                <tr>

                    <th>Order ID</th>

                    <th>Buyer</th>

                    <th>Email</th>

                    <th>Total Amount</th>

                    <th>Status</th>

                    <th>Order Date</th>

                    <th>Action</th>

                </tr>

            </thead>


            <tbody>

                <?php while ($order = $result->fetch_assoc()) { ?>

                    <tr>

                        <!-- ORDER ID -->

                        <td>
                            #<?php
                            echo (int) $order["order_id"];
                            ?>
                        </td>


                        <!-- BUYER -->

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $order["buyer_name"]
                            );
                            ?>
                        </td>


                        <!-- EMAIL -->

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $order["buyer_email"]
                            );
                            ?>
                        </td>


                        <!-- TOTAL -->

                        <td>
                            Rs.
                            <?php
                            echo number_format(
                                (float) $order["total_amount"],
                                2
                            );
                            ?>
                        </td>


                        <!-- STATUS -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                ucfirst(
                                    $order["status"]
                                )
                            );
                            ?>

                        </td>


                        <!-- DATE -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $order["created_at"]
                            );
                            ?>

                        </td>


                        <!-- ACTION -->

                        <td>

                            <a
                                href="order-details.php?order_id=<?php
                                    echo (int) $order["order_id"];
                                ?>"
                            >
                                View Details
                            </a>

                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    <?php } else { ?>

        <p>
            No orders found.
        </p>

    <?php } ?>


</body>

</html>