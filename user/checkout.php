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

    <title>Checkout - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>

        .checkout-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        .checkout-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .checkout-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .checkout-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #ddd;
        }

        .checkout-total {
            text-align: right;
            font-size: 22px;
            font-weight: bold;
            margin-top: 25px;
        }

        .confirm-btn {
            width: 100%;
            padding: 15px;
            margin-top: 20px;
            background: #006b3c;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 17px;
            cursor: pointer;
        }

        .back-btn {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #006b3c;
        }

    </style>

</head>

<body>

<?php include "../includes/header.php"; ?>

<div class="checkout-container">

    <h1 class="checkout-title">Checkout</h1>

    <div class="checkout-box">

        <h2>Order Summary</h2>

        <div id="checkoutItems">
            Loading...
        </div>

        <div id="checkoutTotal"></div>

        <button
            id="confirmButton"
            class="confirm-btn"
            onclick="confirmOrder()"
            style="display:none;">
            Confirm Purchase
        </button>

        <a href="cart.php" class="back-btn">
            ← Back to Cart
        </a>

    </div>

</div>


<script>

let cartData = null;


async function loadCheckout() {

    try {

        const response =
            await fetch("../api/cart/get.php");

        const data =
            await response.json();

        if (!data.success) {

            document.getElementById("checkoutItems").innerHTML =
                "<p>Unable to load cart.</p>";

            return;
        }

        cartData = data;

        if (data.items.length === 0) {

            document.getElementById("checkoutItems").innerHTML = `
                <p>Your cart is empty.</p>
                <a href="../index.php">Continue Shopping</a>
            `;

            return;
        }

        let html = "";

        data.items.forEach(item => {

            html += `
                <div class="checkout-item">

                    <div>
                        <strong>${item.title}</strong>
                        <br>
                        Quantity: ${item.quantity}
                    </div>

                    <div>
                        Rs. ${Number(item.subtotal).toFixed(2)}
                    </div>

                </div>
            `;

        });

        document.getElementById("checkoutItems").innerHTML = html;

        document.getElementById("checkoutTotal").innerHTML = `
            <div class="checkout-total">
                Total: Rs. ${Number(data.total).toFixed(2)}
            </div>
        `;

        document.getElementById("confirmButton").style.display =
            "block";

    } catch (error) {

        console.error(error);

        document.getElementById("checkoutItems").innerHTML =
            "<p>Something went wrong.</p>";
    }
}


async function confirmOrder() {

    const button =
        document.getElementById("confirmButton");

    button.disabled = true;
    button.innerText = "Processing...";

    try {

        const response =
            await fetch("../api/orders/create.php", {

                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify({})
            });

        const data =
            await response.json();

        if (data.success) {

            window.location.href =
                "order-success.php?order_id=" + data.order_id;

        } else {

            alert(data.message);

            button.disabled = false;
            button.innerText = "Confirm Purchase";
        }

    } catch (error) {

        console.error(error);

        alert("Something went wrong while creating your order.");

        button.disabled = false;
        button.innerText = "Confirm Purchase";
    }
}


loadCheckout();

</script>

</body>
</html>