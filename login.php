```php
<?php

session_start();

require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Check required fields
    if (empty($email) || empty($password)) {
        $message = "Please enter your email and password.";
    }

    // Check email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    }

    else {

        // Find the user by email
        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role, status
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Check password
        if ($user && password_verify($password, $user["password"])) {

            // Check if account is active
            if ($user["status"] !== "active") {

                $message = "Your account is inactive.";

            } else {

                // Store logged-in user's information in the session
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_role"] = $user["role"];

                // Send user to dashboard
                header("Location: user/dashboard.php");
                exit;
            }

        } else {

            // Do not reveal whether the email exists
            $message = "Invalid email or password.";
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

    <title>Login - NSBM Marketplace</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <h1>NSBM Marketplace</h1>

    <h2>Login</h2>

    <?php if (!empty($message)): ?>

        <p><?php echo htmlspecialchars($message); ?></p>

    <?php endif; ?>

    <form method="POST" action="">

        <label>Email</label><br>
        <input type="email" name="email" required>

        <br><br>

        <label>Password</label><br>
        <input type="password" name="password" required>

        <br><br>

        <button type="submit">Login</button>

    </form>

    <p>
        Don't have an account?
        <a href="register.php">Register</a>
    </p>

</body>

</html>
```


    <h1>NSBM Marketplace</h1>

    <h2>Login</h2>

    <?php if (!empty($message)): ?>

        <p><?php echo htmlspecialchars($message); ?></p>

    <?php endif; ?>

    <form method="POST" action="">

        <label>Email</label><br>
        <input type="email" name="email" required>

        <br><br>

        <label>Password</label><br>
        <input type="password" name="password" required>

        <br><br>

        <button type="submit">Login</button>

    </form>

    <p>
        Don't have an account?
        <a href="register.php">Register</a>
    </p>

</body>

</html>