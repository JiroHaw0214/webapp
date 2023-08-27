<?php
require_once 'session_check.php';
checkSession();
?>
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
        <?php include 'includes/navbar.php'; ?>

        <?php
        // Include database connection and fetch customer data
        include 'config/database.php';

        try {
            $query = "SELECT  id, image FROM customers WHERE id = ? LIMIT 0,1";
            $stmt = $con->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $image = $row['image'];
            }
        } catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }

        $errors = array(); // 用于保存错误消息

        if ($_POST) {            // Form is submitted, update customer details
            $customer_id = $_POST['customer_id'];
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $email = $_POST['email'];
            $gender = $_POST['gender'];
            $username = $_POST['username'];
            $date_of_birth = $_POST['date_of_birth'];
            $account_status = $_POST['account_status'];

            if (empty($_POST['first_name'])) {
                $errors[] = "First Name is required.";
            } else if (!ctype_alpha($_POST['first_name'])) {
                $errors[] = "First Name should contain only letters.";
            } else {
                $first_name = $_POST['first_name'];
            }

            if (empty($_POST['last_name'])) {
                $errors[] = "Last Name is required.";
            } else if (!ctype_alpha($_POST['last_name'])) {
                $errors[] = "Last Name should contain only letters.";
            } else {
                $last_name = $_POST['last_name'];
            }

            if (empty($date_of_birth)) {
                $errors[] = "Date of Birth is required.";
            } else {
                $date_of_birth  = $_POST['date_of_birth'];
            }
            echo "Image Path: " . $imagePath . "<br>";

            // Check if any of the password fields is filled out
            if (!empty($_POST['old_password']) || !empty($_POST['new_password']) || !empty($_POST['confirm_password'])) {
                // At least one of the password fields is filled out

                // Check if all three password fields are filled
                if (empty($_POST['old_password']) || empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
                    // Not all three password fields are filled
                    $errors[] = "Please fill out all three password fields.";
                } else {
                    // All three password fields are filled
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

                    if (strlen($new_password) < 8 || !preg_match("#[0-9]+#", $new_password) || !preg_match("#[A-Z]+#", $new_password) || !preg_match("#[a-z]+#", $new_password) || !preg_match("/[!@#$%^&*()\-_=+{};:,<.>]/", $new_password)) {
                        $errors[] = "New password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
                    } elseif ($new_password !== $confirm_password) {
                        $errors[] = "New password and confirm password do not match.";
                    } elseif (!password_verify($old_password, $current_password)) {
                        $errors[] = "Old password is incorrect.";
                    } elseif ($new_password === $old_password) {
                        $errors[] = "New password cannot be the same as the old password.";
                    } else {
                        // Update the new password in the database
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $update_password_query = "UPDATE customers SET password=:password WHERE id=:customer_id";
                        $update_password_stmt = $con->prepare($update_password_query);
                        $update_password_stmt->bindParam(':password', $hashed_password);
                        $update_password_stmt->bindParam(':customer_id', $customer_id);
                        $update_password_stmt->execute();
                    }
                }
            }

            if (isset($_POST['delete_image'])) {
                if (!empty($image) && file_exists("uploads/{$image}")) {
                    unlink("uploads/{$image}");
                    $image = null;
                }
            }

            if (!empty($_FILES["new_image"]["name"])) {
                $new_image = $_FILES["new_image"];
                $upload_dir = "uploads/";
                $original_image_name = basename($new_image["name"]);
                $imageFileType = strtolower(pathinfo($original_image_name, PATHINFO_EXTENSION));
                $max_file_size = 524288; // 512 KB

                // Check if file name already exists
                $new_image_name = sha1_file($new_image["tmp_name"]) . "-" . $original_image_name;
                $target_path = $upload_dir . $new_image_name;

                if (file_exists($target_path)) {
                    $errors[] = "Image already exists. Try to change the file name.";
                } else {
                    // Check file type and size
                    $allowed_extensions = array("jpg", "jpeg", "png", "gif");
                    if (!in_array($imageFileType, $allowed_extensions)) {
                        $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
                    } elseif ($new_image["size"] > $max_file_size) {
                        $errors[] = "Image must be less than 512 KB in size.";
                    } else {
                        // Check if the image is square
                        list($width, $height) = getimagesize($new_image["tmp_name"]);
                        if ($width != $height) {
                            $errors[] = "Only square image allowed.";
                        } else {
                            // Move the uploaded image to the target directory
                            if (move_uploaded_file($new_image["tmp_name"], $target_path)) {
                                $image = $new_image_name;
                            } else {
                                $errors[] = "Failed to upload the new image.";
                            }
                        }
                    }
                }
            }

            // If there are no errors, update customer details
            if (empty($errors)) {
                $update_query = "UPDATE customers SET account_status=:account_status, first_name=:first_name, last_name=:last_name, email=:email, gender=:gender, username=:username, image=:image, date_of_birth=:date_of_birth WHERE id=:customer_id";
                $update_stmt = $con->prepare($update_query);
                $update_stmt->bindParam(':first_name', $first_name);
                $update_stmt->bindParam(':last_name', $last_name);
                $update_stmt->bindParam(':email', $email);
                $update_stmt->bindParam(':gender', $gender);
                $update_stmt->bindParam(':username', $username);
                $update_stmt->bindParam(':date_of_birth', $date_of_birth);
                $update_stmt->bindParam(':image', $image);
                $update_stmt->bindParam(':account_status', $account_status);
                $update_stmt->bindParam(':customer_id', $customer_id);

                if ($update_stmt->execute()) {
                    echo "<div class='alert alert-success mt-3'>Customer details updated successfully.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error updating customer details.</div>";
                }
            } else {
                // Display error messages
                foreach ($errors as $error) {
                    echo "<div class='alert alert-danger mt-3'>$error</div>";
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

        <div class="p-3">
            <h1>Edit Customer Details</h1>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="customer_id" value="<?php echo $customer['id']; ?>">

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" readonly id="username" name="username" value="<?php echo $customer['username']; ?>">
            </div>
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $customer['first_name']; ?>">
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $customer['last_name']; ?>">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $customer['email']; ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Gender</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" value="Male" <?php if ($customer['gender'] === 'Male') echo 'checked'; ?>>
                    <label class="form-check-label">Male</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" value="Female" <?php if ($customer['gender'] === 'Female') echo 'checked'; ?>>
                    <label class="form-check-label">Female</label>
                </div>
            </div>
            <div class="mb-3">
                <label for="date_of_birth" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo $customer['date_of_birth']; ?>" max="<?php echo date('Y-m-d'); ?>">
            </div>
            <!-- Existing Image -->
            <!-- Existing Image -->
            <div class="mb-3">
                <label for="current_image" class="form-label"></label>
                <?php
                if (!empty($image)) {
                    echo "<img src='uploads/{$image}' class='customer-image' alt='Customer Image'>";
                    echo "<br><input type='checkbox' name='delete_image' value='1'> Delete Current Image";
                } else {
                    echo '<img src="img/customer.jpg" height="100px" alt="Default Customer Image">';
                }
                ?>
            </div>


            <!-- Upload New Image -->
            <div class="mb-3">
                <label for="image" class="form-label">Upload New Image</label>
                <td><input type='file' name='new_image' accept='image/*' class='form-control' /></td>
            </div>

            <div class="mb-3">
                <label class="form-label">User Status</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="account_status" value="Active" <?php if ($customer['account_status'] === 'Active') echo 'checked'; ?> required>
                    <label class="form-check-label">Active</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="account_status" value="Inactive" <?php if ($customer['account_status'] === 'Inactive') echo 'checked'; ?> required>
                    <label class="form-check-label">Inactive</label>
                </div>
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
</body>

</html>