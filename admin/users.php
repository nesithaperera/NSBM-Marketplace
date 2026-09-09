<?php

require_once "../includes/admin-auth.php";
require_once "../config/database.php";


// Get all users
$sql = "SELECT
            id,
            name,
            email,
            phone,
            role,
            status,
            created_at
        FROM users
        ORDER BY created_at DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manage Users - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>

        body {
            background: #f5f7f9;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .users-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .users-header {
            margin-bottom: 25px;
        }

        .table-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th,
        td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f1f3f5;
        }

        .role {
            font-weight: bold;
        }

        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
        }

        .active {
            background: #e7f5ed;
            color: #006b3c;
        }

        .inactive {
            background: #f8d7da;
            color: #842029;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 16px;
            background: #006b3c;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .empty-message {
            text-align: center;
            padding: 30px;
        }

    </style>

</head>

<body>

<?php include "../includes/header.php"; ?>


<div class="users-container">

    <div class="users-header">

        <a
            href="dashboard.php"
            class="back-btn">
            ← Dashboard
        </a>

        <h1>
            👥 Manage Users
        </h1>

        <p>
            View registered users in the NSBM Marketplace.
        </p>

    </div>


    <div class="table-container">

        <?php if ($result->num_rows > 0): ?>

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Email</th>

                        <th>Phone</th>

                        <th>Role</th>

                        <th>Status</th>

                        <th>Registered</th>

                    </tr>

                </thead>


                <tbody>

                <?php while ($user = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo (int)$user["id"]; ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars($user["name"]);
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars($user["email"]);
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $user["phone"] ?? "-"
                            );
                            ?>
                        </td>

                        <td class="role">

                            <?php
                            echo htmlspecialchars($user["role"]);
                            ?>

                        </td>

                        <td>

                            <span
                                class="status
                                <?php echo htmlspecialchars(
                                    $user["status"]
                                ); ?>"
                            >

                                <?php
                                echo htmlspecialchars($user["status"]);
                                ?>

                            </span>

                        </td>

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $user["created_at"]
                            );
                            ?>

                        </td>

                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="empty-message">

                <h2>
                    No users found
                </h2>

                <p>
                    There are currently no registered users.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>


</body>

</html>