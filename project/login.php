<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if form data is submitted via POST method and include the necessary database connection file
    include 'config/database.php';

    $username_email = $_POST['username_email'];
    $password = $_POST['password'];

    if (empty($username_email) & empty($password)) {
        $_SESSION['message'] = "Please enter username/email and password.";
    }
    elseif (empty($username_email)) {
        $_SESSION['message'] = "Please enter your username/email.";
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
                        exit;
                    } else {
                        $_SESSION['message'] = "Inactive account. Please contact the administrator.";
                    }
                } else {
                    $_SESSION['message'] = "Incorrect password.";
                }
            } else {
                $_SESSION['message'] = "Username/Email not found.";
            }
        } catch (PDOException $exception) {
            $_SESSION['message'] = $exception->getMessage();
        }
    }

    header("Location: login.php"); // Redirect to login.php to display the message
    exit;
} elseif (isset($_SESSION['user_id'])) {
    // If the user is already logged in, redirect to index.php
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE HTML>
<html>

<head>
    <title>DV Product Management System</title>
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

        /* Center the button */
        .login-button {
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4 login-container">
                <?php
                if (isset($_SESSION['message'])) {
                    echo '<div class="alert alert-danger text-center">' . $_SESSION['message'] . '</div>';
                    unset($_SESSION['message']); // Clear the message after displaying it
                }
                ?>
                <div class="login-header">
                    <img src="img/dv.png" alt="Logo" width="100">
                    <h2>Dream Vanguard Product Management System</h2>
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
                    <div class="login-button">
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>