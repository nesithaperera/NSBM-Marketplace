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

    <style>
        .cart-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .cart-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            margin-bottom: 15px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
        }

        .cart-info {
            flex: 1;
        }

        .cart-info h3 {
            margin: 0 0 8px;
        }

        .price {
            font-weight: bold;
            color: #006b3c;
        }

        .quantity-box {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .quantity-box input {
            width: 60px;
            padding: 8px;
            text-align: center;
        }

        .cart-buttons button {
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
        }

        .update-btn {
            background: #006b3c;
            color: white;
        }

        .remove-btn {
            background: #dc3545;
            color: white;
        }

        .cart-summary {
            margin-top: 30px;
            padding: 25px;
            background: #f5f5f5;
            border-radius: 12px;
            text-align: right;
        }

        .checkout-btn {
            display: inline-block;
            background: #006b3c;
            color: white;
            text-decoration: none;
            padding: 13px 25px;
            border-radius: 7px;
            margin-top: 15px;
        }

        .empty-cart {
            text-align: center;
            padding: 50px;
        }

        @media (max-width: 700px) {
            .cart-item {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>

<body>

<?php include "../includes/header.php"; ?>

<div class="cart-container">

    <h1 class="cart-title">🛒 My Shopping Cart</h1>

    <div id="cartItems">
        <p style="text-align:center;">Loading cart...</p>
    </div>

    <div id="cartSummary"></div>

</div>

<script>

async function loadCart() {

    try {

        const response = await fetch("../api/cart/get.php");

        const data = await response.json();

        const cartItems = document.getElementById("cartItems");
        const cartSummary = document.getElementById("cartSummary");

        if (!data.success) {
            cartItems.innerHTML = "<p>Unable to load cart.</p>";
            return;
        }

        if (data.items.length === 0) {

            cartItems.innerHTML = `
                <div class="empty-cart">
                    <h2>Your cart is empty 🛒</h2>
                    <p>Browse products and add something to your cart.</p>

                    <a href="../index.php" class="checkout-btn">
                        Continue Shopping
                    </a>
                </div>
            `;

            cartSummary.innerHTML = "";
            return;
        }

        let html = "";

        data.items.forEach(item => {

            let image = item.image
                ? "../uploads/" + item.image
                : "../assets/images/no-image.png";

            html += `
                <div class="cart-item">

                    <img src="${image}" alt="${item.title}">

                    <div class="cart-info">

                        <h3>${item.title}</h3>

                        <p>
                            Price:
                            <span class="price">
                                Rs. ${Number(item.price).toFixed(2)}
                            </span>
                        </p>

                        <p>
                            Stock available:
                            ${item.available_stock}
                        </p>

                    </div>

                    <div class="quantity-box">

                        <input
                            type="number"
                            id="qty-${item.product_id}"
                            value="${item.quantity}"
                            min="1"
                            max="${item.available_stock}"
                        >

                        <button
                            class="update-btn"
                            onclick="updateQuantity(${item.product_id})">
                            Update
                        </button>

                    </div>

                    <div>

                        <strong>
                            Rs. ${Number(item.subtotal).toFixed(2)}
                        </strong>

                        <br><br>

                        <button
                            class="remove-btn"
                            onclick="removeItem(${item.product_id})">
                            Remove
                        </button>

                    </div>

                </div>
            `;
        });

        cartItems.innerHTML = html;

        cartSummary.innerHTML = `
            <div class="cart-summary">

                <h2>
                    Total:
                    Rs. ${Number(data.total).toFixed(2)}
                </h2>

                <a href="checkout.php" class="checkout-btn">
                    Proceed to Checkout
                </a>

            </div>
        `;

    } catch (error) {

        console.error(error);

        document.getElementById("cartItems").innerHTML =
            "<p>Something went wrong while loading the cart.</p>";
    }
}


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

    } catch (error) {

        console.error(error);
        alert("Unable to update cart.");
    }
}


async function removeItem(productId) {

    if (!confirm("Are you sure you want to remove this product?")) {
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

    } catch (error) {

        console.error(error);
        alert("Unable to remove item.");
    }
}


loadCart();

</script>

</body>
</html>