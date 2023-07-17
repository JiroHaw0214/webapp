<!DOCTYPE HTML>
<html>

<head>
    <title>Login Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="page-header">
            <h1>Login Form</h1>
        </div>
        <?php
        if ($_POST) {
            // Check if form data is submitted via POST method and include the necessary database connection file
            include 'config/database.php';

            $username_email = $_POST['username_email'];
            $password = $_POST['password'];

            if (empty($username_email) || empty($password)) {
                echo "<div class='alert alert-danger'>Please enter username/email and password.</div>";
            } else {
                try {
                    // Check if the entered username/email and password match the data in the database
                    $query = "SELECT id, username, email, password, account_status FROM customers WHERE username = :username OR email = :email";
                    $stmt = $con->prepare($query);
                    $stmt->bindParam(':username', $username_email);
                    $stmt->bindParam(':email', $username_email);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                        // Verify password
                        if (password_verify($password, $row['password'])) {
                            // Check account status
                            if ($row['account_status'] == 'Active') {
                                // Login successful, redirect to index.php
                                header("Location: index.php");
                                exit;
                            } else {
                                echo "<div class='alert alert-danger'>Inactive account. Please contact the administrator.</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>Incorrect password.</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Username/Email not found.</div>";
                    }
                } catch (PDOException $exception) {
                    echo '<div class="alert alert-danger">' . $exception->getMessage() . '</div>';
                }
            }
        }
        ?>

        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
            <div class="mb-3">
                <label for="username_email" class="form-label">Username/Email</label>
                <input type="text" name="username_email" class="form-control" id="username_email">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>