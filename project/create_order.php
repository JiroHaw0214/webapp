<!DOCTYPE HTML>
<html>

<head>
    <title>Create New Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="page-header">
            <h1>Create New Order</h1>
        </div>

        <!-- Your HTML form here to create a new order -->
        <form action="process_order.php" method="POST">
            <!-- Customer dropdown menu -->
            <div class="mb-3">
                <label for="customer_name" class="form-label">Select Customer:</label>
                <select name="customer_name" class="form-control" id="customer_name">
                    <!-- PHP code to fetch and populate the customer names -->
                    <?php
                    // Include database connection
                    include 'config/database.php';

                    // Fetch customer names from the database
                    $query = "SELECT customer_name FROM customers";
                    $stmt = $con->prepare($query);
                    $stmt->execute();

                    // Loop through the results and create options for the dropdown
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $customer_name = $row['customer_name'];
                        echo "<option value='$customer_name'>$customer_name</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Product dropdown menus (You need three dropdowns for three products) -->
            <div class="mb-3">
                <label for="product_1" class="form-label">Select Product 1:</label>
                <select name="product_1" class="form-control" id="product_1">
                    <!-- PHP code to fetch and populate the product names -->
                    <?php
                    // Fetch product names from the database
                    $query = "SELECT name FROM products";
                    $stmt = $con->prepare($query);
                    $stmt->execute();

                    // Loop through the results and create options for the dropdown
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $product_name = $row['name'];
                        echo "<option value='$product_name'>$product_name</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Add two more similar dropdowns for the other two products -->

            <!-- Other order details like quantity, etc. -->

            <button type="submit" class="btn btn-primary">Place Order</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>