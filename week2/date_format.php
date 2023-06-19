<!DOCTYPE html>
<html>

<body>

    <?php
    date_default_timezone_set('Asia/Kuala_Lumpur'); 

    $today = date('M d, Y (D)');
    $time = date('H:i:s');

    echo $today . "<br> \t\t" . $time;
    ?>

</body>

</html>