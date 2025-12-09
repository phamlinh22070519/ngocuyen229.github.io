<?php
// donors.php - Quản lý người hiến (chỉ admin/staff)
session_start();
require_once 'config.php';

if (!hasPermission($_SESSION['role'], ['admin', 'staff'])) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM donors");
$donors = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản lý Người hiến</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header><h1>Quản lý Người hiến</h1><a href="index.php">Trở về</a></header>
    <a href="add_donor.php">Thêm người hiến mới</a>
    <table>
        <thead>
            <tr><th>ID</th><th>Mã</th><th>Tên</th><th>Nhóm máu</th><th>SĐT</th><th>Thao tác</th></tr>
        </thead>
        <tbody>
            <?php foreach ($donors as $donor): ?>
                <tr>
                    <td><?php echo $donor['id']; ?></td>
                    <td><?php echo htmlspecialchars($donor['code']); ?></td>
                    <td><?php echo htmlspecialchars($donor['full_name']); ?></td>
                    <td><?php echo $donor['blood_type']; ?></td>
                    <td><?php echo $donor['phone']; ?></td>
                    <td>
                        <a href="edit_donor.php?id=<?php echo $donor['id']; ?>">Sửa</a>
                        <a href="delete_donor.php?id=<?php echo $donor['id']; ?>" onclick="return confirm('Xóa?');">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>