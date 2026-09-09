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

    <title>Sales Reports</title>

</head>


<body>

    <h1>
        Sales Reports
    </h1>

    <p>
        View marketplace sales statistics.
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

        |

        <a href="reports.php">
            Reports
        </a>

    </p>


    <hr>


    <!-- ==================================================
         SALES SUMMARY
    =================================================== -->

    <h2>
        Sales Summary
    </h2>


    <table
        border="1"
        cellpadding="15"
        cellspacing="0"
    >

        <tr>

            <th>
                Total Sales
            </th>

            <th>
                Completed Orders
            </th>

            <th>
                Items Sold
            </th>

            <th>
                Pending Orders
            </th>

            <th>
                Cancelled Orders
            </th>

        </tr>


        <tr>

            <!-- TOTAL SALES -->

            <td>

                Rs.

                <?php
                echo number_format(
                    (float) $total_sales,
                    2
                );
                ?>

            </td>


            <!-- COMPLETED ORDERS -->

            <td>

                <?php
                echo (int) $total_orders;
                ?>

            </td>


            <!-- ITEMS SOLD -->

            <td>

                <?php
                echo (int) $total_items;
                ?>

            </td>


            <!-- PENDING ORDERS -->

            <td>

                <?php
                echo (int) $pending_orders;
                ?>

            </td>


            <!-- CANCELLED ORDERS -->

            <td>

                <?php
                echo (int) $cancelled_orders;
                ?>

            </td>

        </tr>

    </table>


    <br>

    <hr>


    <!-- ==================================================
         REPORT INFORMATION
    =================================================== -->

    <h2>
        Report Information
    </h2>


    <ul>

        <li>
            Total Sales includes only completed orders.
        </li>

        <li>
            Completed Orders shows the number of
            successfully completed orders.
        </li>

        <li>
            Items Sold shows the total quantity of
            products sold.
        </li>

        <li>
            Pending Orders shows orders currently
            marked as pending.
        </li>

        <li>
            Cancelled Orders shows orders marked
            as cancelled.
        </li>

    </ul>


    <br>


    <a href="dashboard.php">
        ← Back to Dashboard
    </a>


</body>

</html>

<?php

$conn->close();

?>