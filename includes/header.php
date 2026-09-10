<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_url = "/NSBM-Marketplace/";

?>

<header class="site-header">

    <div class="nav-container">

        <a href="<?php echo $base_url; ?>index.php" class="logo">
            NSBM Marketplace
        </a>

        <nav>

            <?php if (!isset($_SESSION["user_id"])) { ?>

                <!-- Guest Navigation -->

                <a href="<?php echo $base_url; ?>index.php">
                    Home
                </a>

                <a href="<?php echo $base_url; ?>products.php">
                    Products
                </a>

                <a href="<?php echo $base_url; ?>login.php">
                    Login
                </a>

                <a href="<?php echo $base_url; ?>register.php">
                    Register
                </a>

            <?php } elseif ($_SESSION["user_role"] === "admin") { ?>

                <!-- Admin Navigation -->

                <a href="<?php echo $base_url; ?>admin/dashboard.php">
                    Dashboard
                </a>

                <a href="<?php echo $base_url; ?>admin/users.php">
                    Users
                </a>

                <a href="<?php echo $base_url; ?>admin/products.php">
                    Products
                </a>

                <a href="<?php echo $base_url; ?>admin/categories.php">
                    Categories
                </a>

                <a href="<?php echo $base_url; ?>admin/orders.php">
                    Orders
                </a>

                <a href="<?php echo $base_url; ?>admin/reports.php">
                    Reports
                </a>

                <a href="<?php echo $base_url; ?>logout.php">
                    Logout
                </a>

            <?php } else { ?>

                <!-- User Navigation -->

                <a href="<?php echo $base_url; ?>index.php">
                    Home
                </a>

                <a href="<?php echo $base_url; ?>products.php">
                    Products
                </a>

                <a href="<?php echo $base_url; ?>user/dashboard.php">
                    Dashboard
                </a>

                <a href="<?php echo $base_url; ?>user/add-product.php">
                    Add Product
                </a>

                <a href="<?php echo $base_url; ?>user/mylisting.php">
                    My Listings
                </a>

                <a href="<?php echo $base_url; ?>user/cart.php">
                    🛒 Cart
                </a>

                <a href="<?php echo $base_url; ?>user/purchases.php">
                    My Orders
                </a>

                <a href="<?php echo $base_url; ?>user/profile.php">
                    Profile
                </a>

                <a href="<?php echo $base_url; ?>logout.php">
                    Logout
                </a>

            <?php } ?>

        </nav>

    </div>

</header>