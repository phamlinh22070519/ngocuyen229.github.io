<?php
if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
    echo 'Welcome User: ' . $_SERVER['PHP_AUTH_USER'] .
         ' Password: ' . $_SERVER['PHP_AUTH_PW'];
} else {
    //Note also that header must be sent before any html
    //So you shouldn't have any HTML before the <?php tag for this file
    header('WWW-Authenticate: Basic realm="Restricted Section"');
    header("HTTP/1.0 401 Unauthorized");
    die("Please enter your username and password");
}