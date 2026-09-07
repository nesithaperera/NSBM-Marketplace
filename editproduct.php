<?php

include "../config/database.php";
include "../includes/auth.php";

$user_id = $_SESSION["user_id"];

// Check if product ID was provided
if (!isset($_GET["id"])) {
    die("Product not found.");
}

$product_id = $_GET["id"];

// Get the product belonging to the logged-in user
$sql = "SELECT * FROM products
        WHERE id = ? AND user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Product not found or you do not have permission to edit this product.");
}

$product = $result->fetch_assoc();

$message = "";

// Update product when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $category_id = $_POST["category_id"];
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];
    $location = trim($_POST["location"]);

    // Check required fields
    if ($title == "" || $description == "" || $category_id == "" ||
        $price == "" || $quantity == "" || $location == "") {

        $message = "Please fill in all fields.";

    } elseif (!is_numeric($price) || $price <= 0) {

        $message = "Please enter a valid price.";

    } elseif (!is_numeric($quantity) || $quantity <= 0) {

        $message = "Please enter a valid quantity.";

    } else {

        // Update the product
        $update_sql = "UPDATE products
                       SET category_id = ?,
                           title = ?,
                           description = ?,
                           price = ?,
                           quantity = ?,
                           location = ?,
                           status = 'pending'
                       WHERE id = ? AND user_id = ?";

        $update_stmt = $conn->prepare($update_sql);

        $update_stmt->bind_param(
            "issdisii",
            $category_id,
            $title,
            $description,
            $price,
            $quantity,
            $location,
            $product_id,
            $user_id
        );

        if ($update_stmt->execute()) {

            $message = "Product updated successfully. It is waiting for admin approval.";

            // Get updated product information
            $product["title"] = $title;
            $product["description"] = $description;
            $product["category_id"] = $category_id;
            $product["price"] = $price;
            $product["quantity"] = $quantity;
            $product["location"] = $location;
            $product["status"] = "pending";

        } else {

            $message = "Error updating product.";

        }

        $update_stmt->close();
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Product</title>
</head>

<body>

    <h1>Edit Product</h1>

    <?php if ($message != "") { ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php } ?>

    <form method="POST">

        <label>Product Title:</label>
        <br>

        <input
            type="text"
            name="title"
            value="<?php echo htmlspecialchars($product["title"]); ?>"
            required
        >

        <br><br>

        <label>Description:</label>
        <br>

        <textarea
            name="description"
            rows="5"
            required
        ><?php echo htmlspecialchars($product["description"]); ?></textarea>

        <br><br>

        <label>Category:</label>
        <br>

        <select name="category_id" required>

            <option value="">Select Category</option>

            <?php

            $category_sql = "SELECT id, name
                             FROM categories
                             WHERE status = 'active'
                             ORDER BY name ASC";

            $category_result = $conn->query($category_sql);

            while ($category = $category_result->fetch_assoc()) {

            ?>

                <option
                    value="<?php echo $category["id"]; ?>"
                    <?php
                    if ($category["id"] == $product["category_id"]) {
                        echo "selected";
                    }
                    ?>
                >
                    <?php echo htmlspecialchars($category["name"]); ?>
                </option>

            <?php } ?>

        </select>

        <br><br>

        <label>Price:</label>
        <br>

        <input
            type="number"
            name="price"
            step="0.01"
            min="0.01"
            value="<?php echo htmlspecialchars($product["price"]); ?>"
            required
        >

        <br><br>

        <label>Quantity:</label>
        <br>

        <input
            type="number"
            name="quantity"
            min="1"
            value="<?php echo htmlspecialchars($product["quantity"]); ?>"
            required
        >

        <br><br>

        <label>Location:</label>
        <br>

        <input
            type="text"
            name="location"
            value="<?php echo htmlspecialchars($product["location"]); ?>"
            required
        >

        <br><br>

        <button type="submit">Update Product</button>

    </form>

    <br>

    <a href="mylisting.php">Back to My Listings</a>

</body>

</html>