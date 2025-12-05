<?php
session_start();

// Xóa toàn bộ session
session_unset();
session_destroy();

// Chuyển về trang login
header("Location: login.php");
exit();
?>
