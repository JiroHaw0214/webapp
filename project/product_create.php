<!DOCTYPE HTML>
<html>

<head>
    <title>Create Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        .error-message {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="page-header">
            <h1>Create Product</h1>
        </div>
        <?php
        $name = $description = $price = $promotion_price = $manufacture_date = $expired_date = '';

        if ($_POST) {
            // Check if form data is submitted via POST method and include the necessary database connection file
            include 'config/database.php';

            $errors = array(); // Array to store error messages

            // Check each field for empty values and store error messages in the $errors array
            if (empty($_POST['name'])) {
                $errors[] = "Name is required.";
            } else {
                $name = $_POST['name'];
            }
            if (empty($_POST['description'])) {
                $errors[] = "Description is required.";
            } else {
                $description = $_POST['description'];
            }
            if (empty($_POST['price'])) {
                $errors[] = "Price is required.";
            } elseif (!is_numeric($_POST['price'])) {
                $errors[] = "Price must be a numeric value.";
            } else {
                $price = $_POST['price'];
            }
            if (empty($_POST['promotion_price'])) {
                $errors[] = "Promotion Price is required.";
            } elseif (!is_numeric($_POST['promotion_price'])) {
                $errors[] = "Promotion Price must be a numeric value.";
            } else {
                $promotion_price = $_POST['promotion_price'];
            }
            if (empty($_POST['manufacture_date'])) {
                $errors[] = "Manufacture Date is required.";
            } else {
                $manufacture_date = $_POST['manufacture_date'];
            }
            if (empty($_POST['expired_date'])) {
                $errors[] = "Expired Date is required.";
            } else {
                $expired_date = $_POST['expired_date'];
            }

            // Check additional conditions
            if ($promotion_price >= $price && !in_array("Promotion price must be cheaper than the original price.", $errors)) {
                $errors[] = "Promotion price must be cheaper than the original price.";
            }
            if ($expired_date <= $manufacture_date && !in_array("Expired date must be later than the manufacture date.", $errors)) {
                $errors[] = "Expired date must be later than the manufacture date.";
            }

            // Check for error messages
            if (!empty($errors)) {
                // Display error messages for each field
                echo "<div class='alert alert-danger'>";
                foreach ($errors as $error) {
                    echo "<p class='error-message'>$error</p>";
                }
                echo "</div>";
            } else {
                try {
                    // Insert form data into the database
                    $query = "INSERT INTO products SET name=:name, description=:description, price=:price, promotion_price=:promotion_price, manufacture_date=:manufacture_date, expired_date=:expired_date, created=:created";
                    // Bind the parameters
                    $stmt = $con->prepare($query);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':price', $price);
                    $stmt->bindParam(':promotion_price', $promotion_price);
                    $stmt->bindParam(':manufacture_date', $manufacture_date);
                    $stmt->bindParam(':expired_date', $expired_date);
                    $created = date('Y-m-d H:i:s');
                    $stmt->bindParam(':created', $created);

                    // Execute the query
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Record was saved.</div>";

                        // Reset form fields
                        $name = $description = $price = $promotion_price = $manufacture_date = $expired_date = '';
                    } else {
                        echo "<div class='alert alert-danger'>Unable to save record.</div>";
                    }
                } catch (PDOException $exception) {
                    die('ERROR: ' . $exception->getMessage());
                }
            }
        }
        ?>

        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" id="name" value="<?php echo $name; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" id="description" rows="5"><?php echo $description; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="text" name="price" class="form-control" id="price" value="<?php echo $price; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="promotion_price" class="form-label">Promotion Price</label>
                        <input type="text" name="promotion_price" class="form-control" id="promotion_price" value="<?php echo $promotion_price; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="manufacture_date" class="form-label">Manufacture Date</label>
                        <input type="date" name="manufacture_date" class="form-control" id="manufacture_date" value="<?php echo $manufacture_date; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="expired_date" class="form-label">Expired Date</label>
                        <input type="date" name="expired_date" class="form-control" id="expired_date" value="<?php echo $expired_date; ?>">
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="index.php" class="btn btn-danger">Back to read products</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>
