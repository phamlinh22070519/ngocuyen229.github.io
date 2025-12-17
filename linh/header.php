<?php
// header.php
require_once __DIR__ . '/config.php';
$currentUser = getCurrentUser($pdo);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Blood Donation Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="navbar">
    <div class="logo">
        <a href="index.php">Blood Donation</a>
    </div>
    <nav>
        <a href="index.php">Trang chủ</a>

        <?php if ($currentUser && in_array($currentUser['role'], ['admin','staff'], true)): ?>
            <a href="admin_dashboard.php">Bảng điều khiển</a>
            <a href="admin_donors_list.php">Người hiến</a>
            <a href="admin_appointments_list.php">Lịch hẹn</a>
            <a href="admin_inventory_list.php">Kho máu</a>
            <a href="admin_notifications_send.php">Gửi thông báo</a>
            <?php if ($currentUser['role'] === 'admin'): ?>
                <a href="admin_users_add.php">Tạo tài khoản quản trị</a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($currentUser && $currentUser['role'] === 'donor'): ?>
            <a href="donor_dashboard.php">Thông tin của tôi</a>
            <a href="donor_appointments.php">Lịch hẹn</a>
            <a href="donor_notifications.php">Thông báo</a>
        <?php endif; ?>

        <?php if ($currentUser): ?>
            <span class="user-label">
                <?= htmlspecialchars($currentUser['name']) ?> (<?= $currentUser['role'] ?>)
            </span>
            <a href="logout.php">Đăng xuất</a>
        <?php else: ?>
            <a href="login_donor.php">Đăng nhập người hiến</a>
            <a href="register_donor.php" class="btn-primary">Đăng ký hiến máu</a>
            <!-- link admin nhỏ, chỉ người biết mới dùng -->
            <a href="login_admin.php" class="admin-login-link">Đăng nhập quản trị</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
