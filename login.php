<?php

session_start();

require_once "config/database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {

        $message = "Please enter your email and password.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

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
                $message_type = "error";

            } else {

                /*
                 * Regenerate the session ID after successful login.
                 */
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
            $message_type = "error";
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

    <style>

        .login-page {
            max-width: 450px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .login-card {
            background: #ffffff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            margin-bottom: 8px;
        }

        .login-header p {
            color: #666;
            margin: 0;
        }

        .message {
            padding: 12px 15px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .message.error {
            background: #fff3f3;
            color: #b42318;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 15px;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #006b3c;
        }

        .login-button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 7px;
            background: #006b3c;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .login-button:hover {
            opacity: 0.9;
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }

        .register-link a {
            color: #006b3c;
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {

            .login-page {
                margin: 35px auto;
            }

            .login-card {
                padding: 25px 20px;
            }

        }

    </style>

</head>

<body>

<?php include "includes/header.php"; ?>


<main class="login-page">

    <div class="login-card">

        <div class="login-header">

            <h1>Login</h1>

            <p>
                Sign in to your NSBM Marketplace account.
            </p>

        </div>


        <?php if ($message != ""): ?>

            <div class="message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <form method="POST" action="">

            <div class="form-group">

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


            <div class="form-group">

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
                class="login-button"
            >
                Login
            </button>

        </form>


        <p class="register-link">

            Don't have an account?

            <a href="register.php">
                Register
            </a>

        </p>

    </div>

</main>


<?php include "includes/footer.php"; ?>

</body>

</html>