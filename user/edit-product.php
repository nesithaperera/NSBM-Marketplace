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

    // Keep the old image
    $image_name = $product["image"];

    // Check required fields
    if ($title == "" || $description == "" || $category_id == "" ||
        $price == "" || $quantity == "" || $location == "") {

        $message = "Please fill in all fields.";

    } elseif (!is_numeric($price) || $price <= 0) {

        $message = "Please enter a valid price.";

    } elseif (!is_numeric($quantity) || $quantity <= 0) {

        $message = "Please enter a valid quantity.";

    } else {

        // Check if a new image was uploaded
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] != 4) {

            if ($_FILES["image"]["error"] == 0) {

                $image_type = strtolower(
                    pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION)
                );

                // Allowed image types
                $allowed_types = ["jpg", "jpeg", "png", "gif"];

                if (!in_array($image_type, $allowed_types)) {

                    $message = "Only JPG, JPEG, PNG and GIF images are allowed.";

                } else {

                    // Create a new image name
                    $image_name = time() . "_" . basename($_FILES["image"]["name"]);

                    $image_path = "../assets/images/products/" . $image_name;

                    // Move new image to folder
                    if (!move_uploaded_file(
                        $_FILES["image"]["tmp_name"],
                        $image_path
                    )) {

                        $message = "Error uploading image.";

                        // Keep old image if upload fails
                        $image_name = $product["image"];
                    }
                }

            } else {

                $message = "There was an error uploading the image.";

            }
        }


        // Update product if there is no error
        if ($message == "") {

            $update_sql = "UPDATE products
                           SET category_id = ?,
                               title = ?,
                               description = ?,
                               price = ?,
                               quantity = ?,
                               image = ?,
                               location = ?,
                               status = 'pending'
                           WHERE id = ? AND user_id = ?";

            $update_stmt = $conn->prepare($update_sql);

            $update_stmt->bind_param(
                "issdissii",
                $category_id,
                $title,
                $description,
                $price,
                $quantity,
                $image_name,
                $location,
                $product_id,
                $user_id
            );

            if ($update_stmt->execute()) {

                $message = "Product updated successfully. It is waiting for admin approval.";

                // Update displayed product information
                $product["title"] = $title;
                $product["description"] = $description;
                $product["category_id"] = $category_id;
                $product["price"] = $price;
                $product["quantity"] = $quantity;
                $product["image"] = $image_name;
                $product["location"] = $location;
                $product["status"] = "pending";

            } else {

                $message = "Error updating product.";

            }

            $update_stmt->close();
        }
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


    <form method="POST" enctype="multipart/form-data">

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


        <label>Product Image:</label>
        <br>

        <?php if (!empty($product["image"]) && $product["image"] != "null") { ?>

            <p>Current Image:</p>

            <img
                src="../assets/images/products/<?php echo htmlspecialchars($product["image"]); ?>"
                width="200"
                alt="Current Product Image"
            >

            <br><br>

        <?php } ?>


        <input
            type="file"
            name="image"
            accept=".jpg,.jpeg,.png,.gif"
        >

        <br>

        <small>
            Leave this empty if you want to keep the current image.
        </small>

        <br><br>


        <button type="submit">
            Update Product
        </button>

    </form>


    <br>

    <a href="mylisting.php">
        Back to My Listings
    </a>

</body>

</html>