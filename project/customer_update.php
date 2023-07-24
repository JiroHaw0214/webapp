    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <title>Edit Customer Details</title>
    </head>

    <body>
        <div class="container">
            <?php
            // Include database connection and fetch customer data
            include 'config/database.php';

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Form is submitted, update customer details
                $customer_id = $_POST['customer_id'];
                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $email = $_POST['email'];
                $gender = $_POST['gender'];
                $username = $_POST['username'];
                $date_of_birth = $_POST['date_of_birth'];

                $update_query = "UPDATE customers SET first_name=:first_name, last_name=:last_name, email=:email, gender=:gender, username=:username, date_of_birth=:date_of_birth WHERE id=:customer_id";
                $update_stmt = $con->prepare($update_query);
                $update_stmt->bindParam(':first_name', $first_name);
                $update_stmt->bindParam(':last_name', $last_name);
                $update_stmt->bindParam(':email', $email);
                $update_stmt->bindParam(':gender', $gender);
                $update_stmt->bindParam(':username', $username);
                $update_stmt->bindParam(':date_of_birth', $date_of_birth);
                $update_stmt->bindParam(':customer_id', $customer_id);

                if ($update_stmt->execute()) {
                    echo "<div class='alert alert-success'>Customer details updated successfully.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error updating customer details.</div>";
                }

                // Check if the password fields are not empty and the new password is different from the old password
                if (!empty($_POST['old_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
                    $old_password = $_POST['old_password'];
                    $new_password = $_POST['new_password'];
                    $confirm_password = $_POST['confirm_password'];

                    // Fetch the customer's current password from the database
                    $password_query = "SELECT password FROM customers WHERE id=:customer_id";
                    $password_stmt = $con->prepare($password_query);
                    $password_stmt->bindParam(':customer_id', $customer_id);
                    $password_stmt->execute();
                    $result = $password_stmt->fetch(PDO::FETCH_ASSOC);
                    $current_password = $result['password'];

                    // Validate the old password and update the new password if valid
                    if (password_verify($old_password, $current_password)) {
                        if ($new_password === $confirm_password) {
                            // Update the new password in the database
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            $update_password_query = "UPDATE customers SET password=:password WHERE id=:customer_id";
                            $update_password_stmt = $con->prepare($update_password_query);
                            $update_password_stmt->bindParam(':password', $hashed_password);
                            $update_password_stmt->bindParam(':customer_id', $customer_id);

                            if ($update_password_stmt->execute()) {
                                echo "<div class='alert alert-success'>Password updated successfully.</div>";
                            } else {
                                echo "<div class='alert alert-danger'>Error updating password.</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>New password and confirm password do not match.</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Old password is incorrect.</div>";
                    }
                }
            }

            // Fetch customer data
            $customer_id = $_GET['id'];
            $customer_query = "SELECT * FROM customers WHERE id=:customer_id";
            $customer_stmt = $con->prepare($customer_query);
            $customer_stmt->bindParam(':customer_id', $customer_id);
            $customer_stmt->execute();
            $customer = $customer_stmt->fetch(PDO::FETCH_ASSOC);
            ?>

            <div class="page-header">
                <h1>Edit Customer Details</h1>
            </div>

            <form action="" method="POST">
                <input type="hidden" name="customer_id" value="<?php echo $customer['id']; ?>">

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $customer['username']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $customer['first_name']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $customer['last_name']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $customer['email']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" value="Male" <?php if ($customer['gender'] === 'Male') echo 'checked'; ?> required>
                        <label class="form-check-label">Male</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" value="Female" <?php if ($customer['gender'] === 'Female') echo 'checked'; ?> required>
                        <label class="form-check-label">Female</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo $customer['date_of_birth']; ?>" required>
                </div>

                <hr>

                <div class="mb-3">
                    <label for="old_password" class="form-label">Old Password</label>
                    <input type="password" class="form-control" id="old_password" name="old_password">
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password">
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                </div>

                <button type="submit" class="btn btn-primary">Update Details</button>
                <a href='customer_read.php' class='btn btn-danger'>Back to Customer List</a>
            </form>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

        <script>
            // Check if the new password is the same as the old password
            const oldPasswordInput = document.getElementById('old_password');
            const newPasswordInput = document.getElementById('new_password');
            const confirmNewPasswordInput = document.getElementById('confirm_password');

            document.querySelector('form').addEventListener('submit', function(event) {
                if (oldPasswordInput.value === newPasswordInput.value) {
                    event.preventDefault();
                    alert("You entered the same password as the old password. Please choose a different password.");
                    newPasswordInput.value = '';
                    confirmNewPasswordInput.value = '';
                    newPasswordInput.focus();
                }
            });
        </script>
    </body>

    </html>