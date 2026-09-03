<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

$user_id = $_SESSION["user_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {

        $message = "Please fill in all fields.";

    }

    elseif ($new_password !== $confirm_password) {

        $message = "New passwords do not match.";

    }

    elseif (strlen($new_password) < 8) {

        $message = "New password must be at least 8 characters long.";

    }

    else {

        $stmt = $conn->prepare(
            "SELECT password
             FROM users
             WHERE id = ?"
        );

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();

        if (!$user || !password_verify($current_password, $user["password"])) {

            $message = "Current password is incorrect.";

        } else {

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "UPDATE users
                 SET password = ?
                 WHERE id = ?"
            );

            $stmt->bind_param("si", $hashed_password, $user_id);

            if ($stmt->execute()) {

                $message = "Password changed successfully.";

            } else {

                $message = "Unable to change password.";
            }

            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Change Password - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <h1>Change Password</h1>

    <?php if (!empty($message)): ?>

        <p><?php echo htmlspecialchars($message); ?></p>

    <?php endif; ?>

    <form method="POST" action="">

        <label>Current Password</label><br>

        <input
            type="password"
            name="current_password"
            required
        >

        <br><br>

        <label>New Password</label><br>

        <input
            type="password"
            name="new_password"
            required
        >

        <br><br>

        <label>Confirm New Password</label><br>

        <input
            type="password"
            name="confirm_password"
            required
        >

        <br><br>

        <button type="submit">Change Password</button>

    </form>

    <br>

    <a href="profile.php">Back to Profile</a>

    <br><br>

    <a href="dashboard.php">Back to Dashboard</a>

</body>

</html>
