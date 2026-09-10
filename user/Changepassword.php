<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

$user_id = $_SESSION["user_id"];

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $current_password = $_POST["current_password"] ?? "";
    $new_password = $_POST["new_password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if (
        empty($current_password) ||
        empty($new_password) ||
        empty($confirm_password)
    ) {

        $message = "Please fill in all fields.";
        $message_type = "error";

    } elseif ($new_password !== $confirm_password) {

        $message = "New passwords do not match.";
        $message_type = "error";

    } elseif (strlen($new_password) < 8) {

        $message = "New password must be at least 8 characters long.";
        $message_type = "error";

    } elseif ($new_password === $current_password) {

        $message = "New password must be different from your current password.";
        $message_type = "error";

    } else {

        /*
         * Get the user's current password
         */
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


        /*
         * Verify current password
         */
        if (
            !$user ||
            !password_verify($current_password, $user["password"])
        ) {

            $message = "Current password is incorrect.";
            $message_type = "error";

        } else {

            /*
             * Hash the new password
             */
            $hashed_password = password_hash(
                $new_password,
                PASSWORD_DEFAULT
            );


            /*
             * Update password
             */
            $stmt = $conn->prepare(
                "UPDATE users
                 SET password = ?
                 WHERE id = ?"
            );

            $stmt->bind_param(
                "si",
                $hashed_password,
                $user_id
            );

            if ($stmt->execute()) {

                $message = "Password changed successfully.";
                $message_type = "success";

            } else {

                $message = "Unable to change password.";
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

    <title>Change Password - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>

        .password-page {
            max-width: 650px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .password-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .password-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .password-header h1 {
            margin-bottom: 8px;
        }

        .password-header p {
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

        .password-help {
            margin-top: 5px;
            color: #777;
            font-size: 13px;
        }

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }

        .form-actions button,
        .form-actions a {
            display: inline-block;
            padding: 11px 18px;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
        }

        .change-button {
            background: #006b3c;
            color: white;
        }

        .change-button:hover {
            opacity: 0.9;
        }

        .cancel-button {
            background: #555;
            color: white;
        }

        .cancel-button:hover {
            opacity: 0.9;
        }

        @media (max-width: 600px) {

            .password-card {
                padding: 25px 20px;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions button,
            .form-actions a {
                width: 100%;
                box-sizing: border-box;
            }

        }

    </style>

</head>

<body>

<?php include "../includes/header.php"; ?>

<main class="password-page">

    <div class="password-card">

        <div class="password-header">

            <h1>Change Password</h1>

            <p>Update your account password securely.</p>

        </div>


        <?php if ($message != ""): ?>

            <div class="message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <form method="POST" action="">

            <div class="form-group">

                <label for="current_password">
                    Current Password
                </label>

                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    required
                >

            </div>


            <div class="form-group">

                <label for="new_password">
                    New Password
                </label>

                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    minlength="8"
                    required
                >

                <div class="password-help">
                    Password must be at least 8 characters long.
                </div>

            </div>


            <div class="form-group">

                <label for="confirm_password">
                    Confirm New Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    minlength="8"
                    required
                >

            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="change-button"
                >
                    Change Password
                </button>

                <a
                    href="profile.php"
                    class="cancel-button"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</main>

<?php include "../includes/footer.php"; ?>

</body>

</html>