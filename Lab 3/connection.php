<?php
//Here is the information the needed for connection
$host = "nestor2.csc.kth.se"; //Don't change
$db_name = "mahdilab2"; //Your username. If you want to look at a simple example for the main page, connect to "aliceer". Delete and create not available for this database.
$username = "wandi"; //Your username
$password = "NMyNvuzDGevr"; //The password you retrieved from your folder in Putty

try {
    $con = new PDO("pgsql:host={$host};dbname={$db_name}", $username, $password);
}
//In case of error
catch(PDOException $exception){
    echo "Connection error: " . $exception->getMessage();
}
?>