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

    <title>My Cart - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php include "../includes/header.php"; ?>


<main class="cart-page">

    <div class="cart-container">

        <div class="cart-title">

            <h1>🛒 My Shopping Cart</h1>

            <p>Review your products before checkout.</p>

        </div>


        <!-- Cart Items -->

        <div id="cartItems">

            <div class="cart-loading">
                Loading cart...
            </div>

        </div>


        <!-- Cart Summary -->

        <div id="cartSummary"></div>

    </div>

</main>


<?php include "../includes/footer.php"; ?>


<script>

async function loadCart() {

    try {

        const response = await fetch("../api/cart/get.php");

        const data = await response.json();

        const cartItems = document.getElementById("cartItems");
        const cartSummary = document.getElementById("cartSummary");


        if (!data.success) {

            cartItems.innerHTML = `
                <div class="cart-message-box">
                    <h3>Unable to load cart</h3>
                    <p>${data.message || "Please try again."}</p>
                </div>
            `;

            return;
        }


        /* Empty Cart */

        if (data.items.length === 0) {

            cartItems.innerHTML = `

                <div class="empty-cart">

                    <div class="empty-cart-icon">
                        🛒
                    </div>

                    <h2>Your cart is empty</h2>

                    <p>
                        You haven't added any products yet.
                    </p>

                    <a
                        href="../products.php"
                        class="continue-shopping-btn"
                    >
                        Browse Products
                    </a>

                </div>

            `;

            cartSummary.innerHTML = "";

            return;
        }


        let html = "";


        /* Display Cart Items */

        data.items.forEach(item => {

            let image = item.image
                ? "../assets/images/products/" + item.image
                : "../assets/images/no-image.png";


            html += `

                <div class="cart-item">


                    <!-- Product Image -->

                    <div class="cart-image">

                        <img
                            src="${image}"
                            alt="${item.title}"
                        >

                    </div>


                    <!-- Product Information -->

                    <div class="cart-info">

                        <h3>
                            ${item.title}
                        </h3>

                        <p class="cart-price">
                            Rs. ${Number(item.price).toFixed(2)}
                        </p>

                        <p class="stock-info">
                            Available:
                            ${item.available_stock}
                        </p>

                    </div>


                    <!-- Quantity -->

                    <div class="cart-quantity">

                        <label>
                            Quantity
                        </label>

                        <div class="quantity-controls">

                            <input
                                type="number"
                                id="qty-${item.product_id}"
                                value="${item.quantity}"
                                min="1"
                                max="${item.available_stock}"
                            >

                            <button
                                class="update-btn"
                                onclick="updateQuantity(${item.product_id})"
                            >
                                Update
                            </button>

                        </div>

                    </div>


                    <!-- Subtotal / Remove -->

                    <div class="cart-actions">

                        <div class="cart-subtotal">

                            Rs.
                            ${Number(item.subtotal).toFixed(2)}

                        </div>


                        <button
                            class="remove-btn"
                            onclick="removeItem(${item.product_id})"
                        >
                            Remove
                        </button>

                    </div>


                </div>

            `;

        });


        cartItems.innerHTML = html;


        /* Cart Summary */

        cartSummary.innerHTML = `

            <div class="cart-summary">

                <div>

                    <span>
                        Cart Total
                    </span>

                    <strong>
                        Rs. ${Number(data.total).toFixed(2)}
                    </strong>

                </div>


                <div class="cart-summary-buttons">

                    <a
                        href="../products.php"
                        class="continue-shopping-btn"
                    >
                        Continue Shopping
                    </a>

                    <a
                        href="checkout.php"
                        class="checkout-btn"
                    >
                        Proceed to Checkout →
                    </a>

                </div>

            </div>

        `;

    }

    catch (error) {

        console.error(error);

        document.getElementById("cartItems").innerHTML = `

            <div class="cart-message-box">

                <h3>Something went wrong</h3>

                <p>
                    Unable to load your cart. Please try again.
                </p>

            </div>

        `;

    }

}


/* ============================= */
/* Update Quantity               */
/* ============================= */

async function updateQuantity(productId) {

    const quantity =
        document.getElementById("qty-" + productId).value;


    if (quantity < 1) {

        alert("Quantity must be at least 1.");

        return;

    }


    try {

        const response = await fetch("../api/cart/update.php", {

            method: "POST",

            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify({

                product_id: productId,

                quantity: Number(quantity)

            })

        });


        const data = await response.json();


        alert(data.message);


        if (data.success) {

            loadCart();

        }

    }

    catch (error) {

        console.error(error);

        alert("Unable to update cart.");

    }

}


/* ============================= */
/* Remove Item                   */
/* ============================= */

async function removeItem(productId) {

    if (
        !confirm(
            "Are you sure you want to remove this product from your cart?"
        )
    ) {

        return;

    }


    try {

        const response = await fetch("../api/cart/remove.php", {

            method: "POST",

            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify({

                product_id: productId

            })

        });


        const data = await response.json();


        alert(data.message);


        if (data.success) {

            loadCart();

        }

    }

    catch (error) {

        console.error(error);

        alert("Unable to remove item.");

    }

}


/* Load Cart */

loadCart();

</script>


</body>

</html>