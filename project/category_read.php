<?php
require_once 'session_check.php';
checkSession();
?>
<!DOCTYPE HTML>
<html>

<head>
    <title>Category List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php
        include 'includes/navbar.php';
        ?>

        <div class="page-header">
            <h1>Category List</h1>
        </div>

        <?php
        // Include the necessary database connection file
        include 'config/database.php';

        $action = isset($_GET['action']) ? $_GET['action'] : "";

        // if it was redirected from delete.php
        if ($action == 'deleted') {
            echo "<div class='alert alert-success'>Record was deleted.</div>";
        }

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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>';

                // Fetch and display each category record
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    // creating new table row per record
                    echo "<tr>";
                    echo "<td>{$id}</td>";
                    echo "<td>{$category_name}</td>";
                    echo "<td>";
                    // read one record
                    echo "<a href='category_read_one.php?id={$id}' class='btn btn-info me-3'>Read</a>";

                    // we will use this links on next part of this post
                    echo "<a href='category_update.php?id={$id}' class='btn btn-primary me-3'>Edit</a>";

                    // we will use this links on next part of this post
                    echo "<a href='#' onclick='category_delete({$id});'  class='btn btn-danger'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                // end table
                echo "</table>";
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
    <script type='text/javascript'>
        // confirm record deletion
        function category_delete(id) {

            if (confirm('Are you sure?')) {
                // if user clicked ok,
                // pass the id to delete.php and execute the delete query
                window.location = 'category_delete.php?id=' + id;
            }
        }
    </script>
</body>

</html>