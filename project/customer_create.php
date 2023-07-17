<!DOCTYPE HTML>
<html>

<head>
    <title>Create Customer</title>
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
            <h1>Create Customer</h1>
        </div>
        <?php
        $username = $password = $confirm_password = $first_name = $last_name = $email = $gender = $date_of_birth = '';

        if ($_POST) {
            // Check if form data is submitted via POST method and include the necessary database connection file
            include 'config/database.php';

            $errors = array(); // Array to store error messages

            // Check each field for empty values and store error messages in the $errors array
            if (empty($_POST['username'])) {
                $errors[] = "Username is required.";
            } else {
                $username = $_POST['username'];
            }
            if (empty($_POST['password'])) {
                $errors[] = "Password is required.";
            } else {
                $password = $_POST['password'];
            }
            if (empty($_POST['confirm_password'])) {
                $errors[] = "Confirm Password is required.";
            } else {
                $confirm_password = $_POST['confirm_password'];
                if ($password != $confirm_password) {
                    $errors[] = "Passwords do not match.";
                }
            }
            if (empty($_POST['first_name'])) {
                $errors[] = "First Name is required.";
            } else {
                $first_name = $_POST['first_name'];
            }
            if (empty($_POST['last_name'])) {
                $errors[] = "Last Name is required.";
            } else {
                $last_name = $_POST['last_name'];
            }
            if (empty($_POST['email'])) {
                $errors[] = "Email is required.";
            } else {
                $email = $_POST['email'];
            }
            if (empty($_POST['gender'])) {
                $errors[] = "Gender is required.";
            } else {
                $gender = $_POST['gender'];
            }
            if (empty($_POST['date_of_birth'])) {
                $errors[] = "Date of Birth is required.";
            } else {
                $date_of_birth = $_POST['date_of_birth'];
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
                    // Hash the password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    // Insert form data into the database
                    $query = "INSERT INTO customers SET username=:username, password=:password, first_name=:first_name, last_name=:last_name, gender=:gender, date_of_birth=:date_of_birth, email=:email, registration_datetime=:registration_datetime, account_status=:account_status";
                    // Bind the parameters
                    $stmt = $con->prepare($query);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':password', $hashed_password);
                    $stmt->bindParam(':first_name', $first_name);
                    $stmt->bindParam(':last_name', $last_name);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':gender', $gender);
                    $stmt->bindParam(':date_of_birth', $date_of_birth);
                    $registration_datetime = date('Y-m-d H:i:s');
                    $stmt->bindParam(':registration_datetime', $registration_datetime);
                    $account_status = "Active";
                    $stmt->bindParam(':account_status', $account_status);

                    // Execute the query
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Customer record was saved.</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Unable to save customer record.</div>";
                    }
                } catch (PDOException $exception) {
                    //  die('ERROR: ' . $exception->getMessage());
                    if ($exception->getCode() == 23000) {
                        echo '<div class= "alert alert-danger role=alert">' . 'Username has been taken' . '</div>';
                    } else {
                        echo '<div class= "alert alert-danger role=alert">' . $exception->getMessage() . '</div>';
                    }
                }
            }
        }
        ?>

        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" id="username" value="<?php echo $username; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[a-zA-Z0-9]{6,}" title="Minimum 6 characters, starts with a letter, and allows only _ or - in between." value="<?php echo $password; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" id="confirm_password" value="<?php echo $confirm_password; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" id="first_name" value="<?php echo $first_name; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" id="last_name" value="<?php echo $last_name; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" value="<?php echo $email; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gender</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender_male" value="Male" <?php if ($gender == 'Male') echo 'checked'; ?>>
                            <label class="form-check-label" for="gender_male">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender_female" value="Female" <?php if ($gender == 'Female') echo 'checked'; ?>>
                            <label class="form-check-label" for="gender_female">Female</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" id="date_of_birth" value="<?php echo $date_of_birth; ?>">
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="customer_read.php" class="btn btn-danger">Back to Customer List</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>