<?php
// admin_donation_add.php
require_once 'config.php';
$user = requireRole($pdo, ['admin','staff']);

$donor_id = (int)($_GET['donor_id'] ?? 0);
$hc_id    = (int)($_GET['hc_id'] ?? 0);

$stmt = $pdo->prepare("SELECT id, full_name, blood_type FROM donors WHERE id = ?");
$stmt->execute([$donor_id]);
$donor = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$donor) {
    die('Không tìm thấy người hiến.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $volume = (int)($_POST['volume_ml'] ?? 350);
    $status = $_POST['status'] ?? 'completed';
    $notes  = trim($_POST['notes'] ?? '');

    if ($volume <= 0) {
        $errors[] = 'Thể tích không hợp lệ.';
    }

    if (!$errors) {
        $donation_code = 'BM-' . date('Y') . '-' . substr(time(), -6);

        $stmtIns = $pdo->prepare("
            INSERT INTO donations
            (donor_id, health_check_id, donation_code, volume_ml, blood_type_collected,
             notes, staff_id, status)
            VALUES (?,?,?,?,?,?,?,?)
        ");
        $stmtIns->execute([
            $donor_id,
            $hc_id,
            $donation_code,
            $volume,
            $donor['blood_type'],
            $notes ?: null,
            $user['id'],
            $status
        ]);

        $pdo->prepare("
            UPDATE donors
            SET total_donations = total_donations + 1,
                last_donation_date = NOW()
            WHERE id = ?
        ")->execute([$donor_id]);

        header('Location: admin_donor_view.php?id='.$donor_id);
        exit;
    }
}

include 'header.php';
?>
<div class="card">
    <h2>Ghi nhận lần hiến: <?= htmlspecialchars($donor['full_name']) ?></h2>
    <?php if ($errors): ?>
        <div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars',$errors)); ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Thể tích (ml)</label>
            <select name="volume_ml">
                <option value="250">250</option>
                <option value="350" selected>350</option>
                <option value="450">450</option>
            </select>
        </div>
        <div class="form-group">
            <label>Trạng thái</label>
            <select name="status">
                <option value="completed">Hoàn thành</option>
                <option value="deferred">Hoãn</option>
                <option value="rejected">Từ chối</option>
            </select>
        </div>
        <div class="form-group"><label>Ghi chú</label><textarea name="notes" rows="3"></textarea></div>
        <button class="btn-primary" type="submit">Lưu lần hiến</button>
    </form>
</div>
<?php include 'footer.php'; ?>
