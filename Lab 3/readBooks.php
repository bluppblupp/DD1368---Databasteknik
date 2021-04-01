<!--Here is some styling HTML you don't need to pay attention to-->
<!DOCTYPE HTML>
<html>
<head>
    <title>LMS Books</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Book information</h1>
        </div>
<!--Styling HTML ends and the real work begins below-->

         
<?php

$isbn=isset($_GET['isbn']) ? $_GET['isbn'] : die('ERROR: Record ID not found.'); //The parameter value from the click is aquired
 
include 'connection.php';
 
try {
    $query = "SELECT * FROM books WHERE isbn = :isbn"; // Put query fetching data from table here
    $stmt = $con->prepare( $query );
 
    $stmt->bindParam(':isbn', $isbn); //Bind the ID for the query

    $stmt->execute(); //Execute query
 
    $row = $stmt->fetch(PDO::FETCH_ASSOC); //Fetchs data
 
    $isbn = $row['isbn']; //Store data. Rename, add or remove columns as you like.
    $title = $row['title'];
	$genre = $row['genre'];
	$language = $row['language'];
	$releasedate = $row['releasedate'];
    $serieid = $row['serieid'];
    $edition = $row['edition'];
    $pages = $row['pages'];
}
 

catch(PDOException $exception){ //In case of error
    die('ERROR: ' . $exception->getMessage());
}
?>
 <!-- Here is how we display our data. Rename, add or remove columns as you like-->
<table class='table table-hover table-responsive table-bordered'>
    <tr>
        <td>ISBN</td>
        <td><?php echo htmlspecialchars($isbn, ENT_QUOTES);  ?></td>
    </tr>
	
	<tr>
        <td>Title</td>
        <td><?php echo htmlspecialchars($title, ENT_QUOTES);  ?></td>
    </tr>
	
	<tr>
        <td>Genre</td>
        <td><?php echo htmlspecialchars($genre, ENT_QUOTES);  ?></td>
    </tr>
	
	<tr>
        <td>Language</td>
        <td><?php echo htmlspecialchars($language, ENT_QUOTES);  ?></td>
    </tr>
	
	<tr>
        <td>Release Date</td>
        <td><?php echo htmlspecialchars($releasedate, ENT_QUOTES);  ?></td>
    </tr>
	
    <tr>
        <td>Serie ID</td>
        <td><?php echo htmlspecialchars($serieid, ENT_QUOTES);  ?></td>
    </tr>
	
	<tr>
        <td>Edition</td>
        <td><?php echo htmlspecialchars($edition, ENT_QUOTES);  ?></td>
    </tr>

    <tr>
        <td>Pages</td>
        <td><?php echo htmlspecialchars($pages, ENT_QUOTES);  ?></td>
    </tr>
	
	
    <tr>
        <td></td>
        <td>
            <a href='books.php' class='btn btn-danger'>Back to read products</a>
        </td>
    </tr>
</table> 
    </div> 
</body>
</html>