<!DOCTYPE html>
<html>

<body>

    <?php
    $num1 = rand(100, 200); 
    $num2 = rand(100, 200); 

    echo "<span style='font-style: italic; color: green;'>".$num1." </span><br>";
    echo "<span style='font-style: italic; color: blue;'>".$num2."</span><br>";
    echo "<span style='font-weight: bold; color: red;'> ".$num1 + $num2."</span><br>";
    echo "<span style='font-weight: bold; font-style: italic;'> ".$num1 * $num2." </span> ";
    ?>

</body>

</html>