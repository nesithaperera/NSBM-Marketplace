<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

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
            margin: 50px auto;
            padding: 0 20px;
        }

        .orders-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .orders-header h1 {
            margin-bottom: 8px;
        }

        .orders-header p {
            color: #666;
            margin: 0;
        }

        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .order-card {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);

            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 25px;
        }

        .order-info {
            flex: 1;
        }

        .order-info h3 {
            margin: 0 0 12px;
        }

        .order-info p {
            margin: 7px 0;
            color: #666;
        }

        .order-total {
            margin-top: 12px;
            font-size: 17px;
            color: #006b3c;
        }

        .order-actions {
            min-width: 150px;
            text-align: center;
        }

        .order-status {
            display: inline-block;
            padding: 6px 13px;
            border-radius: 20px;
            font-weight: 600;
            margin: 0 0 12px;
            text-transform: capitalize;
        }

        .status-completed {
            background: #e8f5ee;
            color: #006b3c;
        }

        .status-pending {
            background: #fff4d6;
            color: #8a5a00;
        }

        .status-cancelled {
            background: #fff0f0;
            color: #b42318;
        }

        .view-btn {
            display: inline-block;
            background: #006b3c;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 7px;
            font-weight: 600;
        }

        .view-btn:hover {
            opacity: 0.9;
        }

        .empty-orders {
            background: #ffffff;
            text-align: center;
            padding: 60px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .empty-orders h2 {
            margin-bottom: 10px;
        }

        .empty-orders p {
            color: #666;
            margin-bottom: 25px;
        }

        .loading-message {
            text-align: center;
            padding: 30px;
            color: #666;
        }

        .error-message {
            background: #fff3f3;
            color: #b42318;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        @media (max-width: 650px) {

            .orders-container {
                margin: 30px auto;
            }

            .order-card {
                flex-direction: column;
                text-align: center;
            }

            .order-actions {
                width: 100%;
            }

            .view-btn {
                width: 100%;
                box-sizing: border-box;
            }

        }

    </style>

</head>

<body>

<?php include "../includes/header.php"; ?>


<main class="orders-container">

    <div class="orders-header">

        <h1>📦 My Orders</h1>

        <p>
            View your previous purchases and order details.
        </p>

    </div>


    <div id="ordersList" class="orders-list">

        <div class="loading-message">
            Loading orders...
        </div>

    </div>

</main>


<?php include "../includes/footer.php"; ?>


<script>

async function loadOrders() {

    const ordersList =
        document.getElementById("ordersList");

    try {

        const response =
            await fetch("../api/orders/get.php");

        const data =
            await response.json();


        if (!data.success) {

            ordersList.innerHTML = `
                <div class="error-message">
                    Unable to load your orders.
                </div>
            `;

            return;
        }


        if (!data.orders || data.orders.length === 0) {

            ordersList.innerHTML = `

                <div class="empty-orders">

                    <h2>No orders yet 📦</h2>

                    <p>
                        You haven't purchased anything yet.
                    </p>

                    <a
                        href="../products.php"
                        class="view-btn">
                        Start Shopping
                    </a>

                </div>

            `;

            return;
        }


        let html = "";


        data.orders.forEach(order => {

            const status =
                String(order.status || "").toLowerCase();

            let statusClass = "status-pending";

            if (status === "completed") {
                statusClass = "status-completed";
            } else if (status === "cancelled") {
                statusClass = "status-cancelled";
            }


            const orderDate =
                new Date(order.created_at)
                    .toLocaleString();


            const orderCount =
                Number(order.order_count || 0);


            const total =
                Number(order.total_amount || 0);


            html += `

                <div class="order-card">

                    <div class="order-info">

                        <h3>
                            Order #${order.order_id}
                        </h3>

                        <p>
                            <strong>Date:</strong>
                            ${orderDate}
                        </p>

                        <p>
                            <strong>Items:</strong>
                            ${orderCount}
                        </p>

                        <p class="order-total">
                            <strong>
                                Total:
                                Rs. ${total.toFixed(2)}
                            </strong>
                        </p>

                    </div>


                    <div class="order-actions">

                        <p class="order-status ${statusClass}">
                            ${status || "Unknown"}
                        </p>

                        <br>

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

        ordersList.innerHTML = `

            <div class="error-message">
                Something went wrong while loading your orders.
            </div>

        `;
    }
}


loadOrders();

</script>

</body>

</html>