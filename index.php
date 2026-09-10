<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>NSBM Marketplace</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<?php include "includes/header.php"; ?>


<main>

    <!-- Hero Section -->

    <section class="hero-section">

        <div class="hero-content">

            <h1>Welcome to NSBM Marketplace</h1>

            <p>
                Buy and sell products within the NSBM community.
            </p>

            <a href="products.php" class="hero-button">
                Browse Products
            </a>

        </div>

    </section>


    <!-- Features Section -->

    <section class="features-section">

        <h2>What You Can Do</h2>

        <div class="features-grid">

            <div class="feature-card">

                <h3>🛍️ Browse Products</h3>

                <p>
                    Explore products listed by students and find what you need.
                </p>

                <a href="products.php">
                    View Products
                </a>

            </div>


            <?php if (!isset($_SESSION["user_id"])) { ?>

                <div class="feature-card">

                    <h3>👤 Create an Account</h3>

                    <p>
                        Register and start using the NSBM Marketplace.
                    </p>

                    <a href="register.php">
                        Register
                    </a>

                </div>


                <div class="feature-card">

                    <h3>🔐 Already Registered?</h3>

                    <p>
                        Login to manage your products, cart and orders.
                    </p>

                    <a href="login.php">
                        Login
                    </a>

                </div>

            <?php } elseif ($_SESSION["user_role"] === "admin") { ?>

                <div class="feature-card">

                    <h3>⚙️ Admin Panel</h3>

                    <p>
                        Manage users, products, categories and orders.
                    </p>

                    <a href="admin/dashboard.php">
                        Admin Dashboard
                    </a>

                </div>

            <?php } else { ?>

                <div class="feature-card">

                    <h3>📦 Sell a Product</h3>

                    <p>
                        Add your own product and make it available in the marketplace.
                    </p>

                    <a href="user/add-product.php">
                        Add Product
                    </a>

                </div>


                <div class="feature-card">

                    <h3>🛒 Your Cart</h3>

                    <p>
                        View your selected products and continue to checkout.
                    </p>

                    <a href="user/cart.php">
                        View Cart
                    </a>

                </div>

            <?php } ?>

        </div>

    </section>

</main>


<?php include "includes/footer.php"; ?>

</body>
</html>