<?php
// logout.php
require_once 'config.php';

$_SESSION = [];
session_destroy();
setcookie('remember_token', '', time() - 3600, "/");

header('Location: index.php');
exit;
