<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

$order_id = isset($_GET["order_id"])
    ? intval($_GET["order_id"])
    : 0;

if ($order_id <= 0) {
    header("Location: purchases.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Order Details - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>

        .order-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        .order-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .order-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .order-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0;
            border-bottom: 1px solid #eee;
            gap: 20px;
        }

        .item-info h3 {
            margin: 0 0 8px 0;
        }

        .item-info p {
            margin: 5px 0;
        }

        .item-total {
            font-weight: bold;
            font-size: 18px;
        }

        .order-total {
            text-align: right;
            font-size: 24px;
            font-weight: bold;
            margin-top: 25px;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            background: #e7f5ed;
            color: #006b3c;
            font-weight: bold;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            background: #006b3c;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .empty-message {
            text-align: center;
            padding: 30px;
        }

        @media(max-width: 650px) {

            .order-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .item-total {
                align-self: flex-end;
            }

        }

    </style>

</head>

<body>

<?php include "../includes/header.php"; ?>

<div class="order-container">

    <h1 class="order-title">
        📦 Order Details
    </h1>

    <div class="order-box">

        <div id="orderDetails">
            Loading order...
        </div>

    </div>

</div>


<script>

const orderId = <?php echo $order_id; ?>;


async function loadOrderDetails() {

    try {

        const response =
            await fetch(
                "../api/orders/details.php?order_id=" + orderId
            );

        const data =
            await response.json();

        const container =
            document.getElementById("orderDetails");


        if (!data.success) {

            container.innerHTML = `
                <div class="empty-message">

                    <h2>Unable to load order</h2>

                    <p>${data.message}</p>

                    <a href="purchases.php" class="back-btn">
                        ← Back to My Orders
                    </a>

                </div>
            `;

            return;
        }


        const order = data.order;


        let html = `

            <div class="order-header">

                <h2>
                    Order #${order.id}
                </h2>

                <p>
                    <strong>Date:</strong>
                    ${new Date(order.created_at).toLocaleString()}
                </p>

                <p>
                    <strong>Status:</strong>
                    <span class="status">
                        ${order.status}
                    </span>
                </p>

            </div>

        `;


        data.items.forEach(item => {

            html += `

                <div class="order-item">

                    <div class="item-info">

                        <h3>
                            ${item.title}
                        </h3>

                        <p>
                            Price:
                            Rs. ${Number(item.price).toFixed(2)}
                        </p>

                        <p>
                            Quantity:
                            ${item.quantity}
                        </p>

                    </div>

                    <div class="item-total">

                        Rs. ${Number(item.subtotal).toFixed(2)}

                    </div>

                </div>

            `;

        });


        html += `

            <div class="order-total">

                Total:
                Rs. ${Number(order.total_amount).toFixed(2)}

            </div>

            <a href="purchases.php" class="back-btn">
                ← Back to My Orders
            </a>

        `;


        container.innerHTML = html;


    } catch (error) {

        console.error(error);

        document.getElementById("orderDetails").innerHTML = `

            <div class="empty-message">

                <h2>Something went wrong</h2>

                <p>
                    Unable to load order details.
                </p>

                <a href="purchases.php" class="back-btn">
                    ← Back to My Orders
                </a>

            </div>

        `;

    }

}


loadOrderDetails();

</script>

</body>

</html>