<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

$user_id = $_SESSION["user_id"];

// Get the logged-in user's information
$stmt = $conn->prepare(
    "SELECT name, email, phone, profile_image, created_at
     FROM users
     WHERE id = ?"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Profile - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <h1>My Profile</h1>

    <?php if ($user): ?>

        <p>
            <strong>Name:</strong>
            <?php echo htmlspecialchars($user["name"]); ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?php echo htmlspecialchars($user["email"]); ?>
        </p>

        <p>
            <strong>Phone:</strong>
            <?php echo htmlspecialchars($user["phone"] ?? "Not provided"); ?>
        </p>

        <p>
            <strong>Member Since:</strong>
            <?php echo htmlspecialchars($user["created_at"]); ?>
        </p>

        <br>

        <a href="edit-profile.php">Edit Profile</a>

        <br><br>

        <a href="change-password.php">Change Password</a>

        <br><br>

        <a href="dashboard.php">Back to Dashboard</a>

        <br><br>

        <a href="../logout.php">Logout</a>

    <?php else: ?>

        <p>Unable to load your profile.</p>

    <?php endif; ?>

</body>

</html>

