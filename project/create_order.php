<!DOCTYPE HTML>
<html>

<head>
    <title>Create New Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <!-- container -->
    <div class="container">
        <div class="page-header">
            <h1>Create New Order</h1>
        </div>

        <!-- Display success message -->
        <?php
        if (isset($_GET['order_created']) && $_GET['order_created'] == 1) {
            echo '<div class="alert alert-success">Order created successfully!</div>';
        }
        ?>

        <!-- Create New Order Form -->
        <form action="process_order.php" method="POST">
            <!-- Customer listing select menu -->
            <div class="mb-3">
                <label for="customer">Select Customer:</label>
                <select class="form-select" name="customer" id="customer">
                    <?php
                    // Include database connection
                    include 'config/database.php';

                    // Fetch customers from the database
                    $customer_query = "SELECT username FROM customers";
                    $customer_stmt = $con->prepare($customer_query);
                    $customer_stmt->execute();
                    while ($customer_row = $customer_stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . $customer_row['username'] . "'>" . $customer_row['username'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Product dropdown menus and Quantity input fields -->
            <?php
            // Fetch products from the database 
            $product_query = "SELECT id, name FROM products";
            $product_stmt = $con->prepare($product_query);
            $product_stmt->execute();
            for ($i = 1; $i <= 3; $i++) { // Assuming you want to allow a maximum of three products per order
                echo "<div class='mb-3'>";
                echo "<label for='product_$i'>Select Product $i:</label>";
                echo "<select class='form-select' name='product_$i' id='product_$i'>";
                echo "<option value=''>Select a product</option>";
                while ($product_row = $product_stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $product_row['id'] . "'>" . $product_row['name'] . "</option>";
                }
                // Reset the product statement to fetch products again
                $product_stmt->execute();
                echo "</select>";
                echo "</div>";

                echo "<div class='mb-3'>";
                echo "<label for='quantity_$i'>Quantity for Product $i:</label>";
                echo "<input type='number' name='quantity_$i' id='quantity_$i' class='form-control'>";
                echo "</div>";
            }
            ?>

            <button type="submit" class="btn btn-primary">Create Order</button>
        </form>

    </div> <!-- end .container -->

</body>

</html>