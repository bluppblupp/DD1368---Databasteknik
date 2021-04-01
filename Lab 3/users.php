<!--Here is some styling HTML you don't need to pay attention to-->
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
</head>
<body>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
    <table class='table table-hover table-responsive table-bordered'>
        <tr>
            <th>Search</th>
        </tr>
        <tr>
            <td>Name</td>
            <td><input type='text' name='fullname' class='form-control' /></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><input type='text' name='email' class='form-control' /></td>
        </tr>
        <tr>
            <td>Amount borrowed</td>
            <td><input type='text' name='numofborrowed' class='form-control' /></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type='submit' name = 'submit' value='Search' class='btn btn-primary' />
                <a href='books.php' class='btn btn-danger'>Back to read products</a>
            </td>
        </tr>
    </table>
</form>

<!--Styling HTML ends and the real work begins below-->

<?php

include 'connection.php'; //Init a connection

if(isset($_POST['submit'])){

    $query = "SELECT * FROM (SELECT users.email, users.numofborrowed, f.* FROM users join (SELECT userid, fullname FROM students full outer join admins using (userid, fullname)) AS f USING(userid)) as fulltable WHERE LOWER(fullname) LIKE LOWER(:fullname) OR  LOWER(numofborrowed::VARCHAR) LIKE LOWER(:numofborrowed::varchar) OR LOWER(email) LIKE LOWER(:email)";

    $stmt = $con->prepare($query);
    if(isset($_POST['fullname'])){
        $fname=htmlspecialchars(strip_tags($_POST['fullname'])); //Reisbn, add or remove columns as you like
        $fullnameSE = "%".$fname."%";
    }
    if(isset($_POST['email'])){
        $mail=htmlspecialchars(strip_tags($_POST['email']));
        $emailSE = "%".$mail."%";
    }
    if(isset($_POST['numofborrowed'])){
        $numborrow=htmlspecialchars(strip_tags($_POST['numofborrowed']));
        $numofborrowedSE = "%".$numborrow."%";
    }


    $stmt->bindParam(':fullname', $fullnameSE);
    $stmt->bindParam(':email', $emailSE);
    $stmt->bindParam(':numofborrowed', $numofborrowedSE);
    $stmt->execute();

    $num = $stmt->rowCount(); //Aquire number of rows

    if($num>0){ //Is there any data/rows?
        echo "<table class='table table-responsive table-fix table-bordered'><thead class='thead-light'>";
        echo "<tr>";
            echo "<th>Name</th>"; // Rename, add or remove columns as you like.
            echo "<th>Email</th>";
            echo "<th>Count of Borrowed Items</th>";
        echo "</tr>";
    while ($rad = $stmt->fetch(PDO::FETCH_ASSOC)){ //Fetches data
        extract($rad);
        echo "<tr>";
            
            // Here is the data added to the table
            echo "<td>{$fullname}</td>"; //Rename, add or remove columns as you like
            echo "<td>{$email}</td>";
            echo "<td>{$numofborrowed}</td>";
            echo "<td>";
            
            //Here are the buttons for update, delete and read.
            echo "<a href='updateUser.php?email={$email}' class='btn btn-primary m-r-1em'>Update</a>";// Replace with ID-variable, to make the buttons work
            echo "</td>";
        echo "</tr>";
    }
    echo "</table>";    
    }
    else{
        echo "<h1> Search gave no result </h1>";
    }
}
?>
</div>
</body>
</html>