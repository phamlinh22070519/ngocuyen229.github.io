<?php
session_start();

/* connect to database check user*/
$con=mysqli_connect('localhost','root');
mysqli_select_db($con,LoginReg);

/* create variables to store data */
$name =$_POST['user'];
$pass =$_POST['password'];

/* select data from DB */
$s="select * from userReg where name='$name'&& password='$pass'";

/* result variable to store data */
$result = mysqli_query($con,$s);

/* check for duplicate names and count records */
$num =mysqli_num_rows($result);
if($num==1){
  /* Storing the username and session */
    $_SESSION['username'] =$name;
    header('location:home.php');
}else{
    header('location:login.php');
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
               <div class="col-md-6 login-left">
                   <h2>Login Here</h2>
                   <form action="validation.php" method="post">
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
                   <button type="submit" class="btn btn-primary">Login</button>
                   </form>
               </div>
                
            </div>


        </div>


    </div>


</body>

</html>
