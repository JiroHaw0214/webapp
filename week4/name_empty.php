<!DOCTYPE html>
<html lang="en">

<head>
    <title>Exercise Week 4 Question 2</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <form method="POST" action="">
            <div class="form-group my-3">
                <label for="firstName">First Name:</label>
                <input type="text" class="form-control" id="firstName" name="firstName">
            </div>
            <div class="form-group my-3">
                <label for="lastName">Last Name:</label>
                <input type="text" class="form-control" id="lastName" name="lastName">
            </div>
            <button type="submit" class="btn btn-primary mb-3" name="submit">Submit</button>
        </form>

        <?php
        if (isset($_POST['submit'])) {
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];

            if (empty($firstName) || empty($lastName)) {
                echo '<p style="color: red;">Please enter your name.</p>';
            } else {
                $formattedFirstName = ucwords(strtolower($firstName));
                $formattedLastName = ucwords(strtolower($lastName));
                echo "Name: " . $formattedLastName . " " . $formattedFirstName;
            }
        }
        ?>
    </div>
</body>

</html>