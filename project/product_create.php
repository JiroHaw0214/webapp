<?php
require_once 'session_check.php';
checkSession();
?>
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
                $query = "INSERT INTO products SET name=:name, category_id=:category_id, description=:description, price=:price, promotion_price=:promotion_price, manufacture_date=:manufacture_date, expired_date=:expired_date, image=:image, created=:created";
                // prepare query for execution
                $stmt = $con->prepare($query);
                $name = $_POST['name'];
                $category_id = $_POST['category_id'];
                $description = $_POST['description'];
                $price = $_POST['price'];

                // Check if an image file was uploaded
                if (!empty($_FILES["image"]["name"])) {
                    $image = sha1_file($_FILES['image']['tmp_name']) . "-" . basename($_FILES["image"]["name"]);
                    $target_directory = "uploads/";
                    $target_file = $target_directory . $image;
                    $file_type = pathinfo($target_file, PATHINFO_EXTENSION);

                    $allowed_file_types = array("jpg", "jpeg", "png", "gif");

                    // Check file type
                    if (!in_array($file_type, $allowed_file_types)) {
                        $errorMessage[] = "<div>Only JPG, JPEG, PNG, GIF files are allowed.</div>";
                    }

                    // Check file size (less than 512 KB)
                    if ($_FILES['image']['size'] > 524288) {
                        $errorMessage[] = "<div>Image must be less than 512 KB in size.</div>";
                    }

                    // Check if the file already exists
                    if (file_exists($target_file)) {
                        $errorMessage[] = "<div>Image already exists. Try to change the file name.</div>";
                    }

                    if (empty($errorMessage)) {
                        // Try to move the uploaded file to the target directory
                        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                            // File uploaded successfully
                        } else {
                            $errorMessage[] = "<div>Unable to upload the image.</div>";
                        }
                    }
                } else {
                    $image = ""; // No image was uploaded
                }

                $promotion_price = $_POST['promotion_price'];
                $manufacture_date = $_POST['manufacture_date'];
                $expired_date = $_POST['expired_date'];

                //Datetime objects
                $dateStart = new DateTime($manufacture_date);
                $dateEnd = new DateTime($expired_date);

                $created = date('Y-m-d H:i:s'); // get the current date and time

                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':category_id', $category_id);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':promotion_price', $promotion_price);
                $stmt->bindParam(':manufacture_date', $manufacture_date);
                $stmt->bindParam(':expired_date', $expired_date);
                $stmt->bindParam(":image", $image);
                $stmt->bindParam(':created', $created);

                // Execute the query
                if ($stmt->execute()) {
                    echo "<div class='alert alert-success m-3'>Record was saved.</div>";
                } else {
                    echo "<div class='alert alert-danger m-3'>Unable to save the record.</div>";
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
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" enctype="multipart/form-data">
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
                        <td>Photo</td>
                        <td><input type="file" name="image" class="form-control" accept="image/*"></td>
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