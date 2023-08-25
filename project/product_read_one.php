<?php
require_once 'session_check.php';
checkSession();
?>
<!DOCTYPE HTML>
<html>

<head>
    <title>Product Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

</head>

<body>
    <!-- container -->
    <div class="container">
        <?php
        include 'includes/navbar.php';
        ?>

        <div class="p-3">
            <h1>Product Detail</h1>
        </div>

        <!-- PHP read one record will be here -->
        <?php
        // get passed parameter value, in this case, the record ID
        // isset() is a PHP function used to verify if a value is there or not
        $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');

        //include database connection
        include 'config/database.php';

        // read current record's data
        try {
            // prepare select query
            $query = "SELECT products.id, products.name, products.description, products.promotion_price, products.price, products.manufacture_date, products.expired_date, products.image,
            category.category_name FROM products INNER JOIN category  ON products.category_id = category.id WHERE products.id=:id";
            $stmt = $con->prepare($query);

            // Bind the parameter
            $stmt->bindParam(":id", $id);

            // execute our query
            $stmt->execute();

            // store retrieved row to a variable
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // values to fill up our form
            $name = $row['name'];
            $description = $row['description'];
            $category_name = $row['category_name'];
            $price = $row['price'];
            $promotion_price = $row['promotion_price'];
            $manufacture_date = $row['manufacture_date'];
            $expired_date = $row['expired_date'];
            $image = $row['image'];
        }

        // show error
        catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }
        ?>

        <!-- HTML read one record table will be here -->
        <!-- we have our html table here where the record will be displayed -->
        <table class='table table-hover table-responsive table-bordered'>
            <tr>
                <td>Name</td>
                <td><?php echo htmlspecialchars($name, ENT_QUOTES);  ?></td>
            </tr>
            <tr>
                <td>Category</td>
                <td><?php echo htmlspecialchars($category_name, ENT_QUOTES);  ?></td>
            </tr>
            <tr>
                <td>Description</td>
                <td><?php echo htmlspecialchars($description, ENT_QUOTES);  ?></td>
            </tr>
            <tr>
                <td>Price</td>
                <td><?php echo htmlspecialchars("RM" . $price, ENT_QUOTES);  ?></td>
            </tr>
            <?php if (!empty($promotion_price) && $promotion_price > 0) : ?>
                <tr>
                    <td>Promotion Price</td>
                    <td><?php echo htmlspecialchars("RM" . $promotion_price, ENT_QUOTES); ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td>Manufacture Date</td>
                <td><?php echo htmlspecialchars($manufacture_date, ENT_QUOTES);  ?></td>
            </tr>
            <?php if (!empty($expired_date)) : ?>
                <tr>
                    <td>Expired Date</td>
                    <td><?php echo htmlspecialchars($expired_date, ENT_QUOTES); ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td>Image</td>
                <td>
                    <?php
                    if (!empty($image)) {
                        echo "<img src='uploads/{$image}' class='img-fluid' alt='Product Image'>";
                    } else {
                        echo '<img src="img/product.jpg" height="100px" alt="">';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <a href='product_read.php' class='btn btn-danger'>Back to Product List</a>
                </td>
            </tr>
        </table>
    </div> <!-- end .container -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>

</body>

</html>