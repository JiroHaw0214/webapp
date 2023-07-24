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
        </div>
        <?php
        $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');
        include 'config/database.php';
        $query = "SELECT order_details.id, order_details.order_id, products.name, order_details.quantity FROM order_details INNER JOIN products ON order_details.product_id = products.id WHERE order_details.order_id =:id";
        $stmt = $con->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $num = $stmt->rowCount();
        if ($num > 0) {
            echo "<div class='p-3'>";
            echo "<table class='table table-hover table-responsive table-bordered'>";
            echo "<tr>";
            echo "<th>OrderDetail ID</th>";
            echo "<th>Order ID</th>";
            echo "<th>Product Name</th>";
            echo "<th>Quantity</th>";
            echo "</tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                echo "<tr>";
                echo "<td>{$id}</td>";
                echo "<td>{$order_id}</td>";
                echo "<td>{$name}</td>";
                echo "<td>{$quantity}</td>";
                echo "</tr>";
            }
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