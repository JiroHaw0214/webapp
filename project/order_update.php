<?php
require_once 'session_check.php';
checkSession();
?>
<!DOCTYPE HTML>
<html>

<head>
    <title>Order Update</title>
    <!-- Latest compiled and minified Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

</head>

<body>
    <!-- container -->
    <div class="container">
        <?php include 'includes/navbar.php'; ?>
        <div class="p-3">
            <h1>Update Order</h1>
        </div>
        <?php
        $order_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');

        include 'config/database.php';

        // 查询订单信息，包括用户ID和订单日期时间
        $order_info_query = "SELECT order_summary.id as order_id, customers.first_name, customers.last_name, order_summary.order_date FROM order_summary
                            INNER JOIN customers ON order_summary.customer_id = customers.id
                            WHERE order_summary.id=:id";
        $order_info_stmt = $con->prepare($order_info_query);
        $order_info_stmt->bindParam(":id", $order_id);
        $order_info_stmt->execute();
        $order_info = $order_info_stmt->fetch(PDO::FETCH_ASSOC);

        $customer_query = "SELECT id, username FROM customers";
        $customer_stmt = $con->prepare($customer_query);
        $customer_stmt->execute();
        $customers = $customer_stmt->fetchAll(PDO::FETCH_ASSOC);

        $product_query = "SELECT id, name FROM products";
        $product_stmt = $con->prepare($product_query);
        $product_stmt->execute();
        $products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

        $order_summary_query = "SELECT * FROM order_summary WHERE id=:id";
        $order_summary_stmt = $con->prepare($order_summary_query);
        $order_summary_stmt->bindParam(":id", $order_id);
        $order_summary_stmt->execute();
        $order_summaries = $order_summary_stmt->fetch(PDO::FETCH_ASSOC);

        $order_details_query = "SELECT * FROM order_details WHERE order_id=:order_id";
        $order_details_stmt = $con->prepare($order_details_query);
        $order_details_stmt->bindParam(":order_id", $order_id);
        $order_details_stmt->execute();
        $order_details = $order_details_stmt->fetchAll(PDO::FETCH_ASSOC);

        $errors = array();
        if ($_POST) {
            $product_id = $_POST["product"];
            $quantity = $_POST["quantity"];

            $noduplicate = array_unique($product_id);

            if (sizeof($noduplicate) != sizeof($product_id)) {
                //key  //val
                //Array ( [0] => 1 [1] => 2 [2] => 3 )
                foreach ($product_id as $key => $val) {
                    if (!array_key_exists($key, $noduplicate)) {
                        $errors[] = "Duplicated products have been chosen";
                        unset($quantity[$key]);
                    }
                }
            }
            $product_id = array_values($noduplicate);
            $quantity = array_values($quantity);

            //如果有duplicate就算                         //没有就跑这个
            $selected_product_count = isset($noduplicate) ? count($noduplicate) : count($order_details);

            try {

                if (isset($selected_product_count)) {
                    for ($i = 0; $i < $selected_product_count; $i++) {
                        if ($product_id[$i] == "") {
                            $errors[] = " Please choose the product for NO " . $i + 1 . ".";
                        }

                        if ($quantity[$i] == 0 || empty($quantity[$i])) {
                            $errors[] = "Quantity cannot be zero or empty.";
                        } else if ($quantity[$i] < 0) {
                            $errors[] = "Quantity cannot be negative number.";
                        }
                    }
                }

                if (!empty($errors)) {
                    echo "<div class='alert alert-danger role='alert'>";
                    foreach ($errors as $error) {
                        echo $error . "<br>";
                    }
                    echo "</div>";
                } else {
                    date_default_timezone_set('Asia/Kuala_Lumpur');
                    $order_date = date('Y-m-d H:i:s');
                    $delete_details_query = "DELETE FROM order_details WHERE order_id=:order_id";
                    $delete_details_stmt = $con->prepare($delete_details_query);
                    $delete_details_stmt->bindParam(":order_id", $order_id);
                    $delete_details_stmt->execute();

                    // Insert updated order details into the database
                    for ($i = 0; $i < $selected_product_count; $i++) {
                        $order_details_query = "INSERT INTO order_details SET order_id=:order_id, product_id=:product_id, quantity=:quantity";
                        $order_details_stmt = $con->prepare($order_details_query);
                        $order_details_stmt->bindParam(":order_id", $order_id);
                        $order_details_stmt->bindParam(":product_id", $product_id[$i]);
                        $order_details_stmt->bindParam(":quantity", $quantity[$i]);
                        $order_details_stmt->execute();
                    }
                    echo "<div class='alert alert-success' role='alert'>Order Update Successfully.</div>";
                    $_POST = array();
                }
            } catch (PDOException $exception) {
                echo '<div class="alert alert-danger role=alert">' . $exception->getMessage() . '</div>';
            }
        }
        ?>
        <div>
            <form action="" method="post">
                <div class="mb-3">
                    <label for="orderId" class="form-label">Order ID</label>
                    <p class="form-control-static" id="orderId"><?php echo $order_info['order_id']; ?></p>
                </div>
                <div class="mb-3">
                    <label for="customerName" class="form-label">Customer Name</label>
                    <p class="form-control-static" id="customerName"><?php echo $order_info['first_name'] . ' ' . $order_info['last_name']; ?></p>
                </div>
                <div class="mb-3">
                    <label for="orderDateTime" class="form-label">Order Date and Time</label>
                    <p class="form-control-static" id="orderDateTime"><?php echo $order_info['order_date']; ?></p>
                </div>

                <table class="table table-hover table-responsive table-bordered" id="row_del">
                    <tr>
                        <th>NO.</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                    <?php

                    $product_loop = empty($error) ? count($order_details) : count($product_id);
                    for ($x = 0; $x < $product_loop; $x++) {

                    ?>
                        <tr class="pRow">
                            <td class="col-1">
                                <?php echo $x + 1; ?>
                            </td>
                            <td>
                                <select name="product[]" id="product" class="form-select" value>
                                    <option value="">Choose a Product</option>
                                    <?php
                                    for ($i = 0; $i < count($products); $i++) {
                                        //一样就出来        不一样就不出
                                        if ($_POST) {
                                            if ($product_id[$x] == $products[$i]['id']) {
                                                $product_selected = "selected";
                                            } else {
                                                $product_selected = '';
                                            }
                                        } else if ($products[$i]['id'] == $order_details[$x]['product_id']) {
                                            $product_selected = "selected";
                                        } else {
                                            $product_selected = '';
                                        }
                                        echo "<option value='{$products[$i]['id']}' $product_selected>{$products[$i]['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="quantity[]" id="quantity" value="<?php echo isset($quantity) ? $quantity[$x] : $order_details[$x]['quantity'] ?>">
                            </td>
                            <td>
                                <input href='#' onclick='deleteRow(this)' class='btn d-flex justify-content-center btn-danger mt-1' readonly value="Delete" />
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
                    <button type="submit" class="btn btn-primary">Place Order</button>
                    <a href="order_read.php" class="btn btn-danger">Back to Read Order Summary</a>
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
                        const [productsSelect, quantityInput] = clone.querySelectorAll('select[name="product[]"], input[name="quantity[]"]');
                        productsSelect.value = "";
                        quantityInput.value = 1;
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
                        alert("You need order at least one item.");
                    }
                }
            </script>
        </div>
    </div> <!-- end .container -->

    <!-- confirm delete record will be here -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>