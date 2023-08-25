<?php
// Include database connection
include 'config/database.php';

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

        // Notify the user that the product is in use by orders and display order IDs
        echo "<div class='alert alert-warning'>This product is ordered by the following orders:</div>";
        echo "<ul>";
        foreach ($orderIds as $orderId) {
            echo "<li>Order ID: $orderId</li>";
        }
        echo "</ul>";
        echo "<p>You cannot delete this product until it is removed from these orders.</p>";
    } else {
        // There are no associated orders, delete the product directly
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
