<?php
session_start();
header("location:login.php");
/* connect to database check user*/
$con=mysqli_connect('localhost','root','040903');
mysqli_select_db($con,LoginRegBlood);

/* create variables to store data */
$name =$_POST['user'];
$phone =$_POST['phone'];
$email =$_POST['email'];
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
    $reg ="insert into userReg(name,phone,email,password) values ('$name','$phone','$email','$pass')";
    mysqli_query($con,$reg);
    echo "registration successful";
}
