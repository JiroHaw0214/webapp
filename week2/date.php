<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

<body>
    <div class="row">
        <div class="col-lg-4">
            <div class="btn-group">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    DAY
                </button>
                <ul class="dropdown-menu dropdown-menu-lg-end">
                    <?php
                    for ($day = 1; $day <= 31; $day++) {
                        echo "<li><a class='dropdown-item' href='#'>$day</a></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="btn-group">
                <button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    MONTH
                </button>
                <ul class="dropdown-menu dropdown-menu-lg-end">
                    <?php
                    for ($month = 1; $month <= 12; $month++) {
                        echo "<li><a class='dropdown-item' href='#'>$month</a></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="btn-group">
                <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    YEAR
                </button>
                <ul class="dropdown-menu dropdown-menu-lg-end">
                    <?php
                    $currentYear = date("Y");
                    for ($year = 1900; $year <= $currentYear; $year++) {
                        echo "<li><a class='dropdown-item' href='#'>$year</a></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>