<?php
require_once 'session_check.php';
checkSession();
?>
<!DOCTYPE HTML>
<html>

<head>
    <title>Update Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <?php include 'includes/navbar.php'; ?>
        <div class="page-header">
            <h1>Update Order</h1>
        </div>
        <?php
        include 'config/database.php';
        // Check if order ID is provided
        if (!isset($_GET['id'])) {
            echo "<div class='alert alert-danger' role='alert'>Order ID not provided.</div>";
            exit;
        }
        $order_id = $_GET['id'];
        // Fetch order details from the database
        $order_query = "SELECT * FROM order_summary WHERE id = :order_id";
        $order_stmt = $con->prepare($order_query);
        $order_stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $order_stmt->execute();
        $order = $order_stmt->fetch(PDO::FETCH_ASSOC);
        // Check if the order exists
        if (!$order) {
            echo "<div class='alert alert-danger' role='alert'>Order with ID $order_id not found.</div>";
            exit;
        }
        $customer_id = $order['customer_id'];
        date_default_timezone_set('Asia/Kuala_Lumpur');
        $order_date = date('Y-m-d H:i:s');
        $error = array();
        $product_id = '';
        if ($_POST) {
            $product_id = $_POST["product"];
            $quantity = $_POST["quantity"];
            $selected_product_count = count($_POST['product']);
            try {
                // Validate the form data
                if (isset($selected_product_count)) {
                    $selected_products = array();
                    for ($i = 0; $i < $selected_product_count; $i++) {
                        if ($product_id[$i] == "") {
                            $error[] = " Please choose product " . ($i + 1) . ".";
                        }
                        if ($quantity[$i] == 0 || empty($quantity[$i])) {
                            $error[] = "Quantity cannot be zero or empty.";
                        } else if ($quantity[$i] < 0) {
                            $error[] = "Quantity cannot be negative.";
                        } else if (!is_numeric($quantity[$i])) {
                            $error[] = "Quantity must be numeric.";
                        }
                        // Check for duplicate product selection
                        if (in_array($product_id[$i], $selected_products)) {
                            $error[] = "Product " . ($i + 1) . " is selected multiple times.";
                        } else {
                            $selected_products[] = $product_id[$i];
                        }
                    }
                }

                if (!empty($error)) {
                    echo "<div class='alert alert-danger' role='alert'>";
                    foreach ($error as $error_message) {
                        echo $error_message . "<br>";
                    }
                    echo "</div>";
                } else {
                    // Update the order in the database
                    $update_order_query = "UPDATE order_summary SET order_date=:order_date WHERE id=:order_id";
                    $update_order_stmt = $con->prepare($update_order_query);
                    $update_order_stmt->bindParam(":order_date", $order_date);
                    $update_order_stmt->bindParam(":order_id", $order_id, PDO::PARAM_INT);
                    $update_order_stmt->execute();

                    // Delete existing order details for this order
                    $delete_details_query = "DELETE FROM order_details WHERE order_id=:order_id";
                    $delete_details_stmt = $con->prepare($delete_details_query);
                    $delete_details_stmt->bindParam(":order_id", $order_id, PDO::PARAM_INT);
                    $delete_details_stmt->execute();

                    // Insert updated order details into the database
                    for ($i = 0; $i < $selected_product_count; $i++) {
                        $order_details_query = "INSERT INTO order_details SET order_id=:order_id, product_id=:product_id, quantity=:quantity";
                        $order_details_stmt = $con->prepare($order_details_query);
                        $order_details_stmt->bindParam(":order_id", $order_id, PDO::PARAM_INT);
                        $order_details_stmt->bindParam(":product_id", $product_id[$i], PDO::PARAM_INT);
                        $order_details_stmt->bindParam(":quantity", $quantity[$i], PDO::PARAM_INT);
                        $order_details_stmt->execute();
                    }

                    echo "<div class='alert alert-success' role='alert'>Order updated successfully.</div>";
                }
            } catch (PDOException $exception) {
                echo '<div class="alert alert-danger role="alert">' . $exception->getMessage() . '</div>';
            }
        }
        ?>
        <div>
            <form action="" method="post">
                <table class="table table-hover table-responsive table-bordered" id="row_del">
                    <tr>
                        <th>NO.</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    // Fetch all products from the database
                    $product_query = "SELECT id, name FROM products";
                    $product_stmt = $con->prepare($product_query);
                    $product_stmt->execute();
                    $products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Fetch existing order details from the database
                    $order_details_query = "SELECT * FROM order_details WHERE order_id=:order_id";
                    $order_details_stmt = $con->prepare($order_details_query);
                    $order_details_stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
                    $order_details_stmt->execute();
                    $order_details = $order_details_stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Loop through existing order details and display them
                    foreach ($order_details as $i => $order_detail) {
                        $product_id = $order_detail['product_id'];
                        $quantity = $order_detail['quantity'];
                    ?>
                        <tr class="pRow">
                            <td class="col-1">
                                <?php echo $i + 1; ?>
                            </td>
                            <td>
                                <select name="product[]" id="product" class="form-select" value>
                                    <option value="">Choose a Product</option>
                                    <?php
                                    foreach ($products as $product) {
                                        $product_selected = $product['id'] == $product_id ? "selected" : "";
                                        echo "<option value='{$product['id']}' $product_selected>{$product['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="quantity[]" id="quantity" value="<?php echo $quantity; ?>">
                            </td>
                            <td>
                                <?php
                                if ($i == 0) {
                                    echo "<input class='btn d-flex justify-content-center btn-danger mt-1' disabled value='Delete' />";
                                } else {
                                    echo "<input onclick='deleteRow(this)' class='btn d-flex justify-content-center btn-danger mt-1' readonly value='Delete' />";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php }
                    ?>
                    <tr>
                        <td></td>
                        <td>
                            <input type="button" value="Add More Product" class="btn btn-success add_one" />
                        </td>
                        <td></td>
                    </tr>
                </table>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Update Order</button>
                    <a href="order_read.php" class="btn btn-danger">Back to Read Order List</a>
                </div>
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKr  diJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>