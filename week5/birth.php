<!DOCTYPE html>
<html lang="en">

<head>
  <title>Exercise Week 5 Question 1</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
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
      <div class="row">
        <div class="col">
          <label for="day">Day</label>
          <select class="form-select" aria-label="Default select example" name="day">
            <?php
            for ($i = 1; $i <= 31; $i++) {
              echo "<option value=\"$i\">$i</option>";
            }
            ?>
          </select>
        </div>
        <div class="col">
          <label for="day">Month</label>
          <select class="form-select" name="month" required>
            <?php
            $months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
            foreach ($months as $month) {
              echo "<option value=\"$month\">$month</option>";
            }
            ?>
          </select>
        </div>
        <div class="col">
          <label for="day">Year</label>
          <select class="form-select" aria-label="Default select example" name="year">
            <?php
            $currentYear = date('Y');
            for ($i = 1900; $i <= $currentYear; $i++) {
              echo "<option value=\"$i\">$i</option>";
            }
            ?>
          </select>
        </div>
      </div>
      <button type="submit" class="btn btn-primary my-3" name="submit">Submit</button>
    </form>

    <?php
    if (isset($_POST['submit'])) {
      $firstName = $_POST['firstName'];
      $lastName = $_POST['lastName'];
      $formattedFirstName = ucwords(strtolower($firstName));
      $formattedLastName = ucwords(strtolower($lastName));
      $day = $_POST['day'];
      $months = $_POST['month'];
      $year = $_POST['year']; //并将它们存储在相应的变量中。
      if (empty($firstName) || empty($lastName)) {
        echo '<span class="text-danger">' . "Please enter your name." . '</span>';
      } else {
        $formattedFirstName = ucwords(strtolower($firstName));
        $formattedLastName = ucwords(strtolower($lastName));
        echo "Name: " . $formattedLastName . " " . $formattedFirstName;
      }
      echo "<br>Date of Birth: " . $day . " " . $months . " " . $year;
      $currentYear = date('Y');
      $age = $currentYear - $year;
      $birthdate = strtotime("$year-$months-$day");
      $age = date('Y') - date('Y', $birthdate);
      if (date('md') < date('md', $birthdate)) {
        $age--; //计算用户的出生日期，并计算用户的年龄
      }
      if ($age >= 18) {
        echo "<br>Welcome you are $age years old.";
      } else {
        echo "<br>Sorry, you are younger than 18.";
      }
    }
    ?>
  </div>
</body>

</html>