<!--Here is some styling HTML you don't need to pay attention to-->
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
</head>
<body>

<!--Styling HTML ends and the real work begins below-->
<?php

include 'connection.php'; //Init a connection

if(isset($_POST['submit'])){
 
    try{
        $query = "INSERT INTO books(isbn,title,genre,language,releasedate,serieid,edition, pages) VALUES (:isbn,:title,:genre,:language,:releasedate,:serieid,:edition,:pages)"; // Put query inserting data to table here
 
        $isbn=htmlspecialchars(strip_tags($_POST['isbn'])); //Reisbn, add or remove columns as you like
        $title=htmlspecialchars(strip_tags($_POST['title']));
        $genre=htmlspecialchars(strip_tags($_POST['genre']));
		$language=htmlspecialchars(strip_tags($_POST['language']));
        $releasedate=htmlspecialchars(strip_tags($_POST['releasedate']));
        if(isset($_POST['serie'])){
            $serie=htmlspecialchars(strip_tags($_POST['serie']));
        }
        if(isset($_POST['orderinserie'])){
            $orderinserie=htmlspecialchars(strip_tags($_POST['orderinserie']));
        }
        if(isset($_POST['author'])){
            $author=htmlspecialchars(strip_tags($_POST['author']));
        }
        if(isset($_POST['publisher'])){
            $publisher=htmlspecialchars(strip_tags($_POST['publisher']));
        }
        $edition=htmlspecialchars(strip_tags($_POST['edition']));
        $pages=htmlspecialchars(strip_tags($_POST['pages']));
        
        $duplicate = FALSE;

        try{
            $duplicateCHECK = "SELECT * FROM books WHERE isbn = :isbn";
            
            $stmt = $con->prepare($duplicateCHECK);

            $stmt->bindParam(':isbn', $isbn); //Binding parameters for the query
            
            $stmt->execute();
            $num = $stmt->rowCount();

            if($num > 0){
                $duplicate = TRUE;
            }

        }catch(PDOException $exception){ //In case of error
            die('ERROR: ' . $exception->getMessage());
        }

        if($duplicate == FALSE){
            /* CHECK/INSERT SERIES */
            try {
                $querycheck = "SELECT * FROM series WHERE LOWER(serie) LIKE LOWER(:serie) AND orderinserie = :orderinserie";

                $stmt = $con->prepare($querycheck);

                $serieinsert = $serie."%";
                $stmt->bindParam(':serie', $serieinsert); //Binding parameters for the query
                $stmt->bindParam(':orderinserie', $orderinserie); //Binding parameters for the query
                
                $stmt->execute();
                $num = $stmt->rowCount();

                if($num>0){
                    $row = $stmt->FETCH(PDO::FETCH_ASSOC);
                    $serieid = $row['serieid'];

                }else if($serie == "" || $orderinserie == ""){
                    $serieid = 0;
                    unset($serieid);
                }else{
                    $queryinsert = "INSERT INTO series(serie, orderinserie) VALUES(:serie,:orderinserie) RETURNING serieid";
                    $stmt = $con->prepare($queryinsert);
                    $stmt->bindParam(':serie', $serie); //Binding parameters for the query
                    $stmt->bindParam(':orderinserie', $orderinserie); //Binding parameters for the query
                    $stmt->execute();
                    $row = $stmt->FETCH(PDO::FETCH_ASSOC);
                    $serieid = $row['serieid'];
                }

            }catch(PDOException $exception){ //In case of error
                die('ERROR: ' . $exception->getMessage());
            }
        }

        if($edition == ""){
            unset($edition);
        }
        if($genre == ""){
            unset($genre);
        }
        $stmt = $con->prepare($query); // prepare query for execution

        $stmt->bindParam(':isbn', $isbn); //Binding parameters for the query
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':genre', $genre);
		$stmt->bindParam(':language', $language);
        $stmt->bindParam(':releasedate', $releasedate);
        $stmt->bindParam(':serieid', $serieid);
        $stmt->bindParam(':edition', $edition);
        $stmt->bindParam(':pages', $pages);
        $stmt->bindParam(':serieid', $serieid);
        if($stmt->execute()){ //Executes and check if correctly executed
            echo "<div class='alert alert-success'>Record was saved.</div>";
        }else{

            echo "<div class='alert alert-danger'>Unable to save record.</div>";
        }
        
        
        if($duplicate == FALSE){
            /* CHECK/INSERT AUTHOR */
            try{
                $querycheck = "SELECT * FROM authors WHERE LOWER(name) LIKE LOWER(:author)";

                $stmt = $con->prepare($querycheck);

                $authorselect = $author."%";
                $stmt->bindParam(':author', $authorselect); //Binding parameters for the query
                
                $stmt->execute();
                $num = $stmt->rowCount();

                if($num>0){
                    $row = $stmt->FETCH(PDO::FETCH_ASSOC);
                    $insertbookauthor = "INSERT INTO bookauthor(authorid,isbn) VALUES(:authorid,:isbn)";

                    $stmt = $con->prepare($insertbookauthor);
                    $stmt->bindParam(':authorid', $row['authorid']);
                    $stmt->bindParam(':isbn', $isbn);

                    $stmt->execute();

                }else{
                    $insertauthor = "INSERT INTO authors(name) VALUES(:author) RETURNING authorid";

                    $stmt = $con->prepare($insertauthor);
                    $stmt->bindParam(':author', $author); //Binding parameters for the query
                    $stmt->execute();
                    $row = $stmt->FETCH(PDO::FETCH_ASSOC);
                    $authorid = $row['authorid'];

                    try{
                        $insertbookauthor2 = "INSERT INTO bookauthor(authorid,isbn) VALUES(:authorid,:isbn)";
                        
                        $stmt = $con->prepare($insertbookauthor2);
                        $stmt->bindParam(':authorid', $authorid);
                        $stmt->bindParam(':isbn', $isbn);

                        $stmt->execute();
                    }catch(PDOException $exception){ //In case of error
                        die('ERROR: ' . $exception->getMessage());
                    }
                }

            }catch(PDOException $exception){ //In case of error
                die('ERROR: ' . $exception->getMessage());
            }

            /* CHECK/INSERT Publisher */
            try{
                $querycheck = "SELECT * FROM publisher WHERE LOWER(name) LIKE LOWER(:publisher)";

                $stmt = $con->prepare($querycheck);

                $publisherselect = $publisher."%";
                $stmt->bindParam(':publisher', $publisherselect); //Binding parameters for the query
                
                $stmt->execute();
                $num = $stmt->rowCount();

                if($num>0){
                    $row = $stmt->FETCH(PDO::FETCH_ASSOC);
                    $insertbookpublisher = "INSERT INTO bookpubliser(isbn,publisherid) VALUES(:isbn,:publisherid)";

                    $stmt = $con->prepare($insertbookpublisher);
                    $stmt->bindParam(':isbn', $isbn);
                    $stmt->bindParam(':publisherid', $row['publisherid']);
                    

                    $stmt->execute();

                }else{
                    $insertpublisher = "INSERT INTO publisher(name) VALUES(:publisher) RETURNING publisherid";

                    $stmt = $con->prepare($insertpublisher);
                    $stmt->bindParam(':publisher', $publisher); //Binding parameters for the query
                    $stmt->execute();
                    $row = $stmt->FETCH(PDO::FETCH_ASSOC);

                    $publisherid = $row['publisherid'];
                    try{
                        $insertbookpublisher2 = "INSERT INTO bookpublisher(isbn,publisherid) VALUES(:isbn,:publisherid)";
                    
                        $stmt = $con->prepare($insertbookpublisher2);
                        $stmt->bindParam(':isbn', $isbn);
                        $stmt->bindParam(':publisherid', $publisherid);

                        $stmt->execute();
                    }catch(PDOException $exception){ //In case of error
                        die('ERROR: ' . $exception->getMessage());
                    }
                    
                }

            }catch(PDOException $exception){ //In case of error
                die('ERROR: ' . $exception->getMessage());
            }
        }


        /* CHECK/INSERT RESOURCES */
        try{
            $resourceINSERT = "INSERT INTO resources(title,borrowed,isbn) VALUES(:title,:borrowed,:isbn)";
            $currBook = "SELECT title FROM books WHERE isbn = :isbn";
            $borrowed = 0;

            if($duplicate == TRUE){
                $stmt = $con->prepare($currBook);
                $stmt->bindParam(':isbn', $isbn); //Binding parameters for the query
                $stmt->execute();
                $row = $stmt->FETCH(PDO::FETCH_ASSOC);
                $title = $row['title'];
            }
            try{
                $stmt = $con->prepare($resourceINSERT);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':borrowed', $borrowed);
                $stmt->bindParam(':isbn', $isbn);
                $stmt->execute();
            }catch(PDOException $exception){ //In case of error
                die('ERROR: ' . $exception->getMessage());
            }
            echo "<div class='alert alert-success'>Added to stock.</div>";


        }catch(PDOException $exception){ //In case of error
            die('ERROR: ' . $exception->getMessage());
        }
    }
    catch(PDOException $exception){ //In case of error
        die('ERROR: ' . $exception->getMessage());
    }
}
?>
 
