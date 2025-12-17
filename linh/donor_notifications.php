<?php
// donor_notifications.php
require_once 'config.php';

$user = requireRole($pdo, ['donor']); // hoặc requireLogin nếu muốn tất cả role xem

// lấy danh sách thông báo của user
$stmt = $pdo->prepare("
    SELECT * FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$user['id']]);
$notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// đánh dấu là đã đọc
$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")
    ->execute([$user['id']]);

include 'header.php';
?>
<div class="card">
    <h2>Thông báo của tôi</h2>

    <?php if (!$notifs): ?>
        <p>Hiện bạn chưa có thông báo nào.</p>
    <?php else: ?>
        <table class="table">
            <tr>
                <th>Thời gian</th>
                <th>Loại</th>
                <th>Tiêu đề</th>
                <th>Nội dung</th>
            </tr>
            <?php foreach ($notifs as $n): ?>
                <tr>
                    <td><?= $n['created_at'] ?></td>
                    <td><?= $n['type'] ?></td>
                    <td><?= htmlspecialchars($n['title']) ?></td>
                    <td><?= nl2br(htmlspecialchars($n['message'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
