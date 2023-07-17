<!DOCTYPE HTML>
<html>

<head>
    <title>Create Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <h1>Create Category</h1>
        </div>
        <?php
        $category_name = '';
        $description = '';

        if ($_POST) {
            // Check if form data is submitted via POST method and include the necessary database connection file
            include 'config/database.php';

            $errors = array(); // Array to store error messages

            // Check each field for empty values and store error messages in the $errors array
            if (empty($_POST['category_name'])) {
                $errors[] = "Category Name is required.";
            } else {
                $category_name = $_POST['category_name'];
            }

            if (empty($_POST['description'])) {
                $errors[] = "Description is required.";
            } else {
                $description = $_POST['description'];
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
                    $query = "INSERT INTO category SET category_name=:category_name, description=:description";
                    // Bind the parameters
                    $stmt = $con->prepare($query);
                    $stmt->bindParam(':category_name', $category_name);
                    $stmt->bindParam(':description', $description);

                    // Execute the query
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Category created successfully.</div>";

                        // Reset form fields
                        $category_name = '';
                        $description = '';
                    } else {
                        echo "<div class='alert alert-danger'>Unable to create category.</div>";
                    }
                } catch (PDOException $exception) {
                    echo '<div class="alert alert-danger">' . $exception->getMessage() . '</div>';
                }
            }
        }
        ?>

        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
            <div class="mb-3">
                <label for="category_name" class="form-label">Category Name</label>
                <input type="text" name="category_name" class="form-control" id="category_name" value="<?php echo $category_name; ?>">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control" id="description" rows="5"><?php echo $description; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>