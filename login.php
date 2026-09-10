<?php

session_start();

require_once "config/database.php";

$message = "";
$message_type = "error";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {

        $message = "Please enter your email and password.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role, status
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {

            if ($user["status"] !== "active") {

                $message = "Your account is inactive.";

            } else {

                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_role"] = $user["role"];

                if ($user["role"] === "admin") {

                    header("Location: admin/dashboard.php");

                } else {

                    header("Location: user/dashboard.php");
                }

                exit;
            }

        } else {

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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login - NSBM Marketplace</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<?php include "includes/header.php"; ?>


<main class="auth-page">

    <div class="auth-container">

        <div class="auth-card">

            <div class="auth-header">

                <h1>Welcome Back</h1>

                <p>
                    Login to your NSBM Marketplace account.
                </p>

            </div>


            <?php if (!empty($message)): ?>

                <div class="auth-message">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <form method="POST" action="">


                <div class="auth-form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>"
                        placeholder="Enter your email"
                        required
                    >

                </div>


                <div class="auth-form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="auth-button"
                >
                    Login
                </button>


            </form>


            <p class="auth-link">

                Don't have an account?

                <a href="register.php">
                    Create an account
                </a>

            </p>

        </div>

    </div>

</main>


<?php include "includes/footer.php"; ?>

</body>

</html>