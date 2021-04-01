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
        $title=htmlspecialchars(strip_tags($_POST['title'])); //Reisbn, add or remove columns as you like
        $email=htmlspecialchars(strip_tags($_POST['email']));

        $allowed = FALSE;
        $userexist = FALSE;
        $resourceexist = FALSE;
        $userid;
        $resourceid;

        /* CHECK USER */
        try{
            $query = "SELECT * FROM users WHERE email = :email AND (numofborrowed < 6)";
            
            $stmt = $con->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $num = $stmt->rowCount();

            if($num > 0){
                $userexist = TRUE;
                $row = $stmt->FETCH(PDO::FETCH_ASSOC);
                $userid = $row['userid'];
            }

            if(!$userexist){
                echo "<div class='alert alert-success'>You are not a user or are already borrowing 5 books.</div>";   
            }
        }catch(PDOException $exception){ //In case of error
            die('ERROR: ' . $exception->getMessage());
        }

        /* CHECK RESOURCES */
        try {
            $query = "SELECT * FROM resources WHERE LOWER(title) LIKE LOWER(:title) AND borrowed = :borrowed LIMIT 1";
            $borrowed = 0;

            $stmt = $con->prepare($query);
            $titlelocate = $title."%";
            $stmt->bindParam(':title', $titlelocate);
            $stmt->bindParam(':borrowed', $borrowed);
            $stmt->execute();

            $num = $stmt->rowCount();

            if($num > 0){
                $resourceexist = TRUE;
                $row = $stmt->FETCH(PDO::FETCH_ASSOC);
                $resourceid = $row['resourceid'];
            }

            if(!$resourceexist){
                echo "<div class='alert alert-success'>You are not a user.</div>";   
            }
        }catch(PDOException $exception){ //In case of error
            die('ERROR: ' . $exception->getMessage());
        }

        if($userexist && $resourceexist){
            $query = "INSERT INTO borrowlist(resourceid,userid,borrowdate,expiredate,returndate,dayspassed) VALUES(:resourceid,:userid,NOW()::date,NOW()::date+7,:returndate,:dayspassed)";
            $dayspassed = 0;
            $returndate = 0;
            unset($returndate);

            $stmt = $con->prepare($query);
            $stmt->bindParam(':resourceid', $resourceid);
            $stmt->bindParam(':userid', $userid);
            $stmt->bindParam(':returndate', $returndate);
            $stmt->bindParam(':dayspassed', $dayspassed);

            $stmt->execute();

            $updateborrowed = "UPDATE resources SET borrowed = '1'::integer WHERE resourceid = :resourceid";
            $stmt = $con->prepare($updateborrowed);
            $stmt->bindParam(':resourceid', $resourceid);

            $stmt->execute();

            $updatenumborrowed = "UPDATE users SET numofborrowed = numofborrowed+ '1'::integer WHERE userid = :userid";
            $stmt = $con->prepare($updatenumborrowed);
            $stmt->bindParam(':userid', $userid);

            $stmt->execute();

            $allowed = TRUE;
        }



        if($allowed){
            echo "<div class='alert alert-success'>Borrow suceeded.</div>";
        }else{
            echo "<div class='alert alert-success'>Borrow failed.</div>";
        }
    }catch(PDOException $exception){ //In case of error
        die('ERROR: ' . $exception->getMessage());
    }
}
?>
 
<!-- The HTML-Form. Reisbn, add or remove columns for your insert here -->
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
    <table class='table table-hover table-responsive table-bordered'>
        <tr>
            <td>title</td>
            <td><input type='text' name='title' class='form-control required' /></td>
        </tr>
        <tr>
            <td>email</td>
            <td><input type='text' name='email' class='form-control required' /></td>
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