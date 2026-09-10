<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

$user_id = $_SESSION["user_id"];

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

    <style>

        .profile-page {
            max-width: 800px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .profile-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-header h1 {
            margin-bottom: 8px;
        }

        .profile-header p {
            color: #666;
            margin: 0;
        }

        .profile-image {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #006b3c;
        }

        .profile-placeholder {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: #e8f5ee;
            color: #006b3c;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 42px;
            font-weight: bold;
        }

        .profile-info {
            border-top: 1px solid #eee;
        }

        .profile-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 16px 0;
            border-bottom: 1px solid #eee;
        }

        .profile-label {
            font-weight: bold;
            color: #444;
        }

        .profile-value {
            color: #666;
            text-align: right;
            word-break: break-word;
        }

        .profile-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }

        .profile-actions a {
            display: inline-block;
            padding: 11px 18px;
            border-radius: 7px;
            text-decoration: none;
            background: #006b3c;
            color: white;
            font-weight: 600;
        }

        .profile-actions a:hover {
            opacity: 0.9;
        }

        .profile-actions .secondary {
            background: #555;
        }

        .profile-actions .danger {
            background: #b42318;
        }

        .error-message {
            text-align: center;
            padding: 25px;
            background: #fff3f3;
            border-radius: 8px;
            color: #b42318;
        }

        @media (max-width: 600px) {

            .profile-card {
                padding: 25px 20px;
            }

            .profile-row {
                flex-direction: column;
                gap: 5px;
            }

            .profile-value {
                text-align: left;
            }

            .profile-actions {
                flex-direction: column;
            }

            .profile-actions a {
                text-align: center;
            }

        }

    </style>

</head>

<body>

<?php include "../includes/header.php"; ?>

<main class="profile-page">

    <?php if ($user): ?>

        <div class="profile-card">

            <div class="profile-header">

                <?php if (!empty($user["profile_image"])): ?>

                    <img
                        src="../assets/images/profiles/<?php echo htmlspecialchars($user["profile_image"]); ?>"
                        alt="Profile Image"
                        class="profile-image"
                    >

                <?php else: ?>

                    <div class="profile-placeholder">
                        <?php echo strtoupper(substr($user["name"], 0, 1)); ?>
                    </div>

                <?php endif; ?>

                <h1>My Profile</h1>

                <p>View and manage your account information.</p>

            </div>


            <div class="profile-info">

                <div class="profile-row">

                    <span class="profile-label">Name</span>

                    <span class="profile-value">
                        <?php echo htmlspecialchars($user["name"]); ?>
                    </span>

                </div>


                <div class="profile-row">

                    <span class="profile-label">Email</span>

                    <span class="profile-value">
                        <?php echo htmlspecialchars($user["email"]); ?>
                    </span>

                </div>


                <div class="profile-row">

                    <span class="profile-label">Phone</span>

                    <span class="profile-value">
                        <?php
                        echo !empty($user["phone"])
                            ? htmlspecialchars($user["phone"])
                            : "Not provided";
                        ?>
                    </span>

                </div>


                <div class="profile-row">

                    <span class="profile-label">Member Since</span>

                    <span class="profile-value">
                        <?php echo htmlspecialchars($user["created_at"]); ?>
                    </span>

                </div>

            </div>


            <div class="profile-actions">

                <a href="edit-profile.php">
                    Edit Profile
                </a>

                <a href="change-password.php">
                    Change Password
                </a>

                <a href="dashboard.php" class="secondary">
                    Back to Dashboard
                </a>

                <a href="../logout.php" class="danger">
                    Logout
                </a>

            </div>

        </div>

    <?php else: ?>

        <div class="profile-card">

            <div class="error-message">
                Unable to load your profile.
            </div>

        </div>

    <?php endif; ?>

</main>

<?php include "../includes/footer.php"; ?>

</body>

</html>