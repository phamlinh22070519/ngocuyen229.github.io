<?php
// notifications.php
session_start();
require_once 'config.php';

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();

// Cập nhật is_read nếu xem
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_read'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Thông báo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header><h1>Thông báo</h1><a href="index.php">Trở về</a></header>
    <ul>
        <?php foreach ($notifications as $notif): ?>
            <li>
                <h3><?php echo htmlspecialchars($notif['title']); ?></h3>
                <p><?php echo htmlspecialchars($notif['message']); ?></p>
                <small><?php echo $notif['created_at']; ?></small>
                <?php if (!$notif['is_read']): ?>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $notif['id']; ?>">
                        <button name="mark_read">Đánh dấu đã đọc</button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>