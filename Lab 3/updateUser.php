<!--Here is some styling HTML you don't need to pay attention to-->
<!DOCTYPE HTML>
<html>
<head>
    <title>Update User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />  
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Uptade user info</h1>
        </div>
     
<!--Styling HTML ends and the real work begins below-->  
<?php
        
$email=isset($_GET['email']) ? $_GET['email'] : die('ERROR: Record ID not found.'); //The parameter value from the click is aquired
 
include 'connection.php'; //Init the connection
 
try { //Aquire the already existing data
    $query = "SELECT * FROM users WHERE email = :email"; //Put query gathering the data here
    $stmt = $con->prepare( $query );

    $stmt->bindParam(':email', $email); //Binding ID for the query
     
    $stmt->execute();
     
    $row = $stmt->fetch(PDO::FETCH_ASSOC); //Fetching the data
     
    $userid = $row['userid'];
    $isadmin = FALSE;

    try{
        $query = "SELECT * FROM admins WHERE userid = :userid";
        
        $stmt = $con->prepare($query);
        $stmt->bindParam(':userid', $userid);
        $stmt->execute();
        
        $num = $stmt->rowCount();

        if($num > 0){
            $isadmin = TRUE;
        }
    }catch(PDOException $exception){ //In case of error
        die('ERROR: ' . $exception->getMessage());
    }

    if($isadmin){
        $adminquery = "SELECT * FROM admins WHERE userid=:userid";
        $stmt = $con->prepare($adminquery);
        $stmt->bindParam(':userid', $userid);
        $stmt->execute();

        $adminrow = $stmt->fetch(PDO::FETCH_ASSOC); //Fetching the data

        $fullname = $adminrow['fullname'];
        $phonenum = $adminrow['phonenumber'];
        $dateofbirth = $adminrow['dateofbirth'];

    }else{
        $studentquery = "SELECT * FROM students WHERE userid=:userid";
        $stmt = $con->prepare($studentquery);
        $stmt->bindParam(':userid', $userid);
        $stmt->execute();

        $adminrow = $stmt->fetch(PDO::FETCH_ASSOC); //Fetching the data

        $fullname = $adminrow['fullname'];
        $phonenumber = $adminrow['phonenumber'];
        $dateofbirth = $adminrow['dateofbirth'];
    }

}
catch(PDOException $exception){ //In case of error
    die('ERROR: ' . $exception->getMessage());
}
?>
 
<?php
 
 if(isset($_POST['submit'])){ //Has the form been submitted?
      
     try{
        $updatequery = "";

        if($isadmin){
            $updatequery = "UPDATE admins SET fullname=:fullname, dateofbirth=:dateofbirth, phonenumber=:phonenumber WHERE userid =:userid"; //Put your query for updating data here
        }else{
            $updatequery = "UPDATE students SET fullname=:fullname, dateofbirth=:dateofbirth, phonenumber=:phonenumber WHERE userid =:userid"; //Put your query for updating data here
        }
        
        $stmt = $con->prepare($updatequery);


        $fullnameupdate=htmlspecialchars(strip_tags($_POST['fullname'])); //Rename, add or remove columns as you like
        $dateofbirthupdate=htmlspecialchars(strip_tags($_POST['dateofbirth']));
        $phonenumberupdate=htmlspecialchars(strip_tags($_POST['phonenumber']));
        $emailupdate=htmlspecialchars(strip_tags($_POST['email']));
        
         
        $stmt->bindParam(':fullname', $fullnameupdate); //Binding parameters for query
        $stmt->bindParam(':dateofbirth', $dateofbirthupdate); //Binding parameters for query
        $stmt->bindParam(':phonenumber', $phonenumberupdate);
        $stmt->bindParam(':userid', $userid);
          
        // Execute the query
        if($stmt->execute()){//Executes and check if correctly executed
            echo "<div class='alert alert-success'>Record was updated.</div>";
        }else{
            echo "<div class='alert alert-danger'>Unable to update record. Please try again.</div>";
        }

        $emailupdatequery = "UPDATE users SET email = :email WHERE userid = :userid";
        $stmt = $con->prepare($emailupdatequery);
        $stmt->bindParam(':userid', $userid);
        $stmt->bindParam(':email', $emailupdate);
        $stmt->execute();
          
        sleep(1);
        header("Location:updateUser.php?email=$emailupdate");
     }catch(PDOException $exception){ //In case of error
        die('ERROR: ' . $exception->getMessage());
    }

 }
 ?>
 
<!-- The HTML-Form. Rename, add or remove columns for your update here -->
<form action="" method="post">
    <table class='table table-hover table-responsive table-bordered'>
        <tr>
            <td>Name</td>
            <td><input type='text' name='fullname' value="<?php echo htmlspecialchars($fullname, ENT_QUOTES);  ?>" class='form-control' /></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><input type='text' name='email' value="<?php echo htmlspecialchars($email, ENT_QUOTES);  ?>" class='form-control' /></td>
        </tr>
        <tr>
            <td>Date of Birth</td>
            <td><input type='text' name='dateofbirth' value="<?php echo htmlspecialchars($dateofbirth, ENT_QUOTES);  ?>" class='form-control' /></td>
        </tr>
        
        <tr>
            <td>Phone Number</td>
            <td><input type='text' name='phonenumber' value="<?php echo htmlspecialchars($phonenumber, ENT_QUOTES);  ?>" class='form-control' /></td>
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

