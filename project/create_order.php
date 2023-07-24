<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <title>Create Order</title>
</head>

<body>
    <div class="container">
        <?php
        include 'includes/navbar.php';
        ?>
        <div class="page-header">
            <h1>Create New Order</h1>
        </div>

        <?php
        date_default_timezone_set('asia/Kuala_Lumpur');
        // include database connection
        include 'config/database.php';
        $product_query = "SELECT id, name FROM products";
        $product_stmt = $con->prepare($product_query);
        $product_stmt->execute();
        $products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($_POST) {

            try {
                $summary_query = "INSERT INTO order_summary SET customer_id=:customer, order_date=:order_date";
                $customer = $_POST['customer'];
                $order_date = date('Y-m-d H:i:s'); // get the current date and time
                $summary_stmt = $con->prepare($summary_query);
                $summary_stmt->bindParam(':customer', $customer);
                $summary_stmt->bindParam(':order_date', $order_date);
                $summary_stmt->execute();
                // order details
                $details_query = "INSERT INTO order_details SET order_id=:order_id, customer_id=:customer_id, product_id=:product_id, quantity=:quantity";
                $order_id = $con->lastInsertId();
                $details_stmt = $con->prepare($details_query);
                $product_id = $_POST['product'];
                $quantity = $_POST['quantity'];
                for ($i = 0; $i < count($product_id); $i++) {
                    // array
                    $details_stmt->bindParam(':order_id', $order_id);
                    $details_stmt->bindParam(':customer_id', $customer);
                    $details_stmt->bindParam(':product_id', $product_id[$i]);
                    $details_stmt->bindParam(':quantity', $quantity[$i]);
                    $details_stmt->execute();
                }
                echo "<div class='alert alert-success'>Order successfully placed.</div>";
            } catch (PDOException $exception) {
                echo "<div class='alert alert-danger'>Unable to place order.</div>";
            }
        }
        ?>

        <form action="" method="POST">
            <span>Select Customer</span>
            <select class="form-select mb-3" name="customer">
                <option value="" selected disabled>Choose a customer</option>
                <?php
                // Fetch customers from the database
                $query = "SELECT id, first_name FROM customers";
                $stmt = $con->prepare($query);
                $stmt->execute();
                $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Generate select options
                foreach ($customers as $customer) {
                    echo "<option value='{$customer['id']}'>{$customer['first_name']}</option>";
                } ?>
            </select>

            <table class='table table-hover table-responsive table-bordered' id="row_del">
                <tr>
                    <td class="text-center">#</td>
                    <td class="text-center">Product</td>
                    <td class="text-center">Quantity</td>
                    <td class="text-center">Action</td>
                </tr>
                <tr class="pRow">
                    <td class="text-center">1</td>
                    <td class="d-flex">
                        <select class="form-select" name="product[]">
                            <option value="" selected disabled>Choose a product</option>
                            <?php
                            // Generate select options
                            foreach ($products as $product) {
                                echo "<option value='{$product['id']}'>{$product['name']}</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td><input class="form-control" type="number" name="quantity[]"></td>
                    <td><input href='#' onclick='deleteRow(this)' class='btn d-flex justify-content-center btn-danger mt-1' value="Delete" /></td>
                </tr>
                <tr>
                    <td>

                    </td>
                    <td colspan="4">
                        <input type="button" value="Add More Product" class="btn btn-success add_one" />
                    </td>
                </tr>
                <tr>
                    <td>

                    </td>
                    <td colspan="4"><input type='submit' value='Place Order' class='btn btn-primary' /></td>
                </tr>
            </table>
        </form>
        <script>
            document.addEventListener('click', function(event) {
                if (event.target.matches('.add_one')) {
                    var rows = document.getElementsByClassName('pRow');
                    // Get the last row in the table
                    var lastRow = rows[rows.length - 1];
                    // Clone the last row
                    var clone = lastRow.cloneNode(true);
                    // Insert the clone after the last row
                    lastRow.insertAdjacentElement('afterend', clone);

                    // Loop through the rows
                    for (var i = 0; i < rows.length; i++) {
                        // Set the inner HTML of the first cell to the current loop iteration number
                        rows[i].cells[0].innerHTML = i + 1;
                    }
                }
            }, false);

            function deleteRow(r) {
                var total = document.querySelectorAll('.pRow').length;
                if (total > 1) {
                    var i = r.parentNode.parentNode.rowIndex;
                    document.getElementById("row_del").deleteRow(i);

                    var rows = document.getElementsByClassName('pRow');
                    for (var i = 0; i < rows.length; i++) {
                        // Set the inner HTML of the first cell to the current loop iteration number
                        rows[i].cells[0].innerHTML = i + 1;
                    }
                } else {
                    alert("You need to order at least one item.");
                }
            }
        </script>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>
