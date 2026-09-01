<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;
}

// Check if the logged-in user is an admin
if ($_SESSION["user_role"] !== "admin") {

    header("Location: ../index.php");
    exit;
}

?>

