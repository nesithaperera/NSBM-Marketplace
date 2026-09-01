<?php

require_once "../includes/auth.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>User Dashboard - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <h1>NSBM Marketplace</h1>

    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!</h2>

    <p>You are logged in successfully.</p>

    <hr>

    <h3>User Dashboard</h3>

    <p>
        <a href="profile.php">My Profile</a>
    </p>

    <p>
        <a href="edit-profile.php">Edit Profile</a>
    </p>

    <p>
        <a href="change-password.php">Change Password</a>
    </p>

    <p>
        <a href="../logout.php">Logout</a>
    </p>

</body>

</html>
