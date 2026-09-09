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

    <style>

        .success-container {
            max-width: 700px;
            margin: 80px auto;
            padding: 20px;
            text-align: center;
        }

        .success-box {
            background: white;
            padding: 50px 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .success-icon {
            font-size: 70px;
            margin-bottom: 20px;
        }

        .success-box h1 {
            color: #006b3c;
        }

        .order-number {
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
        }

        .success-btn {
            display: inline-block;
            padding: 12px 22px;
            margin: 8px;
            border-radius: 7px;
            text-decoration: none;
            background: #006b3c;
            color: white;
        }

        .secondary-btn {
            background: #333;
        }

    </style>

</head>

<body>

<?php include "../includes/header.php"; ?>

<div class="success-container">

    <div class="success-box">

        <div class="success-icon">
            ✅
        </div>

        <h1>
            Order Placed Successfully!
        </h1>

        <p>
            Thank you for shopping at NSBM Marketplace.
        </p>

        <?php if ($order_id > 0): ?>

            <div class="order-number">
                Order #<?php echo htmlspecialchars($order_id); ?>
            </div>

        <?php endif; ?>

        <p>
            Your order has been successfully recorded.
        </p>

        <?php if ($order_id > 0): ?>

            <a
                href="order-details.php?order_id=<?php echo $order_id; ?>"
                class="success-btn">
                View Order
            </a>

        <?php endif; ?>

        <a
            href="purchases.php"
            class="success-btn secondary-btn">
            My Orders
        </a>

        <a
            href="../index.php"
            class="success-btn">
            Continue Shopping
        </a>

    </div>

</div>

</body>
</html>