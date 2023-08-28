<?php
require_once 'session_check.php';
checkSession();
?>

<!DOCTYPE HTML>
<html>

<head>
    <title>Update Product</title>
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

    <!-- Container -->
    <div class="container">
        <?php include 'includes/navbar.php'; ?>
        <div class="p-3">
            <h1>Update Product</h1>
        </div>
        <?php
        $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');

        include 'config/database.php';

        try {
            $query = "SELECT id, name, description, price, category_id, promotion_price, manufacture_date, expired_date, image FROM products WHERE id = ? LIMIT 0,1";
            $stmt = $con->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $name = $row['name'];
            $description = $row['description'];
            $price = $row['price'];
            $category = $row['category_id'];
            $promotion_price = $row['promotion_price'];
            $manufacture_date = $row['manufacture_date'];
            $expired_date = $row['expired_date'];
            $image = $row['image'];
            $price = number_format($price, 2);
            $promotion_price = ($promotion_price != null && $promotion_price != 0) ? number_format($promotion_price, 2) : '';
        } catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }
        ?>

        <?php
        if ($_POST) {
            try {
                $name = htmlspecialchars(strip_tags($_POST['name']));
                $description = htmlspecialchars(strip_tags($_POST['description']));
                $price = htmlspecialchars(strip_tags($_POST['price']));
                $category = htmlspecialchars(strip_tags($_POST['category_id']));
                $promotion_price = htmlspecialchars(strip_tags($_POST['promotion_price']));
                $manufacture_date = htmlspecialchars(strip_tags($_POST['manufacture_date']));
                $expired_date = htmlspecialchars(strip_tags($_POST['expired_date']));

                $errors = [];
                $duplicateQuery = "SELECT COUNT(*) as count FROM products WHERE name = :product_name AND id != :product_id";
                $duplicateStmt = $con->prepare($duplicateQuery);
                $duplicateStmt->bindParam(':product_name', $name);
                $duplicateStmt->bindParam(':product_id', $id);
                $duplicateStmt->execute();
                $duplicateResult = $duplicateStmt->fetch(PDO::FETCH_ASSOC);
                
                if (empty($name)) {
                    $errors[] = "Name cannot be empty.";
                }elseif ($duplicateResult['count'] > 0) {
                    $errors[] = "Product name already exists. Please choose a different name.";

                }

                if (empty($description)) {
                    $errors[] = "Description cannot be empty.";
                }

                if (empty($manufacture_date)) {
                    $errors[] = "Manufacture Date cannot be empty.";
                } else if (empty($expired_date)) {
                    $expired_date = null;
                } else if (!empty($expired_date) && strtotime($expired_date) < strtotime($manufacture_date)) {
                    $errors[] = "Expired Date cannot be earlier than Manufacture Date.";
                }

                if (empty($promotion_price) || $promotion_price === '0') {
                    $promotion_price = null;
                }

                if (!empty($promotion_price) && !is_numeric($promotion_price)) {
                    $errors[] = "Promotion price must be numbers.";
                }

                if (empty($price) || !is_numeric($price) || $price <= 0) {
                    $errors[] = "Price must be a number greater than 0.";
                }

                if (empty($errors)) {
                    $price = (float) $price;
                    $promotion_price = (float) $promotion_price;

                    if ($promotion_price > $price) {
                        $errors[] = "Promotion Price cannot be greater than Price.";
                    } else {
                        // Check if the delete_image checkbox is checked
                        if (isset($_POST['delete_image'])) {
                            if (!empty($image) && file_exists("uploads/{$image}")) {
                                unlink("uploads/{$image}");
                                $image = null;
                            }
                        }

                        if (!empty($_FILES["new_image"]["name"])) {
                            if (!empty($image) && file_exists("uploads/{$image}")) {
                                unlink("uploads/{$image}");
                            }
                            $new_image = $_FILES["new_image"];
                            $upload_dir = "uploads/";
                            $original_image_name = basename($new_image["name"]);
                            $imageFileType = strtolower(pathinfo($original_image_name, PATHINFO_EXTENSION));
                            $max_file_size = 524288; // 512 KB

                            // Check if file name already exists
                            $new_image_name = sha1_file($new_image["tmp_name"]) . "-" . $original_image_name;
                            $target_path = $upload_dir . $new_image_name;

                            if (file_exists($target_path)) {
                                $errors[] = "Image already exists. Try to change the file name.";
                            } else {
                                // Check file type and size
                                $allowed_extensions = array("jpg", "jpeg", "png", "gif");
                                if (!in_array($imageFileType, $allowed_extensions)) {
                                    $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
                                } elseif ($new_image["size"] > $max_file_size) {
                                    $errors[] = "Image must be less than 512 KB in size.";
                                } else {
                                    // Check if the image is square
                                    list($width, $height) = getimagesize($new_image["tmp_name"]);
                                    if ($width != $height) {
                                        $errors[] = "Only square image allowed.";
                                    } else {
                                        // Move the uploaded image to the target directory
                                        if (move_uploaded_file($new_image["tmp_name"], $target_path)) {
                                            $image = $new_image_name;
                                        } else {
                                            $errors[] = "Failed to upload the new image.";
                                        }
                                    }
                                }
                            }
                        }

                        if (empty($errors)) {
                            $query = "UPDATE products
                                SET name=:name, description=:description,
                                price=:price, category_id=:category_id, promotion_price=:promotion_price, manufacture_date=:manufacture_date, expired_date=:expired_date, image=:image
                                WHERE id = :id";

                            $stmt = $con->prepare($query);

                            $stmt->bindParam(':name', $name);
                            $stmt->bindParam(':description', $description);
                            $stmt->bindParam(':price', $price);
                            $stmt->bindParam(':category_id', $category);
                            $stmt->bindParam(':promotion_price', $promotion_price);
                            $stmt->bindParam(':manufacture_date', $manufacture_date);
                            $stmt->bindParam(':expired_date', $expired_date);
                            $stmt->bindParam(':image', $image);
                            $stmt->bindParam(':id', $id);

                            if ($stmt->execute()) {
                                echo "<div class='alert alert-success'>Record was updated.</div>";
                            } else {
                                echo "<div class='alert alert-danger'>Unable to update record. Please try again.</div>";
                            }
                        }
                    }
                }

                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                }
            } catch (PDOException $exception) {
                die('ERROR: ' . $exception->getMessage());
            }
        }
        ?>

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
                    <td>Category</td>
                    <td>
                        <select name='category_id' class='form-select'>
                            <?php
                            $categoryQuery = "SELECT id, category_name FROM category";
                            $categoryStmt = $con->prepare($categoryQuery);
                            $categoryStmt->execute();
                            while ($categoryRow = $categoryStmt->fetch(PDO::FETCH_ASSOC)) {
                                $selected = ($category == $categoryRow['id']) ? 'selected' : '';
                                echo "<option value='{$categoryRow['id']}' $selected>{$categoryRow['category_name']}</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Price (RM)</td>
                    <td><input type='text' name='price' value="<?php echo htmlspecialchars($price, ENT_QUOTES); ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Promotion Price (RM)</td>
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
                        <a href='product_read.php' class='btn btn-danger'>Back to Product List</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
</body>

</html>