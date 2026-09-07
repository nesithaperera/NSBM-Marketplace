<?php

include "../config/database.php";
include "../includes/auth.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $category_id = $_POST["category_id"];
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];
    $location = trim($_POST["location"]);

    $user_id = $_SESSION["user_id"];

    // Check that required fields are filled
    if ($title == "" || $description == "" || $category_id == "" ||
        $price == "" || $quantity == "" || $location == "") {

        $message = "Please fill in all fields.";

    } elseif (!is_numeric($price) || $price <= 0) {

        $message = "Please enter a valid price.";

    } elseif (!is_numeric($quantity) || $quantity <= 0) {

        $message = "Please enter a valid quantity.";

    } else {

        // Add product to database
        $sql = "INSERT INTO products
                (user_id, category_id, title, description, price, quantity, location, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "iissdis",
            $user_id,
            $category_id,
            $title,
            $description,
            $price,
            $quantity,
            $location
        );

        if ($stmt->execute()) {

            $message = "Product added successfully. Waiting for admin approval.";

        } else {

            $message = "Error adding product.";

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Product</title>
</head>

<body>

    <h1>Add New Product</h1>

    <?php if ($message != "") { ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php } ?>

    <form method="POST">

        <label>Product Title:</label>
        <br>
        <input type="text" name="title" required>

        <br><br>

        <label>Description:</label>
        <br>
        <textarea name="description" rows="5" required></textarea>

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

                <option value="<?php echo $category['id']; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>

            <?php } ?>

        </select>

        <br><br>

        <label>Price:</label>
        <br>
        <input type="number" name="price" step="0.01" min="0.01" required>

        <br><br>

        <label>Quantity:</label>
        <br>
        <input type="number" name="quantity" min="1" required>

        <br><br>

        <label>Location:</label>
        <br>
        <input type="text" name="location" required>

        <br><br>

        <button type="submit">Add Product</button>

    </form>

    <br>

    <a href="../products.php">Back to Products</a>

</body>

</html>