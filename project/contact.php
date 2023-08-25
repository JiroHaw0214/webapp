<?php
require_once 'session_check.php';
checkSession();
?>
<!DOCTYPE HTML>
<html>

<head>
    <title>Contact Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        .error-message {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        include 'includes/navbar.php';
        ?>

        <div class="p-3">
            <h1>Contact Form</h1>
        </div>
        <?php
        $name = $email = $message = '';

        if ($_POST) {
            // Check if form data is submitted via POST method and include the necessary database connection file
            include 'config/database.php';

            $errors = array(); // Array to store error messages

            // Check each field for empty values and store error messages in the $errors array
            if (empty($_POST['name'])) {
                $errors[] = "Name is required.";
            } elseif (!preg_match("/^[a-zA-Z]+$/", $_POST['name'])) {
                $errors[] = "Name must contain only English letters.";
            } else {
                $name = $_POST['name'];
            }
            if (empty($_POST['email'])) {
                $errors[] = "Email is required.";
            } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format.";
            } else {
                $email = $_POST['email'];
            }
            if (empty($_POST['message'])) {
                $errors[] = "Message is required.";
            } else {
                $message = $_POST['message'];
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
                    // Insert form data into the database
                    $query = "INSERT INTO contacts SET name=:name, email=:email, message=:message, created=:created";
                    // Bind the parameters
                    $stmt = $con->prepare($query);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':message', $message);
                    $created = date('Y-m-d H:i:s');
                    $stmt->bindParam(':created', $created);

                    // Execute the query
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Message sent successfully.</div>";
                        $name = $message = $email = '';
                    } else {
                        echo "<div class='alert alert-danger'>Unable to send message.</div>";
                    }
                } catch (PDOException $exception) {
                    die('ERROR: ' . $exception->getMessage());
                }
            }
        }
        ?>

        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" id="name" value="<?php echo $name; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" value="<?php echo $email; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea name="message" class="form-control" id="message" rows="5"><?php echo $message; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>