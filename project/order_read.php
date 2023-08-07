<?php
require_once 'session_check.php';
checkSession();
?>
<!DOCTYPE HTML>
<html>

<head>
    <title>Order List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php
        include 'includes/navbar.php';
        ?>
        <div class="page-header">
            <h1>Order List</h1>
        </div>
        <?php
        // Include the necessary database connection file
        include 'config/database.php';
        try {
            // Select all categories from the database
            $query = "SELECT order_summary.id, customers.first_name, order_summary.order_date FROM order_summary INNER JOIN customers ON order_summary.customer_id = customers.id ORDER BY order_summary.order_date DESC";
            $stmt = $con->prepare($query);
            $stmt->execute();
            // Check if there are any
            if ($stmt->rowCount() > 0) {
                echo "<table class='table table-hover table-responsive table-bordered'>"; //start table
                //creating our table heading
                echo "<tr>";
                echo "<th>Order ID</th>";
                echo "<th>Customer Name</th>";
                echo "<th>Order Date</th>";
                echo "<th>Total Amount</th>";
                echo "<th>Action</th>";
                echo "</tr>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // extract row
                    // this will make $row['firstname'] to just $firstname only
                    extract($row);
                    // Get total amount for this order
                    $totalAmountQuery = "SELECT SUM(IFNULL(products.promotion_price, products.price) * order_details.quantity) AS total_amount FROM order_details INNER JOIN products ON order_details.product_id = products.id WHERE order_details.order_id = :order_id";
                    $totalAmountStmt = $con->prepare($totalAmountQuery);
                    $totalAmountStmt->bindParam(":order_id", $id);
                    $totalAmountStmt->execute();
                    $totalAmountRow = $totalAmountStmt->fetch(PDO::FETCH_ASSOC);
                    $totalAmount = $totalAmountRow['total_amount'];
                    // creating new table row per record
                    echo "<tr>";
                    echo "<td>{$id}</td>";
                    echo "<td>{$first_name}</td>";
                    echo "<td>{$order_date}</td>";
                    echo "<td class='text-end'>" . number_format($totalAmount, 2) . "</td>";
                    echo "<td>";
                    // read one record
                    echo "<a href='order_read_one.php?id={$id}' class='btn btn-info me-3'>Read</a>";
                    // we will use this links on next part of this post
                    echo "<a href='order_update.php?id={$id}' class='btn btn-primary me-3'>Edit</a>";
                    // we will use this links on next part of this post
                    echo "<a href='#' onclick='order_delete({$id});'  class='btn btn-danger'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                // end table
                echo "</table>";
            } else {
                echo '<div class="alert alert-info">No order found.</div>';
            }
        } catch (PDOException $exception) {
            echo '<div class="alert alert-danger">' . $exception->getMessage() . '</div>';
        }
        ?>
        <a href="create_order.php" class="btn btn-primary">Create Order</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>