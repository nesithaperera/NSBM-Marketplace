<?php
require_once "../includes/admin-auth.php";
require_once "../config/database.php";

$sql = "SELECT 
            products.id,
            products.title,
            products.price,
            products.quantity,
            products.location,
            products.status,
            products.created_at,
            users.name AS seller_name,
            categories.name AS category_name
        FROM products
        INNER JOIN users 
            ON products.user_id = users.id
        INNER JOIN categories 
            ON products.category_id = categories.id
        ORDER BY products.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Products - NSBM Marketplace</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f5f6fa;
        }

        .container {
            width: 95%;
            margin: 30px auto;
        }

        h1 {
            margin-bottom: 20px;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 15px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }

        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .approved {
            background: #d4edda;
            color: #155724;
        }

        .rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .sold {
            background: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>

<body>

<div class="container">

    <a href="dashboard.php" class="back-btn">
        ← Back to Dashboard
    </a>

    <h1>📦 Manage Products</h1>

    <div class="table-container">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Seller</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($result && $result->num_rows > 0): ?>

                <?php while ($product = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo $product["id"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($product["title"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($product["seller_name"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($product["category_name"]); ?>
                        </td>

                        <td>
                            Rs. <?php echo number_format($product["price"], 2); ?>
                        </td>

                        <td>
                            <?php echo $product["quantity"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($product["location"] ?? "N/A"); ?>
                        </td>

                        <td>

                            <span class="status <?php echo htmlspecialchars($product["status"]); ?>">

                                <?php echo ucfirst($product["status"]); ?>

                            </span>

                        </td>

                        <td>
                            <?php echo htmlspecialchars($product["created_at"]); ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="9">
                        No products found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>