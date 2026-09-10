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

    <!-- =========================
         HERO SECTION
    ========================== -->

    <section class="market-hero">

        <div class="market-container">

            <div class="hero-content">

                <span class="hero-badge">
                    🟢 NSBM Student Marketplace
                </span>

                <h1>
                    Buy & Sell Products
                    <span>Within the NSBM Community</span>
                </h1>

                <p>
                    Discover useful products listed by NSBM students,
                    or list your own products and reach other students.
                </p>

                <div class="hero-actions">

                    <a href="products.php" class="hero-primary-btn">
                        🛍️ Browse Products
                    </a>

                    <?php if (!isset($_SESSION["user_id"])) { ?>

                        <a href="register.php" class="hero-secondary-btn">
                            Create Account
                        </a>

                    <?php } elseif ($_SESSION["user_role"] === "admin") { ?>

                        <a href="admin/dashboard.php" class="hero-secondary-btn">
                            ⚙️ Admin Dashboard
                        </a>

                    <?php } else { ?>

                        <a href="user/add-product.php" class="hero-secondary-btn">
                            + Sell a Product
                        </a>

                    <?php } ?>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================
         QUICK ACCESS
    ========================== -->

    <section class="quick-section">

        <div class="market-container">

            <div class="section-heading">

                <span>EXPLORE</span>

                <h2>
                    Everything You Need
                </h2>

                <p>
                    Simple tools to buy, sell and manage your marketplace activity.
                </p>

            </div>


            <div class="quick-grid">


                <!-- Browse Products -->

                <div class="quick-card">

                    <div class="quick-icon">
                        🛍️
                    </div>

                    <h3>
                        Browse Products
                    </h3>

                    <p>
                        Explore approved products listed by students
                        across the NSBM community.
                    </p>

                    <a href="products.php">
                        Explore Products →
                    </a>

                </div>


                <?php if (!isset($_SESSION["user_id"])) { ?>


                    <!-- Register -->

                    <div class="quick-card">

                        <div class="quick-icon">
                            👤
                        </div>

                        <h3>
                            Create an Account
                        </h3>

                        <p>
                            Join the marketplace and start buying
                            or selling products.
                        </p>

                        <a href="register.php">
                            Register Now →
                        </a>

                    </div>


                    <!-- Login -->

                    <div class="quick-card">

                        <div class="quick-icon">
                            🔐
                        </div>

                        <h3>
                            Already a Member?
                        </h3>

                        <p>
                            Login to manage your products,
                            cart and orders.
                        </p>

                        <a href="login.php">
                            Login →
                        </a>

                    </div>


                <?php } elseif ($_SESSION["user_role"] === "admin") { ?>


                    <!-- Admin -->

                    <div class="quick-card">

                        <div class="quick-icon">
                            ⚙️
                        </div>

                        <h3>
                            Admin Panel
                        </h3>

                        <p>
                            Manage users, products, categories,
                            orders and reports.
                        </p>

                        <a href="admin/dashboard.php">
                            Open Dashboard →
                        </a>

                    </div>


                    <!-- Admin Products -->

                    <div class="quick-card">

                        <div class="quick-icon">
                            📦
                        </div>

                        <h3>
                            Manage Products
                        </h3>

                        <p>
                            Review submitted products and
                            manage marketplace listings.
                        </p>

                        <a href="admin/products.php">
                            Manage Products →
                        </a>

                    </div>


                <?php } else { ?>


                    <!-- Add Product -->

                    <div class="quick-card">

                        <div class="quick-icon">
                            📦
                        </div>

                        <h3>
                            Sell a Product
                        </h3>

                        <p>
                            List your product and submit it
                            for admin approval.
                        </p>

                        <a href="user/add-product.php">
                            Add Product →
                        </a>

                    </div>


                    <!-- Cart -->

                    <div class="quick-card">

                        <div class="quick-icon">
                            🛒
                        </div>

                        <h3>
                            Your Cart
                        </h3>

                        <p>
                            Review your selected products
                            and continue to checkout.
                        </p>

                        <a href="user/cart.php">
                            View Cart →
                        </a>

                    </div>


                <?php } ?>


            </div>

        </div>

    </section>


    <!-- =========================
         MARKETPLACE INFO
    ========================== -->

    <section class="market-info">

        <div class="market-container">

            <div class="info-card">

                <div class="info-content">

                    <span class="info-label">
                        NSBM MARKETPLACE
                    </span>

                    <h2>
                        A marketplace built
                        for students.
                    </h2>

                    <p>
                        Find products you need, discover great deals
                        from fellow students, or sell products you
                        no longer need.
                    </p>

                    <a href="products.php" class="info-button">
                        Start Shopping
                    </a>

                </div>


                <div class="info-features">

                    <div>
                        <strong>🛍️</strong>
                        <span>Buy Products</span>
                    </div>

                    <div>
                        <strong>📦</strong>
                        <span>Sell Products</span>
                    </div>

                    <div>
                        <strong>🔐</strong>
                        <span>Secure Accounts</span>
                    </div>

                    <div>
                        <strong>🛒</strong>
                        <span>Easy Checkout</span>
                    </div>

                </div>

            </div>

        </div>

    </section>

</main>


<?php include "includes/footer.php"; ?>


<!-- =========================
     THEME SWITCHER
========================== -->

<script>

function applyTheme(theme) {

    document.documentElement.setAttribute(
        "data-theme",
        theme
    );

    const button =
        document.getElementById("themeToggle");

    if (button) {

        button.textContent =
            theme === "dark"
                ? "☀️ Light Mode"
                : "🌙 Dark Mode";

    }

}


function toggleTheme() {

    const currentTheme =
        document.documentElement.getAttribute("data-theme") || "light";

    const newTheme =
        currentTheme === "dark"
            ? "light"
            : "dark";

    localStorage.setItem(
        "nsbm-theme",
        newTheme
    );

    applyTheme(newTheme);

}


document.addEventListener(
    "DOMContentLoaded",
    function () {

        const savedTheme =
            localStorage.getItem("nsbm-theme") || "light";

        applyTheme(savedTheme);

    }
);

</script>


</body>

</html>