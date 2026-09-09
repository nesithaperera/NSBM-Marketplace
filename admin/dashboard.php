<?php

require_once "../includes/admin-auth.php";
require_once "../config/database.php";

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

</head>

<body>

    <h1>NSBM Marketplace - Admin Dashboard</h1>

    <p>
        Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!
    </p>

    <hr>

    <h2>Admin Panel</h2>

    <ul>

        <li>
            <a href="dashboard.php">
                Dashboard
            </a>
        </li>

        <li>
            <a href="users.php">
                Manage Users
            </a>
        </li>

        <li>
            <a href="products.php">
                Manage Products
            </a>
        </li>

        <li>
            <a href="categories.php">
                Manage Categories
            </a>
        </li>

        <li>
            <a href="reports.php">
                Reports
            </a>
        </li>

        <li>
            <a href="../logout.php">
                Logout
            </a>
        </li>

    </ul>

</body>

</html>