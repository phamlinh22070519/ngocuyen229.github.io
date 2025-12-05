<?php
$link = mysqli_connect("localhost", "root","040903") or die(mysqli_connect_error());
mysqli_select_db($link, "loginreg") or die(mysqli_error($link));
?>