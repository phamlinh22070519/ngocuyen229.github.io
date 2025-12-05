<?php
//removeCookie.php
setcookie("logged_user", "", time() - 60 * 60 * 24 * 30);
echo 'Remove cookie successfully<br/>';
echo 'Click <a href="index.php">here</a> to go back';
?>
