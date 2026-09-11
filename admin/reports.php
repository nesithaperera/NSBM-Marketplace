<?php

require_once "../includes/admin-auth.php";
require_once "../config/database.php";


// ======================================================
// TOTAL COMPLETED SALES
// ======================================================

$sales_sql = "SELECT
                  COALESCE(SUM(total_amount), 0) AS total_sales
              FROM orders
              WHERE status = 'completed'";

$sales_result = $conn->query($sales_sql);
$sales_data = $sales_result->fetch_assoc();
$total_sales = $sales_data["total_sales"];


// ======================================================
// TOTAL COMPLETED ORDERS
// ======================================================

$orders_sql = "SELECT
                   COUNT(*) AS total_orders
               FROM orders
               WHERE status = 'completed'";

$orders_result = $conn->query($orders_sql);
$orders_data = $orders_result->fetch_assoc();
$total_orders = $orders_data["total_orders"];


// ======================================================
// TOTAL ITEMS SOLD
// ======================================================

$items_sql = "SELECT
                  COALESCE(SUM(order_items.quantity), 0) AS total_items
              FROM order_items
              INNER JOIN orders
                  ON order_items.order_id = orders.id
              WHERE orders.status = 'completed'";

$items_result = $conn->query($items_sql);
$items_data = $items_result->fetch_assoc();
$total_items = $items_data["total_items"];


// ======================================================
// CANCELLED ORDERS
// ======================================================

$cancelled_sql = "SELECT
                      COUNT(*) AS cancelled_orders
                  FROM orders
                  WHERE status = 'cancelled'";

$cancelled_result = $conn->query($cancelled_sql);
$cancelled_data = $cancelled_result->fetch_assoc();
$cancelled_orders = $cancelled_data["cancelled_orders"];


// ======================================================
// PENDING ORDERS
// ======================================================

$pending_sql = "SELECT
                    COUNT(*) AS pending_orders
                FROM orders
                WHERE status = 'pending'";

$pending_result = $conn->query($pending_sql);
$pending_data = $pending_result->fetch_assoc();
$pending_orders = $pending_data["pending_orders"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Sales Reports - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <?php include "../includes/header.php"; ?>


    <main class="admin-reports-page">

        <div class="admin-reports-container">


            <!-- =========================================
                 PAGE HEADER
            ========================================== -->

            <div class="admin-reports-header">

                <span class="admin-reports-label">
                    ADMINISTRATION
                </span>

                <h1>
                    Sales Reports
                </h1>

                <p>
                    View marketplace sales statistics
                    and order performance.
                </p>

            </div>


            <!-- =========================================
                 SALES SUMMARY
            ========================================== -->

            <div class="reports-grid">


                <!-- TOTAL SALES -->

                <div class="report-card report-card-sales">

                    <div class="report-icon">
                        💰
                    </div>

                    <div class="report-info">

                        <span class="report-label">
                            TOTAL SALES
                        </span>

                        <strong class="report-value">
                            Rs.
                            <?php
                            echo number_format(
                                (float) $total_sales,
                                2
                            );
                            ?>
                        </strong>

                        <small>
                            Completed orders only
                        </small>

                    </div>

                </div>


                <!-- COMPLETED ORDERS -->

                <div class="report-card">

                    <div class="report-icon">
                        ✓
                    </div>

                    <div class="report-info">

                        <span class="report-label">
                            COMPLETED ORDERS
                        </span>

                        <strong class="report-value">
                            <?php
                            echo (int) $total_orders;
                            ?>
                        </strong>

                        <small>
                            Successfully completed
                        </small>

                    </div>

                </div>


                <!-- ITEMS SOLD -->

                <div class="report-card">

                    <div class="report-icon">
                        📦
                    </div>

                    <div class="report-info">

                        <span class="report-label">
                            ITEMS SOLD
                        </span>

                        <strong class="report-value">
                            <?php
                            echo (int) $total_items;
                            ?>
                        </strong>

                        <small>
                            Total product quantity
                        </small>

                    </div>

                </div>


                <!-- PENDING ORDERS -->

                <div class="report-card report-card-pending">

                    <div class="report-icon">
                        ⏳
                    </div>

                    <div class="report-info">

                        <span class="report-label">
                            PENDING ORDERS
                        </span>

                        <strong class="report-value">
                            <?php
                            echo (int) $pending_orders;
                            ?>
                        </strong>

                        <small>
                            Awaiting completion
                        </small>

                    </div>

                </div>


                <!-- CANCELLED ORDERS -->

                <div class="report-card report-card-cancelled">

                    <div class="report-icon">
                        ✕
                    </div>

                    <div class="report-info">

                        <span class="report-label">
                            CANCELLED ORDERS
                        </span>

                        <strong class="report-value">
                            <?php
                            echo (int) $cancelled_orders;
                            ?>
                        </strong>

                        <small>
                            Cancelled orders
                        </small>

                    </div>

                </div>

            </div>


            <!-- =========================================
                 REPORT INFORMATION
            ========================================== -->

            <section class="report-information">

                <div class="report-information-header">

                    <span class="admin-reports-label">
                        REPORT DETAILS
                    </span>

                    <h2>
                        Report Information
                    </h2>

                </div>


                <div class="report-information-list">

                    <div class="report-information-item">

                        <span class="report-check">
                            ✓
                        </span>

                        <p>
                            <strong>Total Sales</strong>
                            includes only completed orders.
                        </p>

                    </div>


                    <div class="report-information-item">

                        <span class="report-check">
                            ✓
                        </span>

                        <p>
                            <strong>Completed Orders</strong>
                            shows the number of successfully
                            completed orders.
                        </p>

                    </div>


                    <div class="report-information-item">

                        <span class="report-check">
                            ✓
                        </span>

                        <p>
                            <strong>Items Sold</strong>
                            shows the total quantity of
                            products sold.
                        </p>

                    </div>


                    <div class="report-information-item">

                        <span class="report-check">
                            ✓
                        </span>

                        <p>
                            <strong>Pending Orders</strong>
                            shows orders currently marked
                            as pending.
                        </p>

                    </div>


                    <div class="report-information-item">

                        <span class="report-check">
                            ✓
                        </span>

                        <p>
                            <strong>Cancelled Orders</strong>
                            shows orders marked as cancelled.
                        </p>

                    </div>

                </div>

            </section>


            <!-- =========================================
                 BACK TO DASHBOARD
            ========================================== -->

            <div class="reports-footer-link">

                <a href="dashboard.php">
                    ← Back to Dashboard
                </a>

            </div>

        </div>

    </main>


    <?php include "../includes/footer.php"; ?>


</body>

</html>

<?php

$conn->close();

?>