<?php

require_once "../includes/auth.php";

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
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }

        .cart-container h1 {
            margin-bottom: 25px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fff;
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-info h3 {
            margin: 0 0 10px;
        }

        .cart-item-info p {
            margin: 5px 0;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .quantity-control input {
            width: 60px;
            padding: 8px;
            text-align: center;
        }

        .cart-actions button {
            padding: 8px 12px;
            margin-left: 5px;
            cursor: pointer;
        }

        .cart-summary {
            margin-top: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f8f8f8;
            text-align: right;
        }

        .cart-summary h2 {
            margin-bottom: 15px;
        }

        .checkout-btn {
            display: inline-block;
            padding: 12px 20px;
            background: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 10px;
        }

        .empty-cart {
            text-align: center;
            padding: 50px 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
            display: none;
        }
    </style>
</head>

<body>

<div class="cart-container">

    <h1>My Cart</h1>

    <div id="message" class="message"></div>

    <div id="cart-items">
        <p>Loading cart...</p>
    </div>

    <div id="cart-summary" class="cart-summary" style="display: none;">
        <h2>Total: Rs. <span id="cart-total">0.00</span></h2>
        <p>Items: <span id="item-count">0</span></p>

        <a href="checkout.php" class="checkout-btn">
            Proceed to Checkout
        </a>
    </div>

</div>


<script>

const cartItemsContainer = document.getElementById("cart-items");
const cartSummary = document.getElementById("cart-summary");
const cartTotal = document.getElementById("cart-total");
const itemCount = document.getElementById("item-count");
const messageBox = document.getElementById("message");


function showMessage(message, success = true) {

    messageBox.textContent = message;
    messageBox.style.display = "block";

    if (success) {
        messageBox.style.background = "#d4edda";
        messageBox.style.color = "#155724";
    } else {
        messageBox.style.background = "#f8d7da";
        messageBox.style.color = "#721c24";
    }
}


async function loadCart() {

    try {

        const response = await fetch("../api/cart/get.php");

        const data = await response.json();

        if (!data.success) {
            showMessage(data.message || "Unable to load cart.", false);
            return;
        }

        displayCart(data);

    } catch (error) {

        console.error(error);

        showMessage("Something went wrong while loading the cart.", false);
    }
}


function displayCart(data) {

    cartItemsContainer.innerHTML = "";

    if (!data.items || data.items.length === 0) {

        cartItemsContainer.innerHTML = `
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>Add some products to your cart to continue.</p>
                <a href="../products.php">Browse Products</a>
            </div>
        `;

        cartSummary.style.display = "none";

        return;
    }


    data.items.forEach(item => {

        const itemElement = document.createElement("div");

        itemElement.className = "cart-item";


        let imagePath = "../assets/images/";

        if (item.image) {
            imagePath += item.image;
        } else {
            imagePath += "default-product.png";
        }


        itemElement.innerHTML = `

            <img
                src="${imagePath}"
                alt="${escapeHtml(item.title)}"
                onerror="this.src='../assets/images/default-product.png'"
            >

            <div class="cart-item-info">

                <h3>${escapeHtml(item.title)}</h3>

                <p>
                    Price: Rs. ${Number(item.price).toFixed(2)}
                </p>

                <p>
                    Subtotal:
                    <strong>
                        Rs. ${Number(item.subtotal).toFixed(2)}
                    </strong>
                </p>

                <div class="quantity-control">

                    <label>Quantity:</label>

                    <input
                        type="number"
                        min="1"
                        max="${item.available_stock}"
                        value="${item.quantity}"
                        id="quantity-${item.cart_item_id}"
                    >

                    <button
                        onclick="updateQuantity(${item.cart_item_id})"
                    >
                        Update
                    </button>

                </div>

            </div>


            <div class="cart-actions">

                <button
                    onclick="removeItem(${item.cart_item_id})"
                >
                    Remove
                </button>

            </div>

        `;


        cartItemsContainer.appendChild(itemElement);

    });


    cartTotal.textContent = Number(data.total).toFixed(2);
    itemCount.textContent = data.item_count;

    cartSummary.style.display = "block";
}


async function updateQuantity(cartItemId) {

    const input = document.getElementById(
        `quantity-${cartItemId}`
    );

    const quantity = parseInt(input.value);


    if (!quantity || quantity < 1) {

        showMessage("Please enter a valid quantity.", false);

        return;
    }


    try {

        const response = await fetch("../api/cart/update.php", {

            method: "POST",

            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify({
                cart_item_id: cartItemId,
                quantity: quantity
            })

        });


        const data = await response.json();


        if (!data.success) {

            showMessage(
                data.message || "Unable to update quantity.",
                false
            );

            return;
        }


        showMessage("Cart updated successfully.");

        loadCart();


    } catch (error) {

        console.error(error);

        showMessage(
            "Something went wrong while updating the cart.",
            false
        );
    }
}


async function removeItem(cartItemId) {

    if (!confirm("Are you sure you want to remove this item?")) {
        return;
    }


    try {

        const response = await fetch("../api/cart/remove.php", {

            method: "POST",

            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify({
                cart_item_id: cartItemId
            })

        });


        const data = await response.json();


        if (!data.success) {

            showMessage(
                data.message || "Unable to remove item.",
                false
            );

            return;
        }


        showMessage("Item removed from cart.");

        loadCart();


    } catch (error) {

        console.error(error);

        showMessage(
            "Something went wrong while removing the item.",
            false
        );
    }
}


function escapeHtml(text) {

    const div = document.createElement("div");

    div.textContent = text;

    return div.innerHTML;
}


loadCart();

</script>

</body>
</html>