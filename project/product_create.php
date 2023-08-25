<?php
require_once 'session_check.php';
checkSession();
?>

<!DOCTYPE HTML>
<html>

<head>
    <title>Add New Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container">
        <?php
        include 'includes/navbar.php';
        ?>

        <div class="p-3">
            <h1>Add New Product</h1>
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
                $promotion_price = isset($_POST['promotion_price']) ? $_POST['promotion_price'] : null; // Make promotion_price optional
                $manufacture_date = $_POST['manufacture_date'];
                $expired_date = isset($_POST['expired_date']) ? $_POST['expired_date'] : null; // Make expired_date optional

                //Datetime objects
                $dateStart = new DateTime($manufacture_date);

                $errorMessage = array();

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
                        $errorMessage[] = "Image must be less than 512 KB in size.";
                    }

                    // Check if the file already exists
                    if (file_exists($target_file)) {
                        $errorMessage[] = "Image already exists. Try to change the file name.";
                    }

                    list($width, $height) = getimagesize($_FILES['image']['tmp_name']);
                    if ($width != $height) {
                        $errorMessage[] = "Only square size images are allowed.";
                    }

                    if (empty($errorMessage)) {
                        // Try to move the uploaded file to the target directory
                        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                            // File uploaded successfully
                        } else {
                            $errorMessage[] = "Unable to upload the image.";
                        }
                    }
                } else {
                    $image = ""; // No image was uploaded
                }

                if (empty($name)) {
                    $errorMessage[] = "Name is required.";
                }
                if (empty($description)) {
                    $errorMessage[] = "Description is required.";
                }
                if (empty($price)) {
                    $errorMessage[] = "Price is required.";
                } elseif (!is_numeric($price)) {
                    $errorMessage[] = "Price must be a numeric value.";
                } else {
                    $price = (float)$price; // Convert to float
                }
                if (empty($manufacture_date)) {
                    $errorMessage[] = "Manufacture Date is required.";
                }
                if (!empty($promotion_price) && !is_numeric($promotion_price)) {
                    $errorMessage[] = "Promotion price must be a numeric value.";
                } elseif (!empty($promotion_price)) {
                    $promotion_price = (float)$promotion_price; // Convert to float
                }
                if (!empty($manufacture_date) && !empty($expired_date)) {
                    $dateStart = new DateTime($manufacture_date);
                    $dateEnd = new DateTime($expired_date);
                    if ($dateEnd <= $dateStart) {
                        $errorMessage[] = "Expired date must be later than the manufacture date.";
                        $expired_date = ""; // Clear the invalid value
                    }
                }
                if (!empty($promotion_price) && $promotion_price >= $price) {
                    $errorMessage[] = "Promotion price must be lower than the original price.";
                    $promotion_price = ""; // Clear the invalid value
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
                    $stmt->bindParam(':promotion_price', $promotion_price, PDO::PARAM_NULL); // Bind as NULL if it's empty
                    $stmt->bindParam(':manufacture_date', $manufacture_date);
                    $stmt->bindParam(':expired_date', $expired_date, PDO::PARAM_NULL); // Bind as NULL if it's empty
                    $stmt->bindParam(":image", $image);
                    $created = date('Y-m-d H:i:s'); // get the current date and time
                    $stmt->bindParam(':created', $created);

                    // Execute the query
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success m-3'>Record was saved.</div>";
                        // 清空输入字段
                        $name = $description = $price = $promotion_price = $manufacture_date = $expired_date = "";
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
                        <td>Price (RM)</td>
                        <td><input type='text' name='price' id='price' class='form-control' value="<?php echo isset($_POST['price']) ? $_POST['price'] : ''; ?>" /></td>
                    </tr>
                    <tr>
                        <td>Promotion Price (RM)</td>
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
                            <input type='submit' value='Add' class='btn btn-primary' />
                            <a href='product_read.php' class='btn btn-danger'>Back to Product List</a>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <!-- end container -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script>
        // JavaScript to validate price inputs
        $(document).ready(function() {
            $("#price, #promotion_price").on("input", function() {
                // Remove non-numeric characters except for "." (decimal point)
                $(this).val($(this).val().replace(/[^0-9.]/g, ''));

                // Ensure that there's only one decimal point
                if ($(this).val().split('.').length > 2) {
                    var parts = $(this).val().split('.');
                    $(this).val(parts[0] + '.' + parts.slice(1).join(''));
                }
            });
        });

        document.getElementById("name").value = "";
        document.getElementById("description").value = "";
        document.getElementById("price").value = "";
        document.getElementById("promotion_price").value = "";

        $(document).ready(function() {
            // 监听价格输入框的 blur 事件
            $("#price, #promotion_price").on("blur", function() {
                // 获取输入的值
                var inputValue = $(this).val();

                // 使用 parseFloat 将输入的值转换为浮点数
                var floatValue = parseFloat(inputValue);

                // 检查是否为有效数字
                if (!isNaN(floatValue)) {
                    // 使用 toFixed 方法将浮点数格式化为两位小数
                    var formattedValue = floatValue.toFixed(2);

                    // 更新输入框的值
                    $(this).val(formattedValue);
                }
            });
        });
    </script>
</body>

</html>