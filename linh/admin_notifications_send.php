<?php
// admin_notifications_send.php
require_once 'config.php';
$user = requireRole($pdo, ['admin','staff']);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)($_POST['user_id'] ?? 0);
    $type    = $_POST['type'] ?? 'info';
    $title   = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$user_id || $title === '' || $message === '') {
        $errors[] = 'Vui lòng chọn người nhận và nhập tiêu đề, nội dung.';
    } else {
        sendNotification($pdo, $user_id, $title, $message, $type);
        header('Location: admin_notifications_send.php?sent=1');
        exit;
    }
}

$users = $pdo->query("
    SELECT id, name, role
    FROM users
    WHERE is_active = 1
    ORDER BY role, name
")->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>
<div class="card">
    <h2>Gửi thông báo cho người dùng</h2>
    <?php if (isset($_GET['sent'])): ?>
        <div class="alert alert-success">Đã gửi thông báo.</div>
    <?php endif; ?>
    <?php if ($errors): ?>
        <div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars',$errors)); ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Người nhận</label>
            <select name="user_id">
                <option value="">-- Chọn người dùng --</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>">
                        <?= htmlspecialchars($u['name']) ?> (<?= $u['role'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Loại thông báo</label>
            <select name="type">
                <option value="info">Thông tin</option>
                <option value="appointment">Lịch hẹn</option>
                <option value="blood_request">Yêu cầu máu</option>
                <option value="warning">Cảnh báo</option>
                <option value="success">Thành công</option>
            </select>
        </div>
        <div class="form-group"><label>Tiêu đề</label><input type="text" name="title"></div>
        <div class="form-group"><label>Nội dung</label><textarea name="message" rows="4"></textarea></div>
        <button class="btn-primary" type="submit">Gửi thông báo</button>
    </form>
</div>
<?php include 'footer.php'; ?>
