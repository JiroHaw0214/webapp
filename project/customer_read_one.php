<!DOCTYPE HTML>
<html>

<head>
    <title>Customer Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container">
        <div class="page-header">
            <h1>Customer Detail</h1>
        </div>
        <?php
        // Get the passed parameter value, in this case, the customer ID
        // isset() is a PHP function used to verify if a value is there or not
        $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Customer ID not found.');

        // Include the database connection
        include 'config/database.php';

        // Read the current customer's data
        try {
            // Prepare the select query
            $query = "SELECT id, username, first_name, last_name, email, gender, date_of_birth FROM customers WHERE id = :id";
            $stmt = $con->prepare($query);

            // Bind the parameter
            $stmt->bindParam(":id", $id);

            // Execute the query
            $stmt->execute();

            // Store the retrieved row to a variable
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Values to fill up our form
            $username = $row['username'];
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $email = $row['email'];
            $gender = $row['gender'];
            $date_of_birth = $row['date_of_birth'];
        }

        // Show error
        catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }
        ?>

        <!-- HTML read one customer table will be here -->
        <table class="table table-hover table-responsive table-bordered">
            <tr>
                <td>Username</td>
                <td><?php echo htmlspecialchars($username, ENT_QUOTES); ?></td>
            </tr>
            <tr>
                <td>First Name</td>
                <td><?php echo htmlspecialchars($first_name, ENT_QUOTES); ?></td>
            </tr>
            <tr>
                <td>Last Name</td>
                <td><?php echo htmlspecialchars($last_name, ENT_QUOTES); ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><?php echo htmlspecialchars($email, ENT_QUOTES); ?></td>
            </tr>
            <tr>
                <td>Gender</td>
                <td><?php echo htmlspecialchars($gender, ENT_QUOTES); ?></td>
            </tr>
            <tr>
                <td>Date of Birth</td>
                <td><?php echo htmlspecialchars($date_of_birth, ENT_QUOTES); ?></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <a href="customer_read.php" class="btn btn-danger">Back to Customer List</a>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>