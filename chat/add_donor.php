<?php
// add_donor.php
session_start();
require_once 'config.php';

if (!hasPermission($_SESSION['role'], ['admin', 'staff'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $blood_type = $_POST['blood_type'];
    $weight = $_POST['weight'];
    $code = generateCode('HN');

    $stmt = $pdo->prepare("INSERT INTO donors (code, full_name, phone, email, date_of_birth, gender, address, blood_type, weight) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$code, $full_name, $phone, $email, $date_of_birth, $gender, $address, $blood_type, $weight]);

    header('Location: donors.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Thêm Người hiến</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header><h1>Thêm Người hiến</h1><a href="donors.php">Trở về</a></header>
    <form method="POST">
        <label>Họ tên</label><input type="text" name="full_name" required>
        <label>SĐT</label><input type="text" name="phone" required>
        <label>Email</label><input type="email" name="email">
        <label>Ngày sinh</label><input type="date" name="date_of_birth" required>
        <label>Giới tính</label>
        <select name="gender"><option value="male">Nam</option><option value="female">Nữ</option><option value="other">Khác</option></select>
        <label>Địa chỉ</label><textarea name="address"></textarea>
        <label>Nhóm máu</label>
        <select name="blood_type"><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option><option>O+</option><option>O-</option></select>
        <label>Cân nặng (kg)</label><input type="number" name="weight" step="0.1">
        <button type="submit">Thêm</button>
    </form>
</body>
</html>