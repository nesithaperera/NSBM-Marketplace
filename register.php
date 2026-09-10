<?php

session_start();

require_once "config/database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    // =========================
    // VALIDATION
    // =========================

    if ($name === "" || $email === "" || $phone === "" ||
        $password === "" || $confirm_password === "") {

        $message = "Please fill in all fields.";
        $message_type = "error";

    } elseif (strlen($name) > 100) {

        $message = "Name must not exceed 100 characters.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif (strlen($email) > 150) {

        $message = "Email must not exceed 150 characters.";
        $message_type = "error";

    } elseif (strlen($phone) > 20) {

        $message = "Phone number must not exceed 20 characters.";
        $message_type = "error";

    } elseif (strlen($password) < 8) {

        $message = "Password must be at least 8 characters long.";
        $message_type = "error";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $message_type = "error";

    } else {

        // =========================
        // CHECK IF EMAIL EXISTS
        // =========================

        $check_sql = "SELECT id FROM users WHERE email = ?";

        $check_stmt = $conn->prepare($check_sql);

        if (!$check_stmt) {

            $message = "Database error.";
            $message_type = "error";

        } else {

            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();

            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {

                $message = "An account with this email already exists.";
                $message_type = "error";

            } else {

                // =========================
                // HASH PASSWORD
                // =========================

                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


                // =========================
                // INSERT USER
                // =========================

                $insert_sql = "INSERT INTO users
                               (name, email, phone, password)
                               VALUES (?, ?, ?, ?)";

                $insert_stmt = $conn->prepare($insert_sql);

                if (!$insert_stmt) {

                    $message = "Database error.";
                    $message_type = "error";

                } else {

                    $insert_stmt->bind_param(
                        "ssss",
                        $name,
                        $email,
                        $phone,
                        $hashed_password
                    );


                    if ($insert_stmt->execute()) {

                        $message = "Account created successfully! You can now login.";
                        $message_type = "success";

                        // Clear form values
                        $name = "";
                        $email = "";
                        $phone = "";

                    } else {

                        $message = "Unable to create account. Please try again.";
                        $message_type = "error";
                    }

                    $insert_stmt->close();
                }
            }

            $check_stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Create Account - NSBM Marketplace</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>


<body>


<?php include "includes/header.php"; ?>


<main class="auth-page">

    <div class="auth-container">

        <div class="auth-card">


            <!-- ========================= -->
            <!-- HEADER -->
            <!-- ========================= -->

            <div class="auth-header">

                <h1>Create Account</h1>

                <p>
                    Join NSBM Marketplace and start
                    buying and selling.
                </p>

            </div>


            <!-- ========================= -->
            <!-- MESSAGE -->
            <!-- ========================= -->

            <?php if (!empty($message)) { ?>

                <div class="auth-message <?php echo $message_type; ?>">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php } ?>


            <!-- ========================= -->
            <!-- REGISTER FORM -->
            <!-- ========================= -->

            <form method="POST"
                  action="">


                <!-- NAME -->

                <div class="auth-form-group">

                    <label for="name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Enter your full name"
                        maxlength="100"
                        value="<?php echo htmlspecialchars($name ?? ""); ?>"
                        required
                    >

                </div>


                <!-- EMAIL -->

                <div class="auth-form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email address"
                        maxlength="150"
                        value="<?php echo htmlspecialchars($email ?? ""); ?>"
                        required
                    >

                </div>


                <!-- PHONE -->

                <div class="auth-form-group">

                    <label for="phone">
                        Phone
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        placeholder="Enter your phone number"
                        maxlength="20"
                        value="<?php echo htmlspecialchars($phone ?? ""); ?>"
                        required
                    >

                </div>


                <!-- PASSWORD -->

                <div class="auth-form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        minlength="8"
                        required
                    >

                    <small>
                        Password must be at least 8 characters long.
                    </small>

                </div>


                <!-- CONFIRM PASSWORD -->

                <div class="auth-form-group">

                    <label for="confirm_password">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Re-enter your password"
                        minlength="8"
                        required
                    >

                </div>


                <!-- SUBMIT -->

                <button
                    type="submit"
                    class="auth-button">

                    Create Account

                </button>


            </form>


            <!-- ========================= -->
            <!-- LOGIN LINK -->
            <!-- ========================= -->

            <div class="auth-footer">

                <p>

                    Already have an account?

                    <a href="login.php">
                        Login
                    </a>

                </p>

            </div>


        </div>

    </div>

</main>


<?php include "includes/footer.php"; ?>


</body>

</html>