<?php
// Include database connection
include 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate the form data and process the order

    // Get customer username from the form
    $customer_name = $_POST['customer'];

    // Prepare and insert order_summary record
    try {
        // Start a transaction to ensure atomicity (they are either all successful or all rolled back)
        $con->beginTransaction();

        // Insert order_summary record
        $order_date = date("Y-m-d H:i:s");
        $order_summary_query = "INSERT INTO order_summary (customer_name, order_date) VALUES (:customer_name, :order_date)";
        $order_summary_stmt = $con->prepare($order_summary_query);
        $order_summary_stmt->bindParam(':customer_name', $customer_name);
        $order_summary_stmt->bindParam(':order_date', $order_date);
        $order_summary_stmt->execute();

        // Get the auto-generated order ID
        $order_id = $con->lastInsertId();

        // Calculate total amount and initialize it
        $total_amount = 0;

        // Insert order_details records
        for ($i = 1; $i <= 3; $i++) {
            $product_id = $_POST["product_$i"];
            $quantity = $_POST["quantity_$i"];

            if (!empty($product_id) && !empty($quantity)) {
                // Fetch the product price from the database
                $product_query = "SELECT price FROM products WHERE id = :product_id";
                $product_stmt = $con->prepare($product_query);
                $product_stmt->bindParam(':product_id', $product_id);
                $product_stmt->execute();
                $product_row = $product_stmt->fetch(PDO::FETCH_ASSOC);

                // Calculate the line total
                $line_total = $product_row['price'] * $quantity;

                // Add the line total to the total amount
                $total_amount += $line_total;

                // Insert order_details record
                $order_details_query = "INSERT INTO order_details (order_id, product_id, quantity, line_total) VALUES (:order_id, :product_id, :quantity, :line_total)";
                $order_details_stmt = $con->prepare($order_details_query);
                $order_details_stmt->bindParam(':order_id', $order_id);
                $order_details_stmt->bindParam(':product_id', $product_id);
                $order_details_stmt->bindParam(':quantity', $quantity);
                $order_details_stmt->bindParam(':line_total', $line_total);
                $order_details_stmt->execute();
            }
        }

        // Update the total_amount in order_summary
        $update_total_query = "UPDATE order_summary SET total_amount = :total_amount WHERE id = :order_id";
        $update_total_stmt = $con->prepare($update_total_query);
        $update_total_stmt->bindParam(':total_amount', $total_amount);
        $update_total_stmt->bindParam(':order_id', $order_id);
        $update_total_stmt->execute();

        // Commit the transaction
        $con->commit();

        // Redirect back to the page with a success message
        header("Location: create_order.php?order_created=1");
        exit();
    } catch (PDOException $exception) {
        // Rollback the transaction in case of an error
        $con->rollback();

        // Display error message
        die('ERROR: ' . $exception->getMessage());
    }
} else {
    // Redirect back to the page if form data is not submitted
    header("Location: create_order.php");
    exit();
}
