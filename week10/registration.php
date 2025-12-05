<?php
session_start();

/* connect to database check user*/
$con=mysqli_connect('localhost','root');
mysqli_select_db($con,LoginReg);

/* create variables to store data */
$name =$_POST['user'];
$pass =$_POST['password'];

/* select data from DB */
$s="select * from userReg where name='$name'";

/* result variable to store data */
$result = mysqli_query($con,$s);

/* check for duplicate names and count records */
$num =mysqli_num_rows($result);
if($num==1){
    echo "Username Exists";
}else{
    $reg ="insert into userReg(name,password) values ('$name','$pass')";
    mysqli_query($con,$reg);
    echo "registration successful";
}
?>
<!DOCTYPE html>
<html lang="en">

<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<head>
    <title>User login and Registration</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">


</head>
<body>

    <div class="container">
        <div class="login-box">
            <div class="row">
                <div class="registration">
                    <h2>Registration Here</h2>
                    <form action="registration.php" method="post">
                        <div class="form-group">
                            <label>Username</label>
                            <label>
                                <input type="text" name="user" class="form-control" required>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <label>
                                <input type="password" name="password" class="form-control" required>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </form>

                </div>
            </div>


        </div>


    </div>


</body>

</html>
