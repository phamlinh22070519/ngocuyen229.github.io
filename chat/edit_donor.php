<?php
// edit_donor.php
session_start();
require_once 'config.php';

if (!hasPermission($_SESSION['role'], ['admin', 'staff'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM donors WHERE id = ?");
$stmt->execute([$id]);
$donor = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Cập nhật dữ liệu tương tự add
    $full_name = $_POST['full_name'];
    // ... (tương tự các trường khác)

    $stmt = $pdo->prepare("UPDATE donors SET full_name = ?, ... WHERE id = ?");
    $stmt->execute([$full_name, ... , $id]);
    header('Location: donors.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Sửa Người hiến</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Form tương tự add_donor.php, nhưng value mặc định từ $donor -->
</body>
</html>