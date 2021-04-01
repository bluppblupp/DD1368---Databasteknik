<?php
$isbn=isset($_GET['isbn']) ? $_GET['isbn'] : die('ERROR: ID not found'); //Aquire the ID

include 'connection.php'; //Init the connection

try { 
    $queries = ["DELETE FROM books WHERE isbn = :isbn"]; // Insert your DELETE query here

    $finished = TRUE;
    foreach ($queries as $query) {
        $stmt = $con->prepare($query);
        $stmt->bindParam(':isbn', $isbn); //Binding the ID for the query
        $finished = $stmt->execute();
    }

    if($finished){
        header('Location: books.php'); //Redirecting back to the main page
    }else{
        die('Could not remove'); //Something went wrong
    }
}

catch(PDOException $exception){
    die('ERROR: ' . $exception->getMessage());
}
?>