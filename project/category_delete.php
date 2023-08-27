<?php
// include database connection
include 'config/database.php';
session_start();

try {
    // get record ID
    // isset() is a PHP function used to verify if a value is there or not
    $id = isset($_GET['id']) ? $_GET['id'] :  die('ERROR: Record ID not found.');

    $checkProductsQuery = "SELECT id FROM products WHERE category_id = ?";
    $checkProductsStmt = $con->prepare($checkProductsQuery);
    $checkProductsStmt->bindParam(1, $id);
    $checkProductsStmt->execute();

    if ($checkProductsStmt->rowCount() > 0) {
        // There are associated orders, collect order IDs
        $productIds = array();
        while ($row = $checkProductsStmt->fetch(PDO::FETCH_ASSOC)) {
            $productIds[] = $row['id'];
        }
        $_SESSION['productIds'] = $productIds; // Store orderIds in session
        header('Location: category_read.php?action=fail');
    } else {
        // delete query
        $query = "DELETE FROM category WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bindParam(1, $id);

        if ($stmt->execute()) {
            // redirect to read records page and
            // tell the user record was deleted
            header('Location: category_read.php?action=deleted');
        } else {
            die('Unable to delete record.');
        }
    }
}
// show error
catch (PDOException $exception) {
    die('ERROR: ' . $exception->getMessage());
}
