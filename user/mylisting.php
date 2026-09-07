<?php

include "../config/database.php";
include "../includes/auth.php";

$user_id = $_SESSION["user_id"];

// Get products belonging to the logged-in user
$sql = "SELECT p.*, c.name AS category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>
    <title>My Listings</title>
</head>

<body>

    <h1>My Listings</h1>

    <a href="add-product.php">Add New Product</a>

    <br><br>

    <?php

    if ($result->num_rows > 0) {

        while ($product = $result->fetch_assoc()) {

    ?>

            <div>

                <h2>
                    <?php echo htmlspecialchars($product["title"]); ?>
                </h2>

                <p>
                    <strong>Category:</strong>
                    <?php echo htmlspecialchars($product["category_name"]); ?>
                </p>

                <p>
                    <strong>Price:</strong>
                    Rs. <?php echo number_format($product["price"], 2); ?>
                </p>

                <p>
                    <strong>Quantity:</strong>
                    <?php echo $product["quantity"]; ?>
                </p>

                <p>
                    <strong>Location:</strong>
                    <?php echo htmlspecialchars($product["location"]); ?>
                </p>

                <p>
                    <strong>Status:</strong>
                    <?php echo htmlspecialchars($product["status"]); ?>
                </p>

                <a href="edit-product.php?id=<?php echo $product["id"]; ?>">
                    Edit
                </a>

                &nbsp;

                <a href="delete-product.php?id=<?php echo $product["id"]; ?>">
                    Delete
                </a>

                <hr>

            </div>

    <?php

        }

    } else {

        echo "<p>You have not added any products yet.</p>";

    }

    ?>

    <br>

    <a href="../products.php">View Marketplace</a>

</body>

</html>