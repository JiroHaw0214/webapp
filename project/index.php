<?php
require_once 'session_check.php';
require_once 'config/database.php';
checkSession();
// Function to fetch the total number of records from a table
function getTotalRecords($tableName, $columnName, $con)
{
    $query = "SELECT COUNT(*) AS total FROM $tableName";
    $stmt = $con->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total'];
}
// Function to fetch the latest order details
function getLatestOrder($con)
{
    // ifnull also can
    $query = "SELECT order_summary.id, customers.first_name, order_summary.order_date, SUM(CASE WHEN products.promotion_price IS NOT NULL THEN products.promotion_price * order_details.quantity ELSE products.price * order_details.quantity END) AS total_amount
              FROM order_summary
              INNER JOIN customers ON order_summary.customer_id = customers.id
              INNER JOIN order_details ON order_summary.id = order_details.order_id
              INNER JOIN products ON order_details.product_id = products.id
              GROUP BY order_summary.id, customers.first_name, order_summary.order_date
              ORDER BY order_summary.order_date DESC
              LIMIT 1";
    $stmt = $con->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to fetch the order with the highest purchased amount
function getHighestPurchaseOrder($con)
{
    $query = "SELECT order_summary.id, customers.first_name, order_summary.order_date, SUM(CASE WHEN products.promotion_price IS NOT NULL THEN products.promotion_price * order_details.quantity ELSE products.price * order_details.quantity END) AS total_amount
              FROM order_summary
              INNER JOIN customers ON order_summary.customer_id = customers.id
              INNER JOIN order_details ON order_summary.id = order_details.order_id
              INNER JOIN products ON order_details.product_id = products.id
              GROUP BY order_summary.id, customers.first_name, order_summary.order_date
              ORDER BY total_amount DESC
              LIMIT 1";
    $stmt = $con->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to fetch top 5 selling products
function getTopSellingProducts($con)
{
    $query = "SELECT products.name, SUM(order_details.quantity) AS total_sold
              FROM order_details
              INNER JOIN products ON order_details.product_id = products.id
              GROUP BY products.id
              ORDER BY total_sold DESC
              LIMIT 5";
    $stmt = $con->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to fetch products that never purchased
function getNeverPurchasedProducts($con)
{
    $query = "SELECT products.name
              FROM products
              LEFT JOIN order_details ON products.id = order_details.product_id
              WHERE order_details.id IS NULL
              LIMIT 3";
    $stmt = $con->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch total number of customers, products, and orders
$totalCustomers = getTotalRecords('customers', 'id', $con);
$totalProducts = getTotalRecords('products', 'id', $con);
$totalOrders = getTotalRecords('order_summary', 'id', $con);

// Fetch latest order details
$latestOrder = getLatestOrder($con);
$highestPurchaseOrder = getHighestPurchaseOrder($con);
$topSellingProducts = getTopSellingProducts($con);
$neverPurchasedProducts = getNeverPurchasedProducts($con);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Home - Product Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>

    <div class="container">
        <?php include 'includes/navbar.php'; ?>

        <div class="page-header">
            <h1>Welcome to the Product Management System</h1>
        </div>
        <p>This is the home page of the Product Management System. You can use this system to create and manage products and customers.</p>
        <p>Get started by navigating to the "Create Product" or "Create Customer" page using the navigation menu above.</p>
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        Total Customers
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $totalCustomers; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        Total Products
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $totalProducts; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        Total Orders
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $totalOrders; ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                Latest Order
            </div>
            <div class="card-body">
                <h5 class="card-title">Order ID: <?php echo $latestOrder['id']; ?></h5>
                <p class="card-text">Customer Name: <?php echo $latestOrder['first_name']; ?></p>
                <p class="card-text">Transaction Date: <?php echo $latestOrder['order_date']; ?></p>
                <p class="card-text">Purchase Amount: $<?php echo number_format($latestOrder['total_amount'], 2); ?></p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                The Order with Highest Purchased Amount
            </div>
            <div class="card-body">
                <h5 class="card-title">Order ID: <?php echo $highestPurchaseOrder['id']; ?></h5>
                <p class="card-text">Customer Name: <?php echo $highestPurchaseOrder['first_name']; ?></p>
                <p class="card-text">Transaction Date: <?php echo $highestPurchaseOrder['order_date']; ?></p>
                <p class="card-text">Purchase Amount: $<?php echo number_format($highestPurchaseOrder['total_amount'], 2); ?></p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                Top 5 Selling Products
            </div>
            <div class="card-body">
                <ol>
                    <?php foreach ($topSellingProducts as $product) : ?>
                        <li><?php echo $product['name']; ?> (Sold: <?php echo $product['total_sold']; ?>)</li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                Products Never Purchased
            </div>
            <div class="card-body">
                <ul>
                    <?php foreach ($neverPurchasedProducts as $product) : ?>
                        <li><?php echo $product['name']; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>