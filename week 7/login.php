<?php
session_start();
include "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        $error = "Vui lòng nhập email và mật khẩu!";
    } else {
        $sql = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $sql->bind_param("s", $email);
        $sql->execute();
        $result = $sql->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {
                $_SESSION["userid"] = $user["id"];
                $_SESSION["name"]   = $user["firstname"];

                header("Location: welcome.php");//thay trang add vào đây
                exit();
            } else {
                $error = "Sai mật khẩu!";
            }
        } else {
            $error = "Email không tồn tại!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập</title>
        <link rel="stylesheet" type="text/css" href="style_login.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <h2>Đăng nhập</h2>

    <form method="POST">
        Email: <br>
        <input type="email" name="email"><br><br>

        Password: <br>
        <input type="password" name="password"><br><br>

        <button type="submit">Đăng nhập</button>
    </form>

    <p>Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
</body>
</html>
