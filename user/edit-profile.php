<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

$user_id = $_SESSION["user_id"];
$message = "";

// Get current user information
$stmt = $conn->prepare(
    "SELECT name, email, phone
     FROM users
     WHERE id = ?"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

// Update profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $phone = trim($_POST["phone"]);

    if (empty($name)) {

        $message = "Name cannot be empty.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE users
             SET name = ?, phone = ?
             WHERE id = ?"
        );

        $stmt->bind_param("ssi", $name, $phone, $user_id);

        if ($stmt->execute()) {

            // Update the name stored in the session
            $_SESSION["user_name"] = $name;

            $message = "Profile updated successfully.";

            // Update the displayed values
            $user["name"] = $name;
            $user["phone"] = $phone;

        } else {

            $message = "Unable to update profile.";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Profile - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <h1>Edit Profile</h1>

    <?php if (!empty($message)): ?>

        <p><?php echo htmlspecialchars($message); ?></p>

    <?php endif; ?>

    <form method="POST" action="">

        <label>Full Name</label><br>

        <input
            type="text"
            name="name"
            value="<?php echo htmlspecialchars($user["name"]); ?>"
            required
        >

        <br><br>

        <label>Email</label><br>

        <input
            type="email"
            value="<?php echo htmlspecialchars($user["email"]); ?>"
            readonly
        >

        <br><br>

        <label>Phone</label><br>

        <input
            type="text"
            name="phone"
            value="<?php echo htmlspecialchars($user["phone"] ?? ""); ?>"
        >

        <br><br>

        <button type="submit">Save Changes</button>

    </form>

    <br>

    <a href="profile.php">Back to Profile</a>

    <br><br>

    <a href="dashboard.php">Back to Dashboard</a>

</body>

</html>

