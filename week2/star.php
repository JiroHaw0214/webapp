<!DOCTYPE html>
<html>

<body>

    <?php
    for ($row = 10; $row >= 1; $row--) {
        for ($col = 1; $col <= $row; $col++) {
            echo "*";
        }
        echo "<br>";
    }
    ?>

</body>

</html>