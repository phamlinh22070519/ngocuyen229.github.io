<?php
// my_donations.php - Cho donor xem lịch sử
session_start();
require_once 'config.php';

if ($_SESSION['role'] !== 'donor') {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT d.* FROM donations d JOIN donors dn ON d.donor_id = dn.id WHERE dn.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$donations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Lịch sử hiến máu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Table hiển thị donations cá nhân -->
</body>
</html>