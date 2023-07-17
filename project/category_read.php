<!DOCTYPE HTML>
<html>

<head>
    <title>Category List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="page-header">
            <h1>Category List</h1>
        </div>

        <?php
        // Include the necessary database connection file
        include 'config/database.php';

        try {
            // Select all categories from the database
            $query = "SELECT * FROM category";
            $stmt = $con->prepare($query);
            $stmt->execute();

            // Check if there are any categories
            if ($stmt->rowCount() > 0) {
                echo '<table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Category ID</th>
                                <th>Category Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>';

                // Fetch and display each category record
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>
                            <td>' . $row['id'] . '</td>
                            <td>' . $row['category_name'] . '</td>
                            <td>' . $row['description'] . '</td>
                          </tr>';
                }

                echo '</tbody>
                      </table>';
            } else {
                echo '<div class="alert alert-info">No categories found.</div>';
            }
        } catch (PDOException $exception) {
            echo '<div class="alert alert-danger">' . $exception->getMessage() . '</div>';
        }
        ?>

        <a href="category_create.php" class="btn btn-primary">Create Category</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>