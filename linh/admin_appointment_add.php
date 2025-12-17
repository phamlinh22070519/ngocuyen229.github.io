<?php
// admin_appointment_add.php
require_once 'config.php';
$user = requireRole($pdo, ['admin','staff']);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donor_id = (int)($_POST['donor_id'] ?? 0);
    $datetime = $_POST['appointment_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $notes    = trim($_POST['notes'] ?? '');

    if (!$donor_id || $datetime === '' || $location === '') {
        $errors[] = 'Vui lòng chọn người hiến, thời gian và địa điểm.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO appointments
            (donor_id, appointment_date, location, notes, created_by)
            VALUES (?,?,?,?,?)
        ");
        $stmt->execute([
            $donor_id,
            $datetime,
            $location,
            $notes ?: null,
            $user['id']
        ]);
        header('Location: admin_appointments_list.php');
        exit;
    }
}

$donors = $pdo->query("
    SELECT id, full_name, blood_type
    FROM donors
    ORDER BY full_name
")->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>
<div class="card">
    <h2>Tạo lịch hẹn hiến máu</h2>
    <?php if ($errors): ?>
        <div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars',$errors)); ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Người hiến</label>
            <select name="donor_id">
                <option value="">-- Chọn --</option>
                <?php foreach ($donors as $d): ?>
                    <option value="<?= $d['id'] ?>">
                        <?= htmlspecialchars($d['full_name']) ?> (<?= $d['blood_type'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Thời gian</label><input type="datetime-local" name="appointment_date"></div>
        <div class="form-group"><label>Địa điểm</label><input type="text" name="location"></div>
        <div class="form-group"><label>Ghi chú</label><textarea name="notes" rows="3"></textarea></div>
        <button class="btn-primary" type="submit">Lưu lịch hẹn</button>
    </form>
</div>
<?php include 'footer.php'; ?>
