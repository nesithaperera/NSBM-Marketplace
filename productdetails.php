<?php

include "config/database.php";

// Check if product ID was sent
if (!isset($_GET["id"])) {
    die("Product not found.");
}

$product_id = $_GET["id"];

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
<html>

<head>

    <title><?php echo htmlspecialchars($product["title"]); ?></title>

</head>

<body>

    <h1>NSBM Marketplace</h1>


    <h2>
        <?php echo htmlspecialchars($product["title"]); ?>
    </h2>


    <?php if (!empty($product["image"]) && $product["image"] != "null") { ?>

        <img
            src="assets/images/products/<?php echo htmlspecialchars($product["image"]); ?>"
            width="250"
            alt="Product Image"
        >

        <br><br>

    <?php } ?>


    <h3>Product Information</h3>


    <p>
        <strong>Category:</strong>
        <?php echo htmlspecialchars($product["category_name"]); ?>
    </p>


    <p>
        <strong>Description:</strong>
        <?php echo nl2br(htmlspecialchars($product["description"])); ?>
    </p>


    <p>
        <strong>Price:</strong>
        Rs. <?php echo number_format($product["price"], 2); ?>
    </p>


    <p>
        <strong>Available Quantity:</strong>
        <?php echo $product["quantity"]; ?>
    </p>


    <p>
        <strong>Location:</strong>
        <?php echo htmlspecialchars($product["location"]); ?>
    </p>


    <h3>Seller Information</h3>


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
        <?php echo htmlspecialchars($product["seller_phone"]); ?>
    </p>


    <hr>


    <h3>Add to Cart</h3>


    <?php if ($product["quantity"] > 0) { ?>

        <label>Quantity:</label>

        <input
            type="number"
            id="cartQuantity"
            value="1"
            min="1"
            max="<?php echo $product["quantity"]; ?>"
        >

        <button onclick="addToCart()">
            Add to Cart
        </button>

        <p id="cartMessage"></p>

    <?php } else { ?>

        <p>This product is out of stock.</p>

    <?php } ?>


    <br>


    <a href="products.php">
        Back to Products
    </a>


    <script>

        function addToCart() {

            var quantity = document.getElementById("cartQuantity").value;

            var productId = <?php echo $product_id; ?>;


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

                var message = document.getElementById("cartMessage");

                message.innerText = data.message;

            })

            .catch(function(error) {

                document.getElementById("cartMessage").innerText =
                    "Something went wrong.";

            });

        }

    </script>


</body>

</html>