<?php

require_once "../includes/admin-auth.php";
require_once "../config/database.php";


// ===============================
// DASHBOARD STATISTICS
// ===============================

// Total users
$sql = "SELECT COUNT(*) AS total FROM users";
$result = $conn->query($sql);
$total_users = $result->fetch_assoc()["total"];


// Total products
$sql = "SELECT COUNT(*) AS total FROM products";
$result = $conn->query($sql);
$total_products = $result->fetch_assoc()["total"];


// Pending products
$sql = "SELECT COUNT(*) AS total
        FROM products
        WHERE status = 'pending'";
$result = $conn->query($sql);
$pending_products = $result->fetch_assoc()["total"];


// Approved products
$sql = "SELECT COUNT(*) AS total
        FROM products
        WHERE status = 'approved'";
$result = $conn->query($sql);
$approved_products = $result->fetch_assoc()["total"];


// Total orders
$sql = "SELECT COUNT(*) AS total FROM orders";
$result = $conn->query($sql);
$total_orders = $result->fetch_assoc()["total"];


// Total sales
$sql = "SELECT COALESCE(SUM(total_amount), 0) AS total
        FROM orders
        WHERE status = 'completed'";
$result = $conn->query($sql);
$total_sales = $result->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f7f9;
            margin: 0;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .dashboard-header {
            margin-bottom: 30px;
        }

        .dashboard-header h1 {
            margin-bottom: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(220px, 1fr));

            gap: 20px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .stat-card h3 {
            margin: 0 0 15px 0;
            color: #555;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #006b3c;
        }

        .dashboard-links {
            margin-top: 35px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .dashboard-links a {
            display: inline-block;
            margin: 8px;
            padding: 12px 18px;
            background: #006b3c;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .dashboard-links a:hover {
            opacity: 0.9;
        }

    </style>

</head>

<body>

<?php include "../includes/header.php"; ?>


<div class="dashboard-container">

    <div class="dashboard-header">

        <h1>
            Admin Dashboard
        </h1>

        <p>
            Welcome,
            <strong>
                <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
            </strong>
        </p>

    </div>


    <!-- STATISTICS -->

    <div class="stats-grid">


        <!-- USERS -->

        <div class="stat-card">

            <h3>
                👥 Total Users
            </h3>

            <div class="stat-number">
                <?php echo $total_users; ?>
            </div>

        </div>


        <!-- PRODUCTS -->

        <div class="stat-card">

            <h3>
                📦 Total Products
            </h3>

            <div class="stat-number">
                <?php echo $total_products; ?>
            </div>

        </div>


        <!-- PENDING -->

        <div class="stat-card">

            <h3>
                ⏳ Pending Products
            </h3>

            <div class="stat-number">
                <?php echo $pending_products; ?>
            </div>

        </div>


        <!-- APPROVED -->

        <div class="stat-card">

            <h3>
                ✅ Approved Products
            </h3>

            <div class="stat-number">
                <?php echo $approved_products; ?>
            </div>

        </div>


        <!-- ORDERS -->

        <div class="stat-card">

            <h3>
                🛒 Total Orders
            </h3>

            <div class="stat-number">
                <?php echo $total_orders; ?>
            </div>

        </div>


        <!-- SALES -->

        <div class="stat-card">

            <h3>
                💰 Total Sales
            </h3>

            <div class="stat-number">

                Rs.
                <?php echo number_format($total_sales, 2); ?>

            </div>

        </div>


    </div>


    <!-- ADMIN LINKS -->

   <div class="dashboard-links">

    <h2>
        Admin Management
    </h2>


    <!-- USERS -->

    <a href="users.php">
        👥 Manage Users
    </a>


    <!-- PRODUCTS -->

    <a href="products.php">
        📦 Manage Products
    </a>


    <!-- CATEGORIES -->

    <a href="categories.php">
        🗂️ Manage Categories
    </a>


    <!-- ORDERS -->

    <a href="orders.php">
        🛒 Manage Orders
    </a>


    <!-- REPORTS -->

    <a href="reports.php">
        📊 Reports
    </a>


    <!-- LOGOUT -->

    <a href="../logout.php">
        🚪 Logout
    </a>

</div>

</div>

</body>

</html>