<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<header class="site-header">

    <div class="nav-container">

        <a href="../index.php" class="logo">
            NSBM Marketplace
        </a>

        <nav>

            <?php if (!isset($_SESSION["user_id"])) { ?>

                <!-- Guest Navigation -->

                <a href="../index.php">Home</a>
                <a href="../products.php">Products</a>
                <a href="../login.php">Login</a>
                <a href="../register.php">Register</a>

            <?php } elseif ($_SESSION["user_role"] === "admin") { ?>

                <!-- Admin Navigation -->

                <a href="../admin/dashboard.php">Dashboard</a>
                <a href="../admin/users.php">Users</a>
                <a href="../admin/products.php">Products</a>
                <a href="../admin/categories.php">Categories</a>
                <a href="../admin/orders.php">Orders</a>
                <a href="../admin/reports.php">Reports</a>
                <a href="../logout.php">Logout</a>

            <?php } else { ?>

                <!-- User Navigation -->

                <a href="../index.php">Home</a>
                <a href="../products.php">Products</a>
                <a href="../user/dashboard.php">Dashboard</a>
                <a href="../user/add-product.php">Add Product</a>
                <a href="../user/mylisting.php">My Listings</a>
                <a href="../user/cart.php">🛒 Cart</a>
                <a href="../user/purchases.php">My Orders</a>
                <a href="../user/profile.php">Profile</a>
                <a href="../logout.php">Logout</a>

            <?php } ?>

        </nav>

    </div>

</header>