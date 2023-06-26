<?php
// used to connect to the database
$host = "localhost";
$db_name = "jirohaw";
$username = "jirohaw";
$password = "C3)k0@GY(bzx!w64";
  
try {
    $con = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // show error
}  
// show error
catch(PDOException $exception){
    echo "Connection error: ".$exception->getMessage();
}
