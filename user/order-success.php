<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

$order_id = isset($_GET["order_id"])
    ? intval($_GET["order_id"])
    : 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Order Successful - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php include "../includes/header.php"; ?>


<main class="success-page">

    <div class="success-container">

        <div class="success-box">

            <!-- Success Icon -->

            <div class="success-icon">
                ✓
            </div>


            <!-- Message -->

            <h1>
                Order Placed Successfully!
            </h1>

            <p class="success-message">
                Thank you for shopping at NSBM Marketplace.
                Your order has been successfully recorded.
            </p>


            <!-- Order Number -->

            <?php if ($order_id > 0): ?>

                <div class="order-number">

                    <span>Order ID</span>

                    <strong>
                        #<?php echo htmlspecialchars($order_id); ?>
                    </strong>

                </div>

            <?php endif; ?>


            <!-- Buttons -->

            <div class="success-actions">

                <?php if ($order_id > 0): ?>

                    <a
                        href="order-details.php?order_id=<?php echo $order_id; ?>"
                        class="success-btn primary-btn"
                    >
                        📦 View Order
                    </a>

                <?php endif; ?>


                <a
                    href="purchases.php"
                    class="success-btn secondary-btn"
                >
                    🧾 My Orders
                </a>


                <a
                    href="../products.php"
                    class="success-btn outline-btn"
                >
                    🛍️ Continue Shopping
                </a>

            </div>

        </div>

    </div>

</main>


<?php include "../includes/footer.php"; ?>


</body>

</html>