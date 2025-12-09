<?php
// Tương tự cho các trang khác: appointments.php, add_appointment.php, etc.
// Ví dụ: appointments.php

session_start();
require_once 'config.php';

if (!hasPermission($_SESSION['role'], ['admin', 'staff'])) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->query("SELECT a.*, d.full_name FROM appointments a JOIN donors d ON a.donor_id = d.id");
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản lý Lịch hẹn</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Table hiển thị, nút thêm/sửa/xóa tương tự donors -->
</body>
</html>