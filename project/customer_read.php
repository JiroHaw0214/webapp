<!DOCTYPE HTML>
<html>

<head>
    <title>Customer List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <div class="page-header">
            <h1>Customer List</h1>
        </div>
        <div class="mb-3">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" class="form-inline">
                <label for="search_keyword" class="form-label">Search Customer:</label>
                <input type="text" name="search_keyword" class="form-control mx-sm-2" id="search_keyword" placeholder="Enter name, username, or email" value="<?php echo isset($_GET['search_keyword']) ? htmlspecialchars($_GET['search_keyword'], ENT_QUOTES) : ''; ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <?php
        // Include the database connection
        include 'config/database.php';

        // Check if search keyword is provided
        $search_keyword = isset($_GET['search_keyword']) ? $_GET['search_keyword'] : '';

        // Prepare the query to select customers
        $query = "SELECT id, username, first_name, last_name, email, gender, date_of_birth FROM customers";

        if (!empty($search_keyword)) {
            // Add the search condition to the query
            $query .= " WHERE username LIKE :search_keyword
                        OR first_name LIKE :search_keyword
                        OR last_name LIKE :search_keyword
                        OR email LIKE :search_keyword";
        }

        $query .= " ORDER BY id DESC";

        // Prepare the query statement
        $stmt = $con->prepare($query);

        // Bind the search keyword parameter if it exists
        if (!empty($search_keyword)) {
            $search_keyword = '%' . $search_keyword . '%';
            $stmt->bindParam(':search_keyword', $search_keyword);
        }

        // Execute the query
        $stmt->execute();

        // Check if there are any records
        $num = $stmt->rowCount();

        // If there are records, display them in a table
        if ($num > 0) {
            echo "<table class='table table-hover table-responsive table-bordered'>";
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Username</th>";
            echo "<th>First Name</th>";
            echo "<th>Last Name</th>";
            echo "<th>Email</th>";
            echo "<th>Gender</th>";
            echo "<th>Date of Birth</th>";
            echo "<th>Action</th>";
            echo "</tr>";

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                echo "<tr>";
                echo "<td>{$id}</td>";
                echo "<td>{$username}</td>";
                echo "<td>{$first_name}</td>";
                echo "<td>{$last_name}</td>";
                echo "<td>{$email}</td>";
                echo "<td>{$gender}</td>";
                echo "<td>{$date_of_birth}</td>";
                echo "<td>";
                echo "<a href='customer_read_one.php?id={$id}' class='btn btn-info me-3'>Read</a>";
                echo "<a href='customer_update.php?id={$id}' class='btn btn-primary me-3'>Edit</a>";
                echo "<a href='#' onclick='delete_customer({$id});' class='btn btn-danger'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<div class='alert alert-danger'>No records found.</div>";
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>