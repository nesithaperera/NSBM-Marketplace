<?php

require_once "config/database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if (
        empty($name) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $message = "Please fill in all required fields.";
        $message_type = "error";

    } elseif (strlen($name) > 100) {

        $message = "Name cannot be longer than 100 characters.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif (strlen($password) < 8) {

        $message = "Password must be at least 8 characters long.";
        $message_type = "error";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $message_type = "error";

    } elseif (strlen($phone) > 20) {

        $message = "Phone number cannot be longer than 20 characters.";
        $message_type = "error";

    } else {

        $stmt = $conn->prepare(
            "SELECT id
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $message = "This email is already registered.";
            $message_type = "error";

            $stmt->close();

        } else {

            $stmt->close();

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, phone, password)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $name,
                $email,
                $phone,
                $hashed_password
            );

            if ($stmt->execute()) {

                $message = "Registration successful! You can now login.";
                $message_type = "success";

                // Clear form values after successful registration
                $name = "";
                $email = "";
                $phone = "";

            } else {

                $message = "Registration failed. Please try again.";
                $message_type = "error";
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

    <title>Register - NSBM Marketplace</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>

        .register-page {
            max-width: 550px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .register-card {
            background: #ffffff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h1 {
            margin-bottom: 8px;
        }

        .register-header p {
            color: #666;
            margin: 0;
        }

        .message {
            padding: 12px 15px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .message.success {
            background: #e8f5ee;
            color: #006b3c;
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

        .required-note {
            color: #777;
            font-size: 13px;
            margin-top: 5px;
        }

        .register-button {
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

        .register-button:hover {
            opacity: 0.9;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }

        .login-link a {
            color: #006b3c;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {

            .register-page {
                margin: 30px auto;
            }

            .register-card {
                padding: 25px 20px;
            }

        }

    </style>

</head>

<body>

<?php include "includes/header.php"; ?>


<main class="register-page">

    <div class="register-card">

        <div class="register-header">

            <h1>Create Account</h1>

            <p>
                Join NSBM Marketplace and start buying and selling.
            </p>

        </div>


        <?php if ($message != ""): ?>

            <div class="message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <form method="POST" action="">

            <div class="form-group">

                <label for="name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    maxlength="100"
                    value="<?php echo htmlspecialchars($name); ?>"
                    placeholder="Enter your full name"
                    required
                >

            </div>


            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="150"
                    value="<?php echo htmlspecialchars($email); ?>"
                    placeholder="Enter your email"
                    required
                >

            </div>


            <div class="form-group">

                <label for="phone">
                    Phone
                </label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    maxlength="20"
                    value="<?php echo htmlspecialchars($phone); ?>"
                    placeholder="Enter your phone number"
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
                    minlength="8"
                    placeholder="Create a password"
                    required
                >

                <div class="required-note">
                    Password must be at least 8 characters long.
                </div>

            </div>


            <div class="form-group">

                <label for="confirm_password">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    minlength="8"
                    placeholder="Enter your password again"
                    required
                >

            </div>


            <button
                type="submit"
                class="register-button"
            >
                Create Account
            </button>

        </form>


        <p class="login-link">

            Already have an account?

            <a href="login.php">
                Login
            </a>

        </p>

    </div>

</main>


<?php include "includes/footer.php"; ?>

</body>

</html>