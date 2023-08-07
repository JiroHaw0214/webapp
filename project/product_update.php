<?php
require_once 'session_check.php';
checkSession();
?>
<!DOCTYPE HTML>
<html>

<head>
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <!-- Custom CSS -->
    <style>
        .m-r-1em {
            margin-right: 1em;
        }

        .m-b-1em {
            margin-bottom: 1em;
        }

        .m-l-1em {
            margin-left: 1em;
        }

        .mt0 {
            margin-top: 0;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <!-- Container -->
    <div class="container">
        <div class="page-header">
            <h1>Update Product</h1>
        </div>
        <!-- PHP read record by ID will be here -->
        <?php
        // Get passed parameter value, in this case, the record ID
        // isset() is a PHP function used to verify if a value is there or not
        $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');
        // Include database connection
        include 'config/database.php';
        // Read current record's data
        try {
            // Prepare select query
            $query = "SELECT id, name, description, price, category_id, promotion_price, manufacture_date, expired_date FROM products WHERE id = ? LIMIT 0,1";
            $stmt = $con->prepare($query);
            // This is the first question mark
            $stmt->bindParam(1, $id);
            // Execute our query
            $stmt->execute();
            // Store retrieved row to a variable
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Values to fill up our form
            $name = $row['name'];
            $description = $row['description'];
            $price = $row['price'];
            $category = $row['category_id'];
            $promotion_price = $row['promotion_price'];
            $manufacture_date = $row['manufacture_date'];
            $expired_date = $row['expired_date'];
        }
        // Show error
        catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }
        ?>
        <!-- HTML form to update record will be here -->
        <!-- PHP post to update record will be here -->
        <?php
        // Check if form was submitted
        if ($_POST) {
            try {
                // Posted values
                $name = htmlspecialchars(strip_tags($_POST['name']));
                $description = htmlspecialchars(strip_tags($_POST['description']));
                $price = htmlspecialchars(strip_tags($_POST['price']));
                $category = htmlspecialchars(strip_tags($_POST['category_id']));
                $promotion_price = htmlspecialchars(strip_tags($_POST['promotion_price']));
                $manufacture_date = htmlspecialchars(strip_tags($_POST['manufacture_date']));
                $expired_date = htmlspecialchars(strip_tags($_POST['expired_date']));

                // Check if price and promotion price are valid numbers
                if (!is_numeric($price) || !is_numeric($promotion_price)) {
                    echo "<div class='alert alert-danger'>Price and Promotion Price must be valid numbers.</div>";
                } else if ($price <= 0 || $promotion_price <= 0) {
                    echo "<div class='alert alert-danger'>Price and Promotion Price must be large then 0.</div>";
                } else {
                    // Convert values to float for comparison
                    $price = (float) $price;
                    $promotion_price = (float) $promotion_price;

                    // Check if promotion price is less than price
                    if ($promotion_price > $price) {
                        echo "<div class='alert alert-danger'>Promotion Price cannot large than Price.</div>";
                    } else {
                        // Write update query
                        $query = "UPDATE products
                    SET name=:name, description=:description,
                    price=:price, category_id=:category_id, promotion_price=:promotion_price, manufacture_date=:manufacture_date, expired_date=:expired_date
                    WHERE id = :id";
                        // Prepare query for execution
                        $stmt = $con->prepare($query);
                        // Bind the parameters
                        $stmt->bindParam(':name', $name);
                        $stmt->bindParam(':description', $description);
                        $stmt->bindParam(':price', $price);
                        $stmt->bindParam(':category_id', $category);
                        $stmt->bindParam(':promotion_price', $promotion_price);
                        $stmt->bindParam(':manufacture_date', $manufacture_date);
                        $stmt->bindParam(':expired_date', $expired_date);
                        $stmt->bindParam(':id', $id);
                        // Execute the query
                        if ($stmt->execute()) {
                            echo "<div class='alert alert-success'>Record was updated.</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Unable to update record. Please try again.</div>";
                        }
                    }
                }
            }
            // Show errors
            catch (PDOException $exception) {
                die('ERROR: ' . $exception->getMessage());
            }
        }
        ?>
        <!-- We have our HTML form here where new record information can be updated -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id={$id}"); ?>" method="post">
            <table class='table table-hover table-responsive table-bordered'>
                <tr>
                    <td>Name</td>
                    <td><input type='text' name='name' value="<?php echo htmlspecialchars($name, ENT_QUOTES); ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td><textarea name='description' class='form-control'><?php echo htmlspecialchars($description, ENT_QUOTES); ?></textarea></td>
                </tr>
                <tr>
                    <td>Price</td>
                    <td><input type='text' name='price' value="<?php echo htmlspecialchars($price, ENT_QUOTES); ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Category</td>
                    <td><input type='text' name='category_id' value="<?php echo htmlspecialchars($category, ENT_QUOTES); ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Promotion Price</td>
                    <td><input type='text' name='promotion_price' value="<?php echo htmlspecialchars($promotion_price, ENT_QUOTES); ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Manufacture Date</td>
                    <td><input type='date' name='manufacture_date' value="<?php echo htmlspecialchars($manufacture_date, ENT_QUOTES); ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Expired Date</td>
                    <td><input type='date' name='expired_date' value="<?php echo htmlspecialchars($expired_date, ENT_QUOTES); ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type='submit' value='Save Changes' class='btn btn-primary' />
                        <a href='product_read.php' class='btn btn-danger'>Back to read products</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <!-- End .container -->
</body>

</html>