<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

$user_id = $_SESSION["user_id"];
$message = "";
$message_type = "";

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

if (!$user) {
    die("Unable to load your profile.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if ($name == "") {

        $message = "Name cannot be empty.";
        $message_type = "error";

    } elseif (strlen($name) > 100) {

        $message = "Name cannot be longer than 100 characters.";
        $message_type = "error";

    } elseif (strlen($phone) > 20) {

        $message = "Phone number cannot be longer than 20 characters.";
        $message_type = "error";

    } else {

        $stmt = $conn->prepare(
            "UPDATE users
             SET name = ?, phone = ?
             WHERE id = ?"
        );

        $stmt->bind_param("ssi", $name, $phone, $user_id);

        if ($stmt->execute()) {

            $_SESSION["user_name"] = $name;

            $message = "Profile updated successfully.";
            $message_type = "success";

            $user["name"] = $name;
            $user["phone"] = $phone;

        } else {

            $message = "Unable to update profile.";
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

    <title>Edit Profile - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>

        .edit-profile-page {
            max-width: 700px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .edit-profile-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .edit-profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .edit-profile-header h1 {
            margin-bottom: 8px;
        }

        .edit-profile-header p {
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

        .readonly-field {
            background: #f3f3f3;
            cursor: not-allowed;
        }

        .help-text {
            display: block;
            margin-top: 6px;
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

        .save-button {
            background: #006b3c;
            color: white;
        }

        .save-button:hover {
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

            .edit-profile-card {
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

<main class="edit-profile-page">

    <div class="edit-profile-card">

        <div class="edit-profile-header">

            <h1>Edit Profile</h1>

            <p>Update your personal information.</p>

        </div>


        <?php if ($message != ""): ?>

            <div class="message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <form method="POST" action="">

            <div class="form-group">

                <label for="name">Full Name</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    maxlength="100"
                    value="<?php echo htmlspecialchars($user["name"]); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="email">Email</label>

                <input
                    type="email"
                    id="email"
                    value="<?php echo htmlspecialchars($user["email"]); ?>"
                    class="readonly-field"
                    readonly
                >

                <small class="help-text">
                    Email address cannot be changed.
                </small>

            </div>


            <div class="form-group">

                <label for="phone">Phone</label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    maxlength="20"
                    value="<?php echo htmlspecialchars($user["phone"] ?? ""); ?>"
                    placeholder="Enter your phone number"
                >

            </div>


            <div class="form-actions">

                <button type="submit" class="save-button">
                    Save Changes
                </button>

                <a href="profile.php" class="cancel-button">
                    Cancel
                </a>

            </div>

        </form>

    </div>

</main>

<?php include "../includes/footer.php"; ?>

</body>

</html>