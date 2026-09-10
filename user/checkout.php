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

</head>

<body>

<?php include "../includes/header.php"; ?>


<main class="checkout-page">

    <div class="checkout-container">

        <!-- Page Header -->

        <div class="checkout-title">

            <h1>Checkout</h1>

            <p>
                Review your order before confirming your purchase.
            </p>

        </div>


        <!-- Order Summary -->

        <div class="checkout-box">

            <div class="checkout-heading">

                <h2>🛒 Order Summary</h2>

            </div>


            <div id="checkoutItems">

                <div class="checkout-loading">
                    Loading order summary...
                </div>

            </div>


            <div id="checkoutTotal"></div>


            <!-- Confirm Order -->

            <div class="confirm-section">

                <button
                    id="confirmButton"
                    class="confirm-btn"
                    onclick="confirmOrder()"
                    style="display:none;"
                >
                    Confirm Purchase
                </button>

                <p class="checkout-note">
                    This is a simulated purchase for the NSBM Marketplace.
                </p>

            </div>


            <div class="checkout-back">

                <a href="cart.php">
                    ← Back to Cart
                </a>

            </div>

        </div>

    </div>

</main>


<?php include "../includes/footer.php"; ?>


<script>

let cartData = null;


/* ============================= */
/* Load Checkout                 */
/* ============================= */

async function loadCheckout() {

    try {

        const response =
            await fetch("../api/cart/get.php");

        const data =
            await response.json();


        if (!data.success) {

            document.getElementById("checkoutItems").innerHTML = `

                <div class="checkout-message">

                    <h3>Unable to load cart</h3>

                    <p>
                        ${data.message || "Please try again."}
                    </p>

                </div>

            `;

            return;
        }


        cartData = data;


        /* Empty Cart */

        if (data.items.length === 0) {

            document.getElementById("checkoutItems").innerHTML = `

                <div class="checkout-message">

                    <div class="empty-checkout-icon">
                        🛒
                    </div>

                    <h3>Your cart is empty</h3>

                    <p>
                        Add some products before checking out.
                    </p>

                    <a href="../products.php" class="continue-shopping-btn">
                        Browse Products
                    </a>

                </div>

            `;

            document.getElementById("checkoutTotal").innerHTML = "";

            return;
        }


        let html = "";


        /* Display Items */

        data.items.forEach(item => {

            let image = item.image
                ? "../assets/images/products/" + item.image
                : "../assets/images/no-image.png";


            html += `

                <div class="checkout-item">

                    <div class="checkout-product">

                        <div class="checkout-image">

                            <img
                                src="${image}"
                                alt="${item.title}"
                            >

                        </div>


                        <div class="checkout-product-info">

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


                    <div class="checkout-subtotal">

                        Rs. ${Number(item.subtotal).toFixed(2)}

                    </div>

                </div>

            `;

        });


        document.getElementById("checkoutItems").innerHTML = html;


        /* Total */

        document.getElementById("checkoutTotal").innerHTML = `

            <div class="checkout-total">

                <span>
                    Total
                </span>

                <strong>
                    Rs. ${Number(data.total).toFixed(2)}
                </strong>

            </div>

        `;


        /* Show Confirm Button */

        document.getElementById("confirmButton").style.display =
            "block";

    }

    catch (error) {

        console.error(error);

        document.getElementById("checkoutItems").innerHTML = `

            <div class="checkout-message">

                <h3>Something went wrong</h3>

                <p>
                    Unable to load your order summary.
                </p>

            </div>

        `;

    }

}


/* ============================= */
/* Confirm Order                 */
/* ============================= */

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

        }

        else {

            alert(data.message);

            button.disabled = false;

            button.innerText = "Confirm Purchase";

        }

    }

    catch (error) {

        console.error(error);

        alert(
            "Something went wrong while creating your order."
        );

        button.disabled = false;

        button.innerText = "Confirm Purchase";

    }

}


/* Load Checkout */

loadCheckout();

</script>


</body>

</html>