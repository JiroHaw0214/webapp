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

        .product-image {
            max-width: 300px;
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
            $query = "SELECT id, name, description, price, category_id, promotion_price, manufacture_date, expired_date, image FROM products WHERE id = ? LIMIT 0,1";
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
            $image = $row['image']; // Retrieve the image filename
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
                    echo "<div class='alert alert-danger'>Price and Promotion Price must be greater than 0.</div>";
                } else {
                    // Convert values to float for comparison
                    $price = (float) $price;
                    $promotion_price = (float) $promotion_price;

                    // Check if promotion price is less than price
                    if ($promotion_price > $price) {
                        echo "<div class='alert alert-danger'>Promotion Price cannot be greater than Price.</div>";
                    } else {
                        // Check if the user wants to delete the original image
                        if (isset($_POST['delete_image'])) {
                            // Delete the image file from the server
                            if (!empty($image) && file_exists("uploads/{$image}")) {
                                unlink("uploads/{$image}");
                            }
                            // Set the image field in the database to NULL
                            $image = null;
                        }

                        // Check if a new image is uploaded
                        if (!empty($_FILES["new_image"]["name"])) {
                            // Process the new image upload
                            $new_image = $_FILES["new_image"];
                            $upload_dir = "uploads/";
                            $image_name = basename($new_image["name"]);
                            $target_path = $upload_dir . $image_name;

                            // Check file type and size
                            $imageFileType = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
                            $allowed_extensions = array("jpg", "jpeg", "png", "gif");
                            $max_file_size = 524288; // 512 KB

                            if (!in_array($imageFileType, $allowed_extensions)) {
                                echo "<div class='alert alert-danger'>Only JPG, JPEG, PNG, and GIF files are allowed.</div>";
                            } elseif ($new_image["size"] > $max_file_size) {
                                echo "<div class='alert alert-danger'>Image must be less than 512 KB in size.</div>";
                            } else {
                                // Move the uploaded image to the target directory
                                if (move_uploaded_file($new_image["tmp_name"], $target_path)) {
                                    $image = $image_name;
                                } else {
                                    echo "<div class='alert alert-danger'>Failed to upload the new image.</div>";
                                }
                            }
                        }

                        // Write the update query
                        $query = "UPDATE products
                            SET name=:name, description=:description,
                            price=:price, category_id=:category_id, promotion_price=:promotion_price, manufacture_date=:manufacture_date, expired_date=:expired_date, image=:image
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
                        $stmt->bindParam(':image', $image); // Bind the image filename

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
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id={$id}"); ?>" method="post" enctype="multipart/form-data">
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
                </tr>f
                <tr>
                    <td>Expired Date</td>
                    <td><input type='date' name='expired_date' value="<?php echo htmlspecialchars($expired_date, ENT_QUOTES); ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Current Image</td>
                    <td>
                        <?php
                        if (!empty($image)) {
                            echo "<img src='uploads/{$image}' class='product-image' alt='Product Image'>";
                            echo "<br><input type='checkbox' name='delete_image' value='1'> Delete Current Image";
                        } else {
                            echo "No image available";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>New Image</td>
                    <td><input type='file' name='new_image' accept='image/*' class='form-control' /></td>
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
