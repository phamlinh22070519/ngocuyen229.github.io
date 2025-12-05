<?php
include "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST["firstname"]);
    $lastname  = trim($_POST["lastname"]);
    $email     = trim($_POST["email"]);
    $password  = trim($_POST["password"]);

    if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
        $message = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        // Kiểm tra email tồn tại
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email đã được sử dụng!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $sql = $conn->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)");
            $sql->bind_param("ssss", $firstname, $lastname, $email, $hashed);

            if ($sql->execute()) {
                $message = "Đăng ký thành công! <a href='login.php'>Đăng nhập</a>";
            } else {
                $message = "Lỗi hệ thống!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng kí tài khoản</title>
    <link rel="stylesheet" type="text/css" href="style_login.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <h2>Đăng ký tài khoản</h2>

    <form method="POST">
        First Name: <br>
        <input type="text" name="firstname"><br><br>

        Last Name: <br>
        <input type="text" name="lastname"><br><br>

        Email: <br>
        <input type="email" name="email"><br><br>

        Password: <br>
        <input type="password" name="password"><br><br>

        <button type="submit">Đăng ký</button>
    </form>

    <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
</body>
</html>

