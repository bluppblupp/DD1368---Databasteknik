<!--Here is some styling HTML you don't need to pay attention to-->
<!DOCTYPE HTML>
<html>
<head>
    <title>Update books</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />  
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Update bookinfo</h1>
        </div>
     
<!--Styling HTML ends and the real work begins below-->  
<?php
        
$isbn=isset($_GET['isbn']) ? $_GET['isbn'] : die('ERROR: Record ID not found.'); //The parameter value from the click is aquired
 
include 'connection.php'; //Init the connection
 
try { //Aquire the already existing data
    $query = "SELECT * FROM books WHERE isbn = :isbn"; //Put query gathering the data here
    $stmt = $con->prepare( $query );

    $stmt->bindParam(':isbn', $isbn); //Binding ID for the query
     
    $stmt->execute();
     
    $row = $stmt->fetch(PDO::FETCH_ASSOC); //Fetching the data
     
    $title = $row['title'];
    $genre = $row['genre'];
    $language = $row['language'];
    $releasedate = $row['releasedate'];
    $serieid = $row ['serieid'];
    $edition = $row ['edition'];
    $pages = $row ['pages'];
}
 
catch(PDOException $exception){ //In case of error
    die('ERROR: ' . $exception->getMessage());
}
?>
 
<?php
 
 if(isset($_POST['submit'])){ //Has the form been submitted?
      
     try{
         
        $query = "UPDATE books SET isbn=:isbnupdate, title=:title, genre=:genre, language = :language, releasedate=:releasedate, serieid=:serieid, edition=:edition, pages=:pages WHERE isbn =:isbn"; //Put your query for updating data here
        $stmt = $con->prepare($query);


        $isbnupdate=htmlspecialchars(strip_tags($_POST['isbn'])); //Rename, add or remove columns as you like
        $title=htmlspecialchars(strip_tags($_POST['title']));
        $genre=htmlspecialchars(strip_tags($_POST['genre']));
        $language = htmlspecialchars(strip_tags($_POST['language']));
        $releasedate = htmlspecialchars(strip_tags($_POST['releasedate']));
        $serieid = htmlspecialchars(strip_tags($_POST['serieid']));
        $edition = htmlspecialchars(strip_tags($_POST['edition']));
        $pages = htmlspecialchars(strip_tags($_POST['pages']));
         
        $stmt->bindParam(':isbnupdate', $isbnupdate); //Binding parameters for query
        $stmt->bindParam(':isbn', $isbn); //Binding parameters for query
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':language', $language);
        $stmt->bindParam(':releasedate', $releasedate);
        $stmt->bindParam(':serieid', $serieid);
        $stmt->bindParam(':edition', $edition);
        $stmt->bindParam(':pages', $pages);
          
        // Execute the query
        if($stmt->execute()){//Executes and check if correctly executed
            echo "<div class='alert alert-success'>Record was updated.</div>";
        }else{
            echo "<div class='alert alert-danger'>Unable to update record. Please try again.</div>";
        }

        $resourcetitle = "UPDATE resources SET title = :title WHERE isbn = :isbnupdate";
        $stmt = $con->prepare($resourcetitle);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':isbnupdate', $isbnupdate);
        $stmt->execute();
          
        sleep(1);
        header("Location:updateBooks.php?isbn=$isbnupdate");
     }catch(PDOException $exception){ //In case of error
        die('ERROR: ' . $exception->getMessage());
    }

 }
 ?>
 
<!-- The HTML-Form. Rename, add or remove columns for your update here -->
<form action="" method="post">
    <table class='table table-hover table-responsive table-bordered'>
        <tr>
            <td>ISBN</td>
            <td><input type='text' name='isbn' value="<?php echo htmlspecialchars($isbn, ENT_QUOTES);  ?>" class='form-control' /></td>
        </tr>
        <tr>
            <td>Title</td>
            <td><input type='text' name='title' value="<?php echo htmlspecialchars($title, ENT_QUOTES);  ?>" class='form-control' /></td>
        </tr>
        <tr>
            <td>Genre</td>
            <td><input type='text' name='genre' value="<?php echo htmlspecialchars($genre, ENT_QUOTES);  ?>" class='form-control' /></td>
        </tr>
        
        <tr>
            <td>Language</td>
            <td><input type='text' name='language' value="<?php echo htmlspecialchars($language, ENT_QUOTES);  ?>" class='form-control' /></td>
        </tr>
        
        <tr>
            <td>Releasedate</td>
            <td><input type='text' name='releasedate' value="<?php echo htmlspecialchars($releasedate, ENT_QUOTES);  ?>" class='form-control' /></td>
        </tr>

        <tr>
            <td>Serie ID</td>
            <td><input type='text' name='serieid' value="<?php echo htmlspecialchars($serieid, ENT_QUOTES);  ?>" class='form-control' /></td>
        </tr>

        <tr>
            <td>Edition</td>
            <td><input type='text' name='edition' value="<?php echo htmlspecialchars($edition, ENT_QUOTES);  ?>" class='form-control' /></td>
        </tr>

        <tr>    
            <td>Pages</td>
            <td><input type='text' name='pages' value="<?php echo htmlspecialchars($pages, ENT_QUOTES);  ?>" class='form-control' /></td>
        </tr>
        
        <tr>
            <td></td>
            <td>
                <input type='submit' name = 'submit' value='Save Changes' class='btn btn-primary' />
                <a href='books.php' class='btn btn-danger'>Back to read products</a>
            </td>
        </tr>
    </table>
</form>
    </div>
</body>
</html>

