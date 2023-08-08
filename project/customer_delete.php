<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
include 'config/database.php';

// Check if customer ID is provided via GET
if (!isset($_GET['id'])) {
    $_SESSION['message'] = "Customer ID is missing.";
    header("Location: customer_read.php");
    exit;
}

// Retrieve customer ID from GET parameter
$customer_id = $_GET['id'];

try {
    // Check if the customer exists
    $query = "SELECT id FROM customers WHERE id = :customer_id";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':customer_id', $customer_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $_SESSION['message'] = "Customer not found.";
        header("Location: customer_read.php");
        exit;
    }

    // Delete the customer
    $deleteQuery = "DELETE FROM customers WHERE id = :customer_id";
    $deleteStmt = $con->prepare($deleteQuery);
    $deleteStmt->bindParam(':customer_id', $customer_id);
    $deleteStmt->execute();

    $_SESSION['message'] = "Customer deleted successfully.";
    header("Location: customer_read.php");
    exit;
} catch (PDOException $exception) {
    $_SESSION['message'] = "An error occurred: " . $exception->getMessage();
    header("Location: customer_read.php");
    exit;
}
