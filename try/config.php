<?php
$host = 'localhost';
$dbname = 'blood_donation'; // Tên cơ sở dữ liệu
$username = 'root'; // Tên người dùng MySQL
$password = '12345678'; // Mật khẩu MySQL (mặc định trên XAMPP là trống)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
