<?php

include "../config/database.php";
include "../includes/auth.php";

$user_id = $_SESSION["user_id"];

// Check if product ID was provided
if (!isset($_GET["id"])) {
    die("Product not found.");
}

$product_id = intval($_GET["id"]);

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

$stmt->close();

$message = "";


// ======================================================
// UPDATE PRODUCT
// ======================================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $category_id = $_POST["category_id"] ?? "";
    $price = $_POST["price"] ?? "";
    $quantity = $_POST["quantity"] ?? "";
    $location = trim($_POST["location"] ?? "");

    // Keep the old image
    $image_name = $product["image"];

    // ==================================================
    // CHECK REQUIRED FIELDS
    // ==================================================

    if (
        $title == "" ||
        $description == "" ||
        $category_id == "" ||
        $price == "" ||
        $quantity == "" ||
        $location == ""
    ) {

        $message = "Please fill in all fields.";

    }

    // ==================================================
    // CHECK PRICE
    // ==================================================

    elseif (!is_numeric($price) || $price <= 0) {

        $message = "Please enter a valid price.";

    }

    // ==================================================
    // CHECK QUANTITY
    // ==================================================

    elseif (
        !is_numeric($quantity) ||
        $quantity <= 0 ||
        $quantity != intval($quantity)
    ) {

        $message = "Please enter a valid quantity.";

    }

    else {

        // Convert values to correct types
        $category_id = intval($category_id);
        $price = floatval($price);
        $quantity = intval($quantity);


        // ==================================================
        // CHECK CATEGORY
        // Only ACTIVE categories can be selected
        // ==================================================

        $category_check_sql = "SELECT id
                               FROM categories
                               WHERE id = ?
                               AND status = 'active'";

        $category_check_stmt = $conn->prepare($category_check_sql);

        $category_check_stmt->bind_param(
            "i",
            $category_id
        );

        $category_check_stmt->execute();

        $category_check_result =
            $category_check_stmt->get_result();

        if ($category_check_result->num_rows == 0) {

            $message = "Please select a valid active category.";

        }

        $category_check_stmt->close();


        // ==================================================
        // IMAGE UPLOAD
        // ==================================================

        if ($message == "") {

            // Check if a new image was uploaded
            if (
                isset($_FILES["image"]) &&
                $_FILES["image"]["error"] != 4
            ) {

                // Check upload error
                if ($_FILES["image"]["error"] == 0) {

                    $image_type = strtolower(
                        pathinfo(
                            $_FILES["image"]["name"],
                            PATHINFO_EXTENSION
                        )
                    );

                    // Allowed image types
                    $allowed_types = [
                        "jpg",
                        "jpeg",
                        "png",
                        "gif"
                    ];

                    // Check image extension
                    if (!in_array($image_type, $allowed_types)) {

                        $message =
                            "Only JPG, JPEG, PNG and GIF images are allowed.";

                    }

                    else {

                        // Create a unique image name
                        $image_name =
                            time() . "_" .
                            basename($_FILES["image"]["name"]);

                        $image_path =
                            "../assets/images/products/" .
                            $image_name;


                        // Move uploaded image
                        if (
                            !move_uploaded_file(
                                $_FILES["image"]["tmp_name"],
                                $image_path
                            )
                        ) {

                            $message =
                                "Error uploading image.";

                            // Keep old image if upload fails
                            $image_name = $product["image"];
                        }
                    }

                }

                else {

                    $message =
                        "There was an error uploading the image.";

                }
            }
        }


        // ==================================================
        // UPDATE DATABASE
        // ==================================================

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
                           WHERE id = ?
                           AND user_id = ?";

            $update_stmt =
                $conn->prepare($update_sql);

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

                $message =
                    "Product updated successfully. " .
                    "It is waiting for admin approval.";


                // Update displayed product information
                $product["title"] = $title;
                $product["description"] = $description;
                $product["category_id"] = $category_id;
                $product["price"] = $price;
                $product["quantity"] = $quantity;
                $product["image"] = $image_name;
                $product["location"] = $location;
                $product["status"] = "pending";

            }

            else {

                $message =
                    "Error updating product.";

            }

            $update_stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Product</title>

</head>


<body>

    <h1>Edit Product</h1>


    <!-- ==============================================
         MESSAGE
    =============================================== -->

    <?php if ($message != "") { ?>

        <p>
            <?php
            echo htmlspecialchars($message);
            ?>
        </p>

    <?php } ?>


    <!-- ==============================================
         EDIT PRODUCT FORM
    =============================================== -->

    <form
        method="POST"
        enctype="multipart/form-data"
    >


        <!-- PRODUCT TITLE -->

        <label>Product Title:</label>

        <br>

        <input
            type="text"
            name="title"
            value="<?php
                echo htmlspecialchars($product["title"]);
            ?>"
            required
        >

        <br><br>


        <!-- DESCRIPTION -->

        <label>Description:</label>

        <br>

        <textarea
            name="description"
            rows="5"
            required
        ><?php
            echo htmlspecialchars($product["description"]);
        ?></textarea>

        <br><br>


        <!-- CATEGORY -->

        <label>Category:</label>

        <br>

        <select
            name="category_id"
            required
        >

            <option value="">
                Select Category
            </option>


            <?php

            $category_sql = "SELECT id, name
                             FROM categories
                             WHERE status = 'active'
                             ORDER BY name ASC";

            $category_result =
                $conn->query($category_sql);


            while (
                $category =
                $category_result->fetch_assoc()
            ) {

            ?>

                <option
                    value="<?php
                        echo $category["id"];
                    ?>"
                    <?php

                    if (
                        $category["id"] ==
                        $product["category_id"]
                    ) {

                        echo "selected";
                    }

                    ?>
                >

                    <?php
                    echo htmlspecialchars(
                        $category["name"]
                    );
                    ?>

                </option>

            <?php } ?>

        </select>

        <br><br>


        <!-- PRICE -->

        <label>Price:</label>

        <br>

        <input
            type="number"
            name="price"
            step="0.01"
            min="0.01"
            value="<?php
                echo htmlspecialchars($product["price"]);
            ?>"
            required
        >

        <br><br>


        <!-- QUANTITY -->

        <label>Quantity:</label>

        <br>

        <input
            type="number"
            name="quantity"
            min="1"
            step="1"
            value="<?php
                echo htmlspecialchars($product["quantity"]);
            ?>"
            required
        >

        <br><br>


        <!-- LOCATION -->

        <label>Location:</label>

        <br>

        <input
            type="text"
            name="location"
            value="<?php
                echo htmlspecialchars($product["location"]);
            ?>"
            required
        >

        <br><br>


        <!-- PRODUCT IMAGE -->

        <label>Product Image:</label>

        <br>


        <?php

        if (
            !empty($product["image"]) &&
            $product["image"] != "null"
        ) {

        ?>

            <p>Current Image:</p>

            <img
                src="../assets/images/products/<?php
                    echo htmlspecialchars(
                        $product["image"]
                    );
                ?>"
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
            Leave this empty if you want to keep
            the current image.
        </small>

        <br><br>


        <!-- SUBMIT -->

        <button type="submit">
            Update Product
        </button>

    </form>


    <br>


    <!-- BACK -->

    <a href="mylisting.php">
        Back to My Listings
    </a>


</body>

</html>