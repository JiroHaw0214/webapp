<?php
// Include database connection
include 'config/database.php';
session_start();

try {
    // Get record ID
    $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');

    // Check if the product has associated orders
    $checkOrdersQuery = "SELECT order_id FROM order_details WHERE product_id = ?";
    $checkOrdersStmt = $con->prepare($checkOrdersQuery);
    $checkOrdersStmt->bindParam(1, $id);
    $checkOrdersStmt->execute();

    if ($checkOrdersStmt->rowCount() > 0) {
        // There are associated orders, collect order IDs
        $orderIds = array();
        while ($row = $checkOrdersStmt->fetch(PDO::FETCH_ASSOC)) {
            $orderIds[] = $row['order_id'];
        }
        $_SESSION['orderIds'] = $orderIds; // Store orderIds in session
        header('Location: product_read.php?action=fail');
    } else {
        // There are no associated orders, delete the product directly
        // First, check if the product has an associated image
        $getImageQuery = "SELECT image FROM products WHERE id = ?";
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

        // Now, delete the product record
        $deleteProductQuery = "DELETE FROM products WHERE id = ?";
        $deleteProductStmt = $con->prepare($deleteProductQuery);
        $deleteProductStmt->bindParam(1, $id);

        if ($deleteProductStmt->execute()) {
            // Redirect to read records page and notify the user that the record was deleted
            header('Location: product_read.php?action=deleted');
        } else {
            die('Unable to delete product.');
        }
    }
} catch (PDOException $exception) {
    die('ERROR: ' . $exception->getMessage());
}
?>
