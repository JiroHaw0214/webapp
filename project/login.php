<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
                        // Login successful, set session variable and redirect to index.php
                        $_SESSION['user_id'] = $row['id'];
                        header("Location: index.php");
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
<!DOCTYPE HTML>
<html>

<head>
    <title>Product Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 100px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4 login-container">
                <div class="login-header">
                    <h2>Welcome to Product Management System</h2>
                    <p>Please login to access the system</p>
                </div>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <div class="mb-3">
                        <label for="username_email" class="form-label">Username/Email</label>
                        <input type="text" name="username_email" class="form-control" id="username_email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="password">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
