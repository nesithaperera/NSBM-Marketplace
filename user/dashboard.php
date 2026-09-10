<?php

require_once "../includes/auth.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>User Dashboard - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php include "../includes/header.php"; ?>


<main class="user-dashboard-page">

    <div class="user-dashboard-container">


        <!-- Welcome Section -->

        <section class="user-dashboard-header">

            <div>

                <p class="dashboard-label">
                    NSBM Marketplace
                </p>

                <h1>
                    Welcome,
                    <?php echo htmlspecialchars($_SESSION["user_name"]); ?>! 👋
                </h1>

                <p>
                    Manage your account, products, cart and orders
                    from your dashboard.
                </p>

            </div>

        </section>


        <!-- Quick Actions -->

        <section class="dashboard-section">

            <h2>
                Quick Actions
            </h2>

            <div class="dashboard-grid">


                <!-- Add Product -->

                <a
                    href="add-product.php"
                    class="dashboard-card"
                >

                    <div class="dashboard-card-icon">
                        ➕
                    </div>

                    <div>

                        <h3>
                            Add Product
                        </h3>

                        <p>
                            List a new product on the marketplace.
                        </p>

                    </div>

                </a>


                <!-- My Listings -->

                <a
                    href="mylisting.php"
                    class="dashboard-card"
                >

                    <div class="dashboard-card-icon">
                        📦
                    </div>

                    <div>

                        <h3>
                            My Listings
                        </h3>

                        <p>
                            Manage the products you have listed.
                        </p>

                    </div>

                </a>


                <!-- Browse Products -->

                <a
                    href="../products.php"
                    class="dashboard-card"
                >

                    <div class="dashboard-card-icon">
                        🛍️
                    </div>

                    <div>

                        <h3>
                            Browse Products
                        </h3>

                        <p>
                            Explore products available in the marketplace.
                        </p>

                    </div>

                </a>


                <!-- Cart -->

                <a
                    href="cart.php"
                    class="dashboard-card"
                >

                    <div class="dashboard-card-icon">
                        🛒
                    </div>

                    <div>

                        <h3>
                            My Cart
                        </h3>

                        <p>
                            View products currently in your cart.
                        </p>

                    </div>

                </a>


                <!-- Orders -->

                <a
                    href="purchases.php"
                    class="dashboard-card"
                >

                    <div class="dashboard-card-icon">
                        🧾
                    </div>

                    <div>

                        <h3>
                            My Orders
                        </h3>

                        <p>
                            View your previous purchases and orders.
                        </p>

                    </div>

                </a>

            </div>

        </section>


        <!-- Account -->

        <section class="dashboard-section">

            <h2>
                My Account
            </h2>

            <div class="account-grid">


                <a
                    href="profile.php"
                    class="account-card"
                >

                    <span>
                        👤
                    </span>

                    <div>

                        <strong>
                            My Profile
                        </strong>

                        <small>
                            View your account information
                        </small>

                    </div>

                </a>


                <a
                    href="edit-profile.php"
                    class="account-card"
                >

                    <span>
                        ✏️
                    </span>

                    <div>

                        <strong>
                            Edit Profile
                        </strong>

                        <small>
                            Update your personal information
                        </small>

                    </div>

                </a>


                <a
                    href="change-password.php"
                    class="account-card"
                >

                    <span>
                        🔒
                    </span>

                    <div>

                        <strong>
                            Change Password
                        </strong>

                        <small>
                            Update your account password
                        </small>

                    </div>

                </a>

            </div>

        </section>


        <!-- Logout -->

        <div class="dashboard-logout">

            <a href="../logout.php">
                🚪 Logout
            </a>

        </div>


    </div>

</main>


<?php include "../includes/footer.php"; ?>


</body>

</html>