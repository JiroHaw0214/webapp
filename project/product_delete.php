<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
include 'config/database.php';

// Check if product ID is provided via GET
if (!isset($_GET['id'])) {
    $_SESSION['message'] = "Product ID is missing.";
    header("Location: product_read.php");
    exit;
}

// Retrieve product ID from GET parameter
$product_id = $_GET['id'];

try {
    // Check if the product exists
    $query = "SELECT id FROM products WHERE id = :product_id";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $_SESSION['message'] = "Product not found.";
        header("Location: product_read.php");
        exit;
    }

    // Delete the product
    $deleteQuery = "DELETE FROM products WHERE id = :product_id";
    $deleteStmt = $con->prepare($deleteQuery);
    $deleteStmt->bindParam(':product_id', $product_id);
    $deleteStmt->execute();

    $_SESSION['message'] = "Product deleted successfully.";
    header("Location: product_read.php");
    exit;
} catch (PDOException $exception) {
    $_SESSION['message'] = "An error occurred: " . $exception->getMessage();
    header("Location: product_read.php");
    exit;
}
