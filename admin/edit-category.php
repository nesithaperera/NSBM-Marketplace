<?php

require_once "../includes/admin-auth.php";
require_once "../config/database.php";

$category_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($category_id <= 0) {
    header("Location: categories.php");
    exit;
}

// Get category
$sql = "SELECT id, name, description, status
        FROM categories
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: categories.php");
    exit;
}

$category = $result->fetch_assoc();
$stmt->close();

$message = "";
$message_type = "";

$name = $category["name"];
$description = $category["description"];
$status = $category["status"];

// Update category
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status = $_POST["status"] ?? "active";

    if ($name === "") {

        $message = "Category name is required.";
        $message_type = "error";

    } elseif (!in_array($status, ["active", "inactive"])) {

        $message = "Invalid category status.";
        $message_type = "error";

    } else {

        // Check duplicate category name
        $check_sql = "SELECT id
                      FROM categories
                      WHERE name = ?
                      AND id != ?";

        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $name, $category_id);
        $check_stmt->execute();

        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {

            $message = "Another category with this name already exists.";
            $message_type = "error";

        } else {

            $update_sql = "UPDATE categories
                           SET name = ?,
                               description = ?,
                               status = ?
                           WHERE id = ?";

            $update_stmt = $conn->prepare($update_sql);

            $update_stmt->bind_param(
                "sssi",
                $name,
                $description,
                $status,
                $category_id
            );

            if ($update_stmt->execute()) {

                $message = "Category updated successfully.";
                $message_type = "success";

            } else {

                $message = "Failed to update category.";
                $message_type = "error";
            }

            $update_stmt->close();
        }

        $check_stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Category - NSBM Marketplace</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            margin: 0;
        }

        .container {
            width: 500px;
            max-width: 90%;
            margin: 50px auto;
        }

        .form-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h1 {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        textarea {
            height: 120px;
            resize: vertical;
        }

        button {
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            background: #198754;
            color: white;
            cursor: pointer;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #333;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

    </style>

</head>

<body>

<div class="container">

    <a href="categories.php" class="back-btn">
        ← Back to Categories
    </a>

    <div class="form-box">

        <h1>✏️ Edit Category</h1>

        <?php if ($message !== ""): ?>

            <div class="message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <label for="name">
                Category Name
            </label>

            <input
                type="text"
                id="name"
                name="name"
                value="<?php echo htmlspecialchars($name); ?>"
                required
            >

            <label for="description">
                Description
            </label>

            <textarea
                id="description"
                name="description"
            ><?php echo htmlspecialchars($description); ?></textarea>

            <label for="status">
                Status
            </label>

            <select id="status" name="status">

                <option
                    value="active"
                    <?php echo $status === "active" ? "selected" : ""; ?>
                >
                    Active
                </option>

                <option
                    value="inactive"
                    <?php echo $status === "inactive" ? "selected" : ""; ?>
                >
                    Inactive
                </option>

            </select>

            <button type="submit">
                Update Category
            </button>

        </form>

    </div>

</div>

</body>

</html>