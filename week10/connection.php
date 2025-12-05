<?php
//creating a database connection - $link is a variable use for just connection class
$link=mysqli_connect("localhost","root","040903") or die(mysqli_error($link));
mysqli_select_db($link,"user_account") or die(mysqli_error($link));

