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

    <title>Manage Orders - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <?php include "../includes/header.php"; ?>


    <main class="admin-orders-page">

        <div class="admin-orders-container">


            <!-- =========================================
                 PAGE HEADER
            ========================================== -->

            <div class="admin-orders-header">

                <div>

                    <span class="admin-orders-label">
                        ADMINISTRATION
                    </span>

                    <h1>Manage Orders</h1>

                    <p>
                        View and manage customer orders
                        placed through the marketplace.
                    </p>

                </div>

            </div>


            <!-- =========================================
                 ORDERS TABLE
            ========================================== -->

            <?php if ($result && $result->num_rows > 0) { ?>

                <div class="admin-orders-card">

                    <div class="orders-table-wrapper">

                        <table class="orders-table">

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

                                <?php while (
                                    $order =
                                    $result->fetch_assoc()
                                ) { ?>

                                    <tr>


                                        <!-- ORDER ID -->

                                        <td>

                                            <span class="order-id">

                                                #<?php
                                                echo (int)
                                                    $order["order_id"];
                                                ?>

                                            </span>

                                        </td>


                                        <!-- BUYER -->

                                        <td>

                                            <span class="buyer-name">

                                                <?php
                                                echo htmlspecialchars(
                                                    $order["buyer_name"]
                                                );
                                                ?>

                                            </span>

                                        </td>


                                        <!-- EMAIL -->

                                        <td>

                                            <span class="buyer-email">

                                                <?php
                                                echo htmlspecialchars(
                                                    $order["buyer_email"]
                                                );
                                                ?>

                                            </span>

                                        </td>


                                        <!-- TOTAL -->

                                        <td>

                                            <span class="order-total">

                                                Rs.
                                                <?php
                                                echo number_format(
                                                    (float)
                                                    $order["total_amount"],
                                                    2
                                                );
                                                ?>

                                            </span>

                                        </td>


                                        <!-- STATUS -->

                                        <td>

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

                                        </td>


                                        <!-- DATE -->

                                        <td>

                                            <span class="order-date">

                                                <?php
                                                echo htmlspecialchars(
                                                    $order["created_at"]
                                                );
                                                ?>

                                            </span>

                                        </td>


                                        <!-- ACTION -->

                                        <td>

                                            <a
                                                href="order-details.php?order_id=<?php
                                                    echo (int)
                                                        $order["order_id"];
                                                ?>"
                                                class="view-order-btn"
                                            >
                                                View Details
                                            </a>

                                        </td>

                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            <?php } else { ?>


                <!-- =====================================
                     EMPTY STATE
                ====================================== -->

                <div class="admin-orders-empty">

                    <div class="admin-orders-empty-icon">
                        📦
                    </div>

                    <h2>No Orders Found</h2>

                    <p>
                        There are currently no customer orders
                        in the marketplace.
                    </p>

                </div>

            <?php } ?>


        </div>

    </main>


    <?php include "../includes/footer.php"; ?>


</body>

</html>