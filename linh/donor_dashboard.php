<?php
// donor_dashboard.php
require_once 'config.php';

// chỉ cho phép role = donor
$user = requireRole($pdo, ['donor']);

// tìm hồ sơ donor tương ứng với tài khoản đang đăng nhập
$stmt = $pdo->prepare("SELECT * FROM donors WHERE user_id = ? LIMIT 1");
$stmt->execute([$user['id']]);
$donor = $stmt->fetch(PDO::FETCH_ASSOC);

$donations = [];
if ($donor) {
    $stmt2 = $pdo->prepare("
        SELECT * FROM donations
        WHERE donor_id = ?
        ORDER BY donation_date DESC
    ");
    $stmt2->execute([$donor['id']]);
    $donations = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}

include 'header.php';
?>
<div class="card">
    <h2>Thông tin người hiến máu</h2>

    <?php if (!$donor): ?>
        <p>Tài khoản của bạn chưa được gắn với hồ sơ người hiến máu.
           Vui lòng liên hệ nhân viên để được hỗ trợ.</p>
    <?php else: ?>
        <p><strong>Mã hiến máu:</strong> <?= htmlspecialchars($donor['code']) ?></p>
        <p><strong>Họ tên:</strong> <?= htmlspecialchars($donor['full_name']) ?></p>
        <p><strong>Nhóm máu:</strong> <?= $donor['blood_type'] ?></p>
        <p><strong>Ngày sinh:</strong> <?= $donor['date_of_birth'] ?></p>
        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($donor['phone']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($donor['email']) ?></p>
        <p><strong>Địa chỉ:</strong>
            <?= nl2br(htmlspecialchars($donor['address'] ?? '')) ?></p>
        <p><strong>Số lần hiến:</strong> <?= (int)$donor['total_donations'] ?></p>
        <p><strong>Lần gần nhất:</strong> <?= $donor['last_donation_date'] ?: 'Chưa có' ?></p>
        <p style="margin-top:8px;color:#b00020;">
            Bạn chỉ có quyền xem thông tin, mọi thay đổi dữ liệu do quản trị viên thực hiện.
        </p>
    <?php endif; ?>
</div>

<?php if ($donor): ?>
<div class="card">
    <h2>Lịch sử hiến máu</h2>

    <?php if (!$donations): ?>
        <p>Bạn chưa có lần hiến máu nào trong hệ thống.</p>
    <?php else: ?>
        <table class="table">
            <tr>
                <th>Mã hiến</th>
                <th>Ngày hiến</th>
                <th>Thể tích</th>
                <th>Nhóm máu</th>
                <th>Trạng thái</th>
                <th>Ghi chú</th>
            </tr>
            <?php foreach ($donations as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['donation_code']) ?></td>
                    <td><?= $d['donation_date'] ?></td>
                    <td><?= $d['volume_ml'] ?> ml</td>
                    <td><?= $d['blood_type_collected'] ?></td>
                    <td><?= $d['status'] ?></td>
                    <td><?= nl2br(htmlspecialchars($d['notes'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>
