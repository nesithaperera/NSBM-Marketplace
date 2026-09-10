<?php

include "config/database.php";

// Check if product ID was sent
if (!isset($_GET["id"])) {
    die("Product not found.");
}

$product_id = intval($_GET["id"]);

// Get product details
$sql = "SELECT p.*, c.name AS category_name, u.name AS seller_name,
               u.email AS seller_email, u.phone AS seller_phone
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN users u ON p.user_id = u.id
        WHERE p.id = ? AND p.status = 'approved'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($product["title"]); ?>
        - NSBM Marketplace
    </title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<?php include "includes/header.php"; ?>


<main class="product-details-page">

    <!-- Product Details -->

    <section class="product-details-container">


        <!-- Product Image -->

        <div class="product-details-image">

            <?php if (!empty($product["image"]) && $product["image"] != "null") { ?>

                <img
                    src="assets/images/products/<?php echo htmlspecialchars($product["image"]); ?>"
                    alt="<?php echo htmlspecialchars($product["title"]); ?>"
                >

            <?php } else { ?>

                <div class="product-no-image">
                    No Image Available
                </div>

            <?php } ?>

        </div>


        <!-- Product Information -->

        <div class="product-details-info">

            <p class="details-category">
                <?php echo htmlspecialchars($product["category_name"]); ?>
            </p>


            <h1>
                <?php echo htmlspecialchars($product["title"]); ?>
            </h1>


            <p class="details-price">
                Rs. <?php echo number_format($product["price"], 2); ?>
            </p>


            <div class="details-description">

                <h3>Description</h3>

                <p>
                    <?php echo nl2br(htmlspecialchars($product["description"])); ?>
                </p>

            </div>


            <div class="details-info-list">

                <p>
                    <strong>📍 Location:</strong>
                    <?php echo htmlspecialchars($product["location"] ?? "N/A"); ?>
                </p>

                <p>
                    <strong>📦 Available:</strong>
                    <?php echo (int)$product["quantity"]; ?>
                </p>

            </div>


            <!-- Add to Cart -->

            <div class="add-cart-section">

                <h3>Add to Cart</h3>


                <?php if ($product["quantity"] > 0) { ?>

                    <div class="cart-controls">

                        <label for="cartQuantity">
                            Quantity
                        </label>

                        <input
                            type="number"
                            id="cartQuantity"
                            value="1"
                            min="1"
                            max="<?php echo (int)$product["quantity"]; ?>"
                        >

                        <button
                            type="button"
                            onclick="addToCart()"
                            class="add-cart-button"
                        >
                            🛒 Add to Cart
                        </button>

                    </div>


                    <p id="cartMessage" class="cart-message"></p>

                <?php } else { ?>

                    <p class="out-of-stock">
                        This product is currently out of stock.
                    </p>

                <?php } ?>

            </div>

        </div>

    </section>


    <!-- Seller Information -->

    <section class="seller-section">

        <h2>Seller Information</h2>

        <div class="seller-card">

            <p>
                <strong>Seller:</strong>
                <?php echo htmlspecialchars($product["seller_name"]); ?>
            </p>

            <p>
                <strong>Email:</strong>
                <?php echo htmlspecialchars($product["seller_email"]); ?>
            </p>

            <p>
                <strong>Phone:</strong>
                <?php echo htmlspecialchars($product["seller_phone"] ?? "N/A"); ?>
            </p>

        </div>

    </section>


    <!-- Back -->

    <div class="back-products">

        <a href="products.php">
            ← Back to Products
        </a>

    </div>

</main>


<?php include "includes/footer.php"; ?>


<script>

function addToCart() {

    var quantityInput = document.getElementById("cartQuantity");

    var quantity = parseInt(quantityInput.value);

    var productId = <?php echo (int)$product_id; ?>;

    var message = document.getElementById("cartMessage");


    if (quantity < 1) {

        message.innerText = "Please select a valid quantity.";

        return;
    }


    fetch("api/cart/add.php", {

        method: "POST",

        headers: {
            "Content-Type": "application/json"
        },

        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })

    })

    .then(function(response) {
        return response.json();
    })

    .then(function(data) {

        message.innerText = data.message;

    })

    .catch(function(error) {

        message.innerText =
            "Something went wrong. Please try again.";

    });

}

</script>


</body>

</html>