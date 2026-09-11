<?php

include "../config/database.php";
include "../includes/auth.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $category_id = $_POST["category_id"] ?? "";
    $price = $_POST["price"] ?? "";
    $quantity = $_POST["quantity"] ?? "";
    $location = trim($_POST["location"] ?? "");

    $user_id = $_SESSION["user_id"];

    $image_name = "";

    // Check that required fields are filled
    if (
        $title == "" ||
        $description == "" ||
        $category_id == "" ||
        $price == "" ||
        $quantity == "" ||
        $location == ""
    ) {

        $message = "Please fill in all fields.";

    } elseif (!is_numeric($price) || $price <= 0) {

        $message = "Please enter a valid price.";

    } elseif (!is_numeric($quantity) || $quantity <= 0 || $quantity != intval($quantity)) {

        $message = "Please enter a valid quantity.";

    } else {

        // Check that selected category is active
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

        $category_check_result = $category_check_stmt->get_result();

        if ($category_check_result->num_rows === 0) {

            $message = "Please select a valid active category.";

        }

        $category_check_stmt->close();


        // Continue only if category is valid
        if ($message == "") {

            // Check if an image was uploaded
            if (
                isset($_FILES["image"]) &&
                $_FILES["image"]["error"] != 4
            ) {

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

                    if (!in_array($image_type, $allowed_types)) {

                        $message = "Only JPG, JPEG, PNG and GIF images are allowed.";

                    } else {

                        // Create unique image name
                        $image_name = time() . "_" .
                            basename($_FILES["image"]["name"]);

                        $image_path =
                            "../assets/images/products/" .
                            $image_name;

                        // Move image to products folder
                        if (
                            !move_uploaded_file(
                                $_FILES["image"]["tmp_name"],
                                $image_path
                            )
                        ) {

                            $message = "Error uploading image.";
                            $image_name = "";

                        }

                    }

                } else {

                    $message = "There was an error uploading the image.";

                }
            }


            // Add product if there is no error
            if ($message == "") {

                $sql = "INSERT INTO products
                        (
                            user_id,
                            category_id,
                            title,
                            description,
                            price,
                            quantity,
                            image,
                            location,
                            status
                        )
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

                $stmt = $conn->prepare($sql);

                $stmt->bind_param(
                    "iissdiss",
                    $user_id,
                    $category_id,
                    $title,
                    $description,
                    $price,
                    $quantity,
                    $image_name,
                    $location
                );

                if ($stmt->execute()) {

                    $message =
                        "Product added successfully. Waiting for admin approval.";

                } else {

                    $message = "Error adding product.";

                }

                $stmt->close();
            }
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

    <title>Add Product - NSBM Marketplace</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

    <?php include "../includes/header.php"; ?>

    <main class="product-form-page">

        <div class="product-form-container">

            <div class="product-form-header">
                <span class="form-label">SELL ON NSBM MARKETPLACE</span>

                <h1>Add New Product</h1>

                <p>
                    Create a product listing and make it available
                    to the NSBM community.
                </p>
            </div>


            <?php if ($message != "") { ?>

                <div class="form-message">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php } ?>


            <form
                method="POST"
                enctype="multipart/form-data"
                class="product-form"
            >

                <div class="form-group">

                    <label for="title">
                        Product Title
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        placeholder="Enter product title"
                        value="<?php echo htmlspecialchars($_POST["title"] ?? ""); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        placeholder="Describe your product..."
                        required
                    ><?php echo htmlspecialchars($_POST["description"] ?? ""); ?></textarea>

                </div>


                <div class="form-group">

                    <label for="category_id">
                        Category
                    </label>

                    <select
                        id="category_id"
                        name="category_id"
                        required
                    >

                        <option value="">
                            Select Category
                        </option>

                        <?php

                        $category_result = $conn->query(
                            "SELECT id, name
                             FROM categories
                             WHERE status = 'active'
                             ORDER BY name ASC"
                        );

                        while (
                            $category = $category_result->fetch_assoc()
                        ) {

                        ?>

                            <option
                                value="<?php echo $category["id"]; ?>"
                                <?php
                                if (
                                    isset($_POST["category_id"]) &&
                                    $_POST["category_id"] == $category["id"]
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >

                                <?php
                                echo htmlspecialchars($category["name"]);
                                ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="price">
                            Price (LKR)
                        </label>

                        <input
                            type="number"
                            id="price"
                            name="price"
                            step="0.01"
                            min="0.01"
                            placeholder="0.00"
                            value="<?php echo htmlspecialchars($_POST["price"] ?? ""); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="quantity">
                            Quantity
                        </label>

                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            min="1"
                            step="1"
                            placeholder="1"
                            value="<?php echo htmlspecialchars($_POST["quantity"] ?? ""); ?>"
                            required
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label for="location">
                        Location
                    </label>

                    <input
                        type="text"
                        id="location"
                        name="location"
                        placeholder="Enter your location"
                        value="<?php echo htmlspecialchars($_POST["location"] ?? ""); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="image">
                        Product Image
                    </label>

                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept=".jpg,.jpeg,.png,.gif"
                    >

                    <small class="form-help">
                        JPG, JPEG, PNG or GIF images are allowed.
                    </small>

                </div>


                <div class="form-actions">

                    <button
                        type="submit"
                        class="form-submit-btn"
                    >
                        Add Product
                    </button>

                    <a
                        href="mylisting.php"
                        class="form-cancel-btn"
                    >
                        Back to My Listings
                    </a>

                </div>

            </form>

        </div>

    </main>

    <?php include "../includes/footer.php"; ?>

</body>

</html>