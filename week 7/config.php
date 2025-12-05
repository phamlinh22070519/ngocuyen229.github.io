<?php
// Thông tin kết nối MySQL
$host = "localhost";      // hoặc IP server
$username = "root";       // username MySQL
$password = "040903";           // mật khẩu MySQL
$database = "blood_donor"; // tên database

// Kết nối MySQL
$conn = mysqli_connect($host, $username, $password, $database);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Set charset UTF-8 để hỗ trợ tiếng Việt
mysqli_set_charset($conn, "utf8");
?>
