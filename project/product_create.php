<!DOCTYPE HTML>
<html>

<head>
    <title>Create Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <?php
        include 'includes/navbar.php';
        ?>

        <div class="page-header">
            <h1>Create Product</h1>
        </div>

        <?php
        if ($_POST) {
            // include database connection
            include 'config/database.php';
            try {
                // insert query
                $query = "INSERT INTO products SET name=:name, category_id=:category_id, description=:description, price=:price, promotion_price=:promotion_price, manufacture_date=:manufacture_date, expired_date=:expired_date, created=:created";
                // prepare query for execution
                $stmt = $con->prepare($query);
                $name = $_POST['name'];
                $category_id = $_POST['category_id'];
                $description = $_POST['description'];
                $price = $_POST['price'];
                $promotion_price = $_POST['promotion_price'];
                $manufacture_date = $_POST['manufacture_date'];
                $expired_date = $_POST['expired_date'];

                //Datetime objects
                $dateStart = new DateTime($manufacture_date);
                $dateEnd = new DateTime($expired_date);

                $errorMessage = array();

                if (empty($name)) {
                    $errorMessage[] = "Name is required.";
                }
                if (empty($description)) {
                    $errorMessage[] = "Description is required.";
                }
                if (empty($price)) {
                    $errorMessage[] = "Price is required.";
                } else if (!is_numeric($price)) {
                    $errorMessage[] = "Prices must be a numeric value.";
                }
                if (!is_numeric($promotion_price)) {
                    $errorMessage[] = "Promotion prices must be a numeric value.";
                }
                if (empty($manufacture_date)) {
                    $errorMessage[] = "Manufacture date is required.";
                }
                if (empty($expired_date)) {
                    $errorMessage[] = "Expired date is required.";
                }
                if ($promotion_price >= $price) {
                    $errorMessage[] = "Promotion price must be cheaper than the original price.";
                }
                if ($expired_date <= $manufacture_date) {
                    $errorMessage[] = "Expired date must be later than the manufacture date.";
                }


                if (!empty($errorMessage)) {
                    echo "<div class='alert alert-danger m-3'>";
                    foreach ($errorMessage as $displayErrorMessage) {
                        echo $displayErrorMessage . "<br>";
                    }
                    echo "</div>";
                } else {
                    // Bind the parameters
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':category_id', $category_id);
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':price', $price);
                    $stmt->bindParam(':promotion_price', $promotion_price);
                    $stmt->bindParam(':manufacture_date', $manufacture_date);
                    $stmt->bindParam(':expired_date', $expired_date);
                    $created = date('Y-m-d H:i:s'); // get the current date and time
                    $stmt->bindParam(':created', $created);

                    // Execute the query
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success m-3'>Record was saved.</div>";
                        $_POST = array();
                    } else {
                        echo "<div class='alert alert-danger m-3'>Unable to save the record.</div>";
                    }
                }
            }
            // show error
            catch (PDOException $exception) {
                die('ERROR: ' . $exception->getMessage());
            }
        }
        ?>

        <div class="p-3">
            <!-- html form here where the product information will be entered -->
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                <table class='table table-hover table-responsive table-bordered'>
                    <tr>
                        <td>Name</td>
                        <td><input type='text' name='name' id='name' class='form-control' value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" /></td>
                    </tr>
                    <tr>
                        <td>Category</td>
                        <td>
                            <select name="category_id" id="category_id" class="form-select">
                                <?php
                                include "config/database.php";
                                $mysql = "SELECT id, category_name FROM category";
                                $stmt = $con->prepare($mysql);
                                $stmt->execute();
                                $num = $stmt->rowCount();

                                if ($num > 0) {
                                    $options = array();
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $options[$row['id']] = $row['category_name'];
                                    }
                                }

                                foreach ($options as $id => $category_name) {
                                    echo "<option value='" . $id . "'>" . $category_name . "</option>";
                                }


                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Description</td>
                        <td><textarea name='description' id='description' class='form-control'><?php echo isset($_POST['description']) ? $_POST['description'] : ''; ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Price</td>
                        <td><input type='text' name='price' id='price' class='form-control' value="<?php echo isset($_POST['price']) ? $_POST['price'] : ''; ?>" /></td>
                    </tr>
                    <tr>
                        <td>Promotion Price</td>
                        <td><input type='text' name='promotion_price' id='promotion_price' class='form-control' value="<?php echo isset($_POST['promotion_price']) ? $_POST['promotion_price'] : ''; ?>" /></td>
                    </tr>
                    <tr>
                        <td>Manufacture Date</td>
                        <td><input type='date' name='manufacture_date' class='form-control' value="<?php echo isset($_POST['manufacture_date']) ? $_POST['manufacture_date'] : ''; ?>" /></td>
                    </tr>
                    <tr>
                        <td>Expired Date</td>
                        <td><input type='date' name='expired_date' class='form-control' value="<?php echo isset($_POST['expired_date']) ? $_POST['expired_date'] : ''; ?>" /></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type='submit' value='Save' class='btn btn-primary' />
                            <a href='product_read.php' class='btn btn-danger'>Back to read products</a>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <!-- end container -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>