<!-- The HTML-Form. Reisbn, add or remove columns for your insert here -->
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
    <table class='table table-hover table-responsive table-bordered'>
        <tr>
            <td>isbn</td>
            <td><input type='text' name='isbn' class='form-control required' /></td>
        </tr>
        <tr>
            <td>title</td>
            <td><input type='text' name='title' class='form-control required' /></td>
        </tr>
        <tr>
            <td>genre</td>
            <td><input type='text' name='genre' class='form-control' /></td>
        </tr>
		<tr>
            <td>language</td>
            <td><input type='text' name='language' class='form-control required' /></td>
        </tr>
		<tr>
            <td>releasedate</td>
            <td><input type='text' name='releasedate' class='form-control required' /></td>
        </tr>
        <tr>
            <td>publisher</td>
            <td><input type='text' name='publisher' class='form-control required' /></td>
        </tr>
        <tr>
            <td>author</td>
            <td><input type='text' name='author' class='form-control required' /></td>
        </tr>
		<tr>
            <td>serie</td>
            <td><input type='text' name='serie' class='form-control' /></td>
        </tr>
        <tr>
            <td>orderinserie</td>
            <td><input type='number' name='orderinserie' class='form-control' /></td>
        </tr>
        <tr>
            <td>edition</td>
            <td><input type='text' name='edition' class='form-control' /></td>
        </tr>
        <tr>
            <td>pages</td>
            <td><input type='text' name='pages' class='form-control required' /></td>
        </tr>
		
        <tr>
            <td></td>
            <td>
                <input type='submit' name='submit' value='Save' class='btn btn-primary' />
                <a href='books.php' class='btn btn-danger'>Go back</a>
            </td>
        </tr>
    </table>
</form>
</body>
</html>