<?php
//setupusers.php
require_once './login.php';

$conn = new mysqli($hostname, $username, $password, $database, $port);
if($conn->error) die($conn->error);

$salt1 = "qm&*";
$salt2 = "pg!@";

// Thiết lập người dùng 1
$forename = 'Bill';
$surname = 'Smith';
$username = 'bsmith';
$password = 'mysecret';
$token = hash('ripemd128', $salt1 . $password . $salt2);
add_user($conn, $forename, $surname, $username, $token);

// Thiết lập người dùng 2
$forename = 'Pauline';
$surname = 'Jones';
$username = 'pjones';
$password = 'acrobat';
$token = hash('ripemd128', $salt1 . $password . $salt2);
add_user($conn, $forename, $surname, $username, $token);

// Định nghĩa hàm add_user
function add_user($conn, $forename, $surname, $username, $token) {
    $query = "INSERT INTO users VALUES ('$forename', '$surname', '$username', '$token')";
    $result = $conn->query($query);
    if(!$result) die($conn->error);
}
?>
<?php
require_once './login.php';

$conn = new mysqli($hostname, $username, $password, $database, $port);
if($conn->error) die("connection error " . $conn->error);

function mysql_fix_string($conn, $string) {
    if(get_magic_quotes_gpc()) $string = stripslashes ($string);
    return $string = $conn->real_escape_string($string);
}

function mysql_entities_fix_string($conn, $string) {
    return htmlentities(mysql_fix_string($conn, $string));
}

if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])){
    header('WWW-Authenticate: Basic realm="Restricted Section"');
    header("HTTP/1.0 401 Unauthorized");
    die("Please enter your username and password");
}

//Continued next page

//Continued from previous page
else
{
    $un_temp = mysql_entities_fix_string($conn, $_SERVER['PHP_AUTH_USER']);
    $pw_temp = mysql_entities_fix_string($conn, $_SERVER['PHP_AUTH_PW']);

    $query = "SELECT * FROM users WHERE username='$un_temp'";
    $result = $conn->query($query);
    if(!$result) {
        die($conn->error);
    } else {
        $row = mysqli_fetch_array($result);
        $salt1 = "qm&*";
        $salt2 = "pg!@";
        $token = hash('ripemd128', $salt1 . $pw_temp . $salt2);

        if($token == $row[3]) {
            echo "$row[0] $row[1]: Hi $row[0] you are logged in as $row[2]";
        } else {
            die("Invalid username/password combination");
        }
    }
}
$conn->close();
?>