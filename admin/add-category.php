<?php

require_once "../includes/admin-auth.php";
require_once "../config/database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");

    // Validate category name
    if ($name === "") {

        $message = "Category name is required.";
        $message_type = "error";

    } else {

        // Check if category already exists
        $check_sql = "SELECT id FROM categories WHERE name = ?";

        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $name);
        $check_stmt->execute();

        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {

            $message = "This category already exists.";
            $message_type = "error";

        } else {

            // Insert category
            $sql = "INSERT INTO categories (name, description, status)
                    VALUES (?, ?, 'active')";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "ss",
                $name,
                $description
            );

            if ($stmt->execute()) {

                $message = "Category added successfully.";
                $message_type = "success";

                // Clear form values
                $name = "";
                $description = "";

            } else {

                $message = "Failed to add category.";
                $message_type = "error";
            }

            $stmt->close();
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

    <title>Add Category - NSBM Marketplace</title>

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
        textarea {
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

        <h1>➕ Add Category</h1>

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
                value="<?php echo htmlspecialchars($name ?? ""); ?>"
                placeholder="Enter category name"
                required
            >

            <label for="description">
                Description
            </label>

            <textarea
                id="description"
                name="description"
                placeholder="Enter category description"
            ><?php echo htmlspecialchars($description ?? ""); ?></textarea>

            <button type="submit">
                Add Category
            </button>

        </form>

    </div>

</div>

</body>

</html>