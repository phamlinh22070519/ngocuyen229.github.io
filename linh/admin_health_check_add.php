<?php
// admin_health_check_add.php
require_once 'config.php';
$user = requireRole($pdo, ['admin','staff']);

$donor_id = (int)($_GET['donor_id'] ?? 0);

$stmt = $pdo->prepare("SELECT id, full_name, blood_type FROM donors WHERE id = ?");
$stmt->execute([$donor_id]);
$donor = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$donor) {
    die('Không tìm thấy người hiến.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $weight   = $_POST['weight'] !== '' ? (float)$_POST['weight'] : null;
    $bp       = trim($_POST['blood_pressure'] ?? '');
    $hr       = $_POST['heart_rate'] !== '' ? (int)$_POST['heart_rate'] : null;
    $temp     = $_POST['temperature'] !== '' ? (float)$_POST['temperature'] : null;
    $hb       = $_POST['hemoglobin'] !== '' ? (float)$_POST['hemoglobin'] : null;
    $is_normal= isset($_POST['is_normal']) ? 1 : 0;
    $notes    = trim($_POST['notes'] ?? '');

    if ($hb === null || $weight === null) {
        $errors[] = 'Cần nhập tối thiểu cân nặng và hemoglobin.';
    }

    if (!$errors) {
        $stmtIns = $pdo->prepare("
            INSERT INTO health_checks
            (donor_id, weight, blood_pressure, heart_rate, temperature, hemoglobin,
             is_normal, notes, staff_id)
            VALUES (?,?,?,?,?,?,?,?,?)
        ");
        $stmtIns->execute([
            $donor_id,
            $weight,
            $bp ?: null,
            $hr,
            $temp,
            $hb,
            $is_normal,
            $notes ?: null,
            $user['id']
        ]);

        $hc_id = (int)$pdo->lastInsertId();

        if ($is_normal) {
            header('Location: admin_donation_add.php?donor_id='.$donor_id.'&hc_id='.$hc_id);
            exit;
        } else {
            header('Location: admin_donor_view.php?id='.$donor_id);
            exit;
        }
    }
}

include 'header.php';
?>
<div class="card">
    <h2>Kiểm tra sức khỏe: <?= htmlspecialchars($donor['full_name']) ?></h2>
    <?php if ($errors): ?>
        <div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars',$errors)); ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group"><label>Cân nặng (kg)</label><input type="number" step="0.1" name="weight"></div>
        <div class="form-group"><label>Huyết áp</label><input type="text" name="blood_pressure" placeholder="120/80"></div>
        <div class="form-group"><label>Nhịp tim</label><input type="number" name="heart_rate"></div>
        <div class="form-group"><label>Nhiệt độ (°C)</label><input type="number" step="0.1" name="temperature"></div>
        <div class="form-group"><label>Hemoglobin (g/dL)</label><input type="number" step="0.1" name="hemoglobin"></div>
        <div class="form-group">
            <label>Kết luận</label>
            <label class="checkbox-inline">
                <input type="checkbox" name="is_normal" checked> Đạt điều kiện hiến máu
            </label>
        </div>
        <div class="form-group"><label>Ghi chú</label><textarea name="notes" rows="3"></textarea></div>
        <button class="btn-primary" type="submit">Lưu kiểm tra</button>
    </form>
</div>
<?php include 'footer.php'; ?>
