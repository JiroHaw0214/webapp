<?php
require_once 'session_check.php';
checkSession();
?>
<!DOCTYPE HTML>
<html>

<head>
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>
    <div class="container p-0 bg-light">
        <?php include 'includes/navbar.php'; ?>
        <div class="page-header p-3 pb-1">
            <h1>Order Details</h1>
            <?php
            // Include database connection
            include 'config/database.php';

            // Get order ID from URL parameter
            $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');

            // Fetch customer name and order date from the database
            $orderInfoQuery = "SELECT CONCAT(customers.first_name, ' ', customers.last_name) AS full_name, order_summary.order_date FROM order_summary INNER JOIN customers ON order_summary.customer_id = customers.id WHERE order_summary.id = :id";
            $orderInfoStmt = $con->prepare($orderInfoQuery);
            $orderInfoStmt->bindParam(":id", $id);
            $orderInfoStmt->execute();
            $orderInfoRow = $orderInfoStmt->fetch(PDO::FETCH_ASSOC);
            echo "<p><strong>Customer Name:</strong> {$orderInfoRow['full_name']}</p>";
            echo "<p><strong>Order Date & Time:</strong> {$orderInfoRow['order_date']}</p>";
            ?>
        </div>
        <?php
        // Fetch order details from the database
        $query = "SELECT order_details.id, products.name, order_details.quantity, products.price, products.promotion_price FROM order_details INNER JOIN products ON order_details.product_id = products.id WHERE order_details.order_id =:id";
        $stmt = $con->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $num = $stmt->rowCount();
        if ($num > 0) {
            echo "<div class='p-3'>";
            echo "<table class='table table-hover table-responsive table-bordered'>";
            echo "<tr>";
            echo "<th>No.</th>";
            echo "<th>Product Name</th>";
            echo "<th>Quantity</th>";
            echo "<th>Price</th>";
            echo "<th>Total</th>";
            echo "</tr>";
            $totalPrice = 0;
            $counter = 1;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $productPrice = (!empty($promotion_price) && $promotion_price != 0) ? $promotion_price : $price;
                $subtotal = $quantity * $productPrice;
                $totalPrice += $subtotal;
                echo "<tr>";
                echo "<td>{$counter}</td>";
                echo "<td>{$name}</td>";
                echo "<td>{$quantity}</td>";
                echo "<td class='text-end'>";
                if (!empty($promotion_price) && $promotion_price != 0) {
                    echo "<div class='text-decoration-line-through'>" . number_format($price, 2) . "</div>";
                    echo number_format($promotion_price, 2);
                } else {
                    echo number_format($price, 2);
                }
                echo "</td>";
                echo "<td class='text-end'>" . number_format($subtotal, 2) . "</td>";
                echo "</tr>";
                $counter++;
            }
            echo "<tr>";
            echo "<td colspan='4' class='text-end'><strong>Total:</strong></td>";
            echo "<td class='text-end'>" . number_format($totalPrice, 2) . "</td>";
            echo "</tr>";
            echo "</table>";
            echo "<a href='order_read.php' class='btn btn-danger'>Back to order list</a>";
            echo "</div>";
        } else {
            echo '<div class="p-3">
                    <div class="alert alert-danger">No records found.</div>
                </div>';
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>