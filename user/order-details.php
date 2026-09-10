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

</head>

<body>

<?php include "../includes/header.php"; ?>


<main class="order-details-page">

    <div class="order-container">

        <div class="order-title">

            <h1>📦 Order Details</h1>

            <p>
                View the details of your purchase.
            </p>

        </div>


        <div class="order-box">

            <div id="orderDetails">

                <div class="order-loading">
                    Loading order details...
                </div>

            </div>

        </div>

    </div>

</main>


<?php include "../includes/footer.php"; ?>


<script>

const orderId = <?php echo $order_id; ?>;


/* ============================= */
/* Load Order Details            */
/* ============================= */

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

                <div class="order-message">

                    <div class="message-icon">
                        ⚠️
                    </div>

                    <h2>Unable to Load Order</h2>

                    <p>
                        ${data.message || "The order could not be found."}
                    </p>

                    <a
                        href="purchases.php"
                        class="order-btn primary-order-btn"
                    >
                        ← Back to My Orders
                    </a>

                </div>

            `;

            return;
        }


        const order = data.order;


        let html = `

            <!-- Order Header -->

            <div class="order-header">

                <div>

                    <span class="order-label">
                        Order ID
                    </span>

                    <h2>
                        #${order.id}
                    </h2>

                </div>


                <div class="order-status ${order.status}">

                    ${order.status}

                </div>

            </div>


            <!-- Order Date -->

            <div class="order-date">

                <strong>Order Date:</strong>

                ${new Date(order.created_at).toLocaleString()}

            </div>


            <!-- Items -->

            <div class="order-items">

                <h2>
                    Ordered Products
                </h2>

        `;


        if (data.items.length === 0) {

            html += `

                <div class="no-order-items">

                    No products found for this order.

                </div>

            `;

        }


        data.items.forEach(item => {

            let image = item.image
                ? "../assets/images/products/" + item.image
                : "../assets/images/no-image.png";


            html += `

                <div class="order-item">

                    <div class="order-product">

                        <div class="order-product-image">

                            <img
                                src="${image}"
                                alt="${item.title}"
                            >

                        </div>


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

                    </div>


                    <div class="item-total">

                        Rs. ${Number(item.subtotal).toFixed(2)}

                    </div>

                </div>

            `;

        });


        html += `

            </div>


            <!-- Total -->

            <div class="order-total">

                <span>
                    Total
                </span>

                <strong>
                    Rs. ${Number(order.total_amount).toFixed(2)}
                </strong>

            </div>


            <!-- Actions -->

            <div class="order-actions">

                <a
                    href="purchases.php"
                    class="order-btn primary-order-btn"
                >
                    ← Back to My Orders
                </a>

                <a
                    href="../products.php"
                    class="order-btn secondary-order-btn"
                >
                    🛍️ Continue Shopping
                </a>

            </div>

        `;


        container.innerHTML = html;

    }

    catch (error) {

        console.error(error);


        document.getElementById("orderDetails").innerHTML = `

            <div class="order-message">

                <div class="message-icon">
                    ⚠️
                </div>

                <h2>Something Went Wrong</h2>

                <p>
                    Unable to load the order details.
                </p>

                <a
                    href="purchases.php"
                    class="order-btn primary-order-btn"
                >
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