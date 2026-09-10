<?php

require_once "../includes/auth.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>User Dashboard - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php include "../includes/header.php"; ?>


<div class="dashboard-container">

    <div class="dashboard-header">

        <h1>User Dashboard</h1>

        <h2>
            Welcome,
            <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!
        </h2>

        <p>
            You are logged in successfully.
        </p>

    </div>


    <hr>


    <h3>👤 My Account</h3>

    <p>
        <a href="profile.php">
            My Profile
        </a>
    </p>

    <p>
        <a href="edit-profile.php">
            Edit Profile
        </a>
    </p>

    <p>
        <a href="change-password.php">
            Change Password
        </a>
    </p>


    <hr>


    <h3>🛍️ Marketplace</h3>

    <p>
        <a href="add-product.php">
            ➕ Add Product
        </a>
    </p>

    <p>
        <a href="mylisting.php">
            📦 My Listings
        </a>
    </p>

    <p>
        <a href="../products.php">
            🛍️ Browse Products
        </a>
    </p>

    <p>
        <a href="cart.php">
            🛒 My Cart
        </a>
    </p>

    <p>
        <a href="purchases.php">
            📋 My Orders
        </a>
    </p>


    <hr>


    <p>
        <a href="../logout.php">
            🚪 Logout
        </a>
    </p>

</div>


</body>

</html>