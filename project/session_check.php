<?php
session_start();

function checkSession() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?message=Please login to access the page.");
        exit();
    }
}
?>
