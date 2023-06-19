<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head>

<body>

    <?php
    $num1 = rand(1, 50);
    $num2 = rand(1, 50);

    echo "<div class='col'>";
    if ($num1 > $num2) {
        echo "<span class='text-primary fw-bold display-4'>$num1</span>";
        echo $num2;
    } else if ($num2 > $num1) {
        echo $num1;
        echo "<span class='text-secondary fw-bold display-4'>$num2</span>";
    } else {
        echo "Suprise!";
    }
    echo "</div>";
    ?>
</body>

</html>