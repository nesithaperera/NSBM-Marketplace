<?php

include "config/database.php";

$sql = "SELECT p.*, c.name AS category_name, u.name AS seller_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN users u ON p.user_id = u.id
        WHERE p.status = 'approved'
        ORDER BY p.created_at DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>NSBM Marketplace</title>
</head>

<body>

    <h1>NSBM Marketplace</h1>

    <h2>Available Products</h2>

    <?php

    if ($result->num_rows > 0) {

        while ($product = $result->fetch_assoc()) {

    ?>

            <div>
                <h3><?php echo htmlspecialchars($product['title']); ?></h3>

                <p>
                    Category:
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </p>

                <p>
                    Price:
                    Rs. <?php echo number_format($product['price'], 2); ?>
                </p>

                <p>
                    Location:
                    <?php echo htmlspecialchars($product['location']); ?>
                </p>

                <p>
                    Available:
                    <?php echo $product['quantity']; ?>
                </p>

                <p>
                    Seller:
                    <?php echo htmlspecialchars($product['seller_name']); ?>
                </p>

                <a href="productdetails.php?id=<?php echo $product['id']; ?>">
                    View Details
                </a>

                <hr>

            </div>

    <?php

        }

    } else {

        echo "<p>No products available.</p>";

    }

    ?>

</body>
</html>