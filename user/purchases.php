<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Orders - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>

        .orders-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }

        .orders-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .order-card {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);

            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .order-info h3 {
            margin-top: 0;
        }

        .order-status {
            padding: 6px 12px;
            border-radius: 20px;
            background: #e7f5ed;
            color: #006b3c;
            font-weight: bold;
        }

        .view-btn {
            background: #006b3c;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
        }

        .empty-orders {
            text-align: center;
            padding: 50px;
        }

        @media(max-width: 650px) {

            .order-card {
                flex-direction: column;
                text-align: center;
            }

        }

    </style>

</head>

<body>

<?php include "../includes/header.php"; ?>

<div class="orders-container">

    <h1 class="orders-title">
        📦 My Orders
    </h1>

    <div id="ordersList">
        Loading orders...
    </div>

</div>


<script>

async function loadOrders() {

    try {

        const response =
            await fetch("../api/orders/get.php");

        const data =
            await response.json();

        const ordersList =
            document.getElementById("ordersList");

        if (!data.success) {

            ordersList.innerHTML =
                "<p>Unable to load orders.</p>";

            return;
        }

        if (data.orders.length === 0) {

            ordersList.innerHTML = `
                <div class="empty-orders">

                    <h2>No orders yet 📦</h2>

                    <p>
                        You haven't purchased anything yet.
                    </p>

                    <a href="../index.php" class="view-btn">
                        Start Shopping
                    </a>

                </div>
            `;

            return;
        }

        let html = "";

        data.orders.forEach(order => {

            html += `

                <div class="order-card">

                    <div class="order-info">

                        <h3>
                            Order #${order.order_id}
                        </h3>

                        <p>
                            Date:
                            ${new Date(order.created_at).toLocaleString()}
                        </p>

                        <p>
                            Items:
                            ${order.order_count}
                        </p>

                        <strong>
                            Total:
                            Rs. ${Number(order.total_amount).toFixed(2)}
                        </strong>

                    </div>

                    <div>

                        <p class="order-status">
                            ${order.status}
                        </p>

                        <a
                            href="order-details.php?order_id=${order.order_id}"
                            class="view-btn">
                            View Details
                        </a>

                    </div>

                </div>

            `;

        });

        ordersList.innerHTML = html;

    } catch (error) {

        console.error(error);

        document.getElementById("ordersList").innerHTML =
            "<p>Something went wrong.</p>";
    }
}

loadOrders();

</script>

</body>
</html>