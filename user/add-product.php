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

</head>

<body>

    <h1>Add New Product</h1>

    <?php if ($message != "") { ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php } ?>


    <form
        method="POST"
        enctype="multipart/form-data"
    >

        <label>
            Product Title:
        </label>

        <br>

        <input
            type="text"
            name="title"
            required
        >

        <br><br>


        <label>
            Description:
        </label>

        <br>

        <textarea
            name="description"
            rows="5"
            required
        ></textarea>

        <br><br>


        <label>
            Category:
        </label>

        <br>

        <select
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
                >

                    <?php
                    echo htmlspecialchars($category["name"]);
                    ?>

                </option>

            <?php } ?>

        </select>

        <br><br>


        <label>
            Price:
        </label>

        <br>

        <input
            type="number"
            name="price"
            step="0.01"
            min="0.01"
            required
        >

        <br><br>


        <label>
            Quantity:
        </label>

        <br>

        <input
            type="number"
            name="quantity"
            min="1"
            step="1"
            required
        >

        <br><br>


        <label>
            Location:
        </label>

        <br>

        <input
            type="text"
            name="location"
            required
        >

        <br><br>


        <label>
            Product Image:
        </label>

        <br>

        <input
            type="file"
            name="image"
            accept=".jpg,.jpeg,.png,.gif"
        >

        <br><br>


        <button type="submit">
            Add Product
        </button>

    </form>

    <br>

    <a href="mylisting.php">
        Back to My Listings
    </a>

</body>

</html>