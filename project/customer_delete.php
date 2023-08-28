<?php
// include database connection
include 'config/database.php';
session_start();

try {
    // get record ID
    // isset() is a PHP function used to verify if a value is there or not
    $id = isset($_GET['id']) ? $_GET['id'] :  die('ERROR: Record ID not found.');
    $checkOrdersQuery = "SELECT id FROM order_summary WHERE customer_id = ?";
    $checkOrdersStmt = $con->prepare($checkOrdersQuery);
    $checkOrdersStmt->bindParam(1, $id);
    $checkOrdersStmt->execute();

    if ($checkOrdersStmt->rowCount() > 0) {
        // There are associated orders, collect order IDs
        $orderIds = array();
        while ($row = $checkOrdersStmt->fetch(PDO::FETCH_ASSOC)) {
            $orderIds[] = $row['id'];
        }
        $_SESSION['orderIds'] = $orderIds; // Store orderIds in session
        header('Location: customer_read.php?action=fail');
    } else {
        // There are no associated orders, delete the product directly
        // First, check if the product has an associated image
        $getImageQuery = "SELECT image FROM customers WHERE id = ?";
        $getImageStmt = $con->prepare($getImageQuery);
        $getImageStmt->bindParam(1, $id);
        $getImageStmt->execute();

        if ($getImageStmt->rowCount() > 0) {
            // Product has an associated image, delete it
            $imageRow = $getImageStmt->fetch(PDO::FETCH_ASSOC);
            $imageFileName = $imageRow['image'];
            if (!empty($imageFileName) && file_exists("uploads/$imageFileName")) {
                unlink("uploads/$imageFileName");
            }
        }
        // delete query
        $query = "DELETE FROM customers WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bindParam(1, $id);

        if ($stmt->execute()) {
            // redirect to read records page and
            // tell the user record was deleted
            header('Location: customer_read.php?action=deleted');
        } else {
            die('Unable to delete record.');
        }
    }
}
// show error
catch (PDOException $exception) {
    die('ERROR: ' . $exception->getMessage());
}
