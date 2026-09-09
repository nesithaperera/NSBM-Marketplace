<?php

require_once "../includes/admin-auth.php";
require_once "../config/database.php";

$sql = "SELECT id, name, description, status, created_at
        FROM categories
        ORDER BY created_at DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Categories - NSBM Marketplace</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f5f6fa;
        }

        .container {
            width: 90%;
            margin: 30px auto;
        }

        h1 {
            margin-bottom: 20px;
        }

        .top-buttons {
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 5px;
        }

        .add-btn {
            background: #198754;
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

        .active {
            background: #d4edda;
            color: #155724;
        }

        .inactive {
            background: #f8d7da;
            color: #721c24;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>📂 Manage Categories</h1>

    <div class="top-buttons">

        <a href="dashboard.php" class="btn">
            ← Dashboard
        </a>

        <a href="add-category.php" class="btn add-btn">
            ➕ Add Category
        </a>

    </div>

    <div class="table-container">

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Name</th>

                    <th>Description</th>

                    <th>Status</th>

                    <th>Created</th>

                    <th>Actions</th>

                </tr>

            </thead>

            <tbody>

            <?php if ($result && $result->num_rows > 0): ?>

                <?php while ($category = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo $category["id"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($category["name"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($category["description"] ?? ""); ?>
                        </td>

                        <td>

                            <span class="status <?php echo htmlspecialchars($category["status"]); ?>">

                                <?php echo ucfirst($category["status"]); ?>

                            </span>

                        </td>

                        <td>
                            <?php echo htmlspecialchars($category["created_at"]); ?>
                        </td>

                        <td>

                            <a
                                href="edit-category.php?id=<?php echo $category["id"]; ?>"
                                class="btn"
                            >
                                Edit
                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>

                    <td colspan="6">
                        No categories found.
                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>