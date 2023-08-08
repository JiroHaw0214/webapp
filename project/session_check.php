<?php
session_start();

function checkSession() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['message'] = "Please login to access the page.";
        header("Location: login.php");
        exit;
    }
}
