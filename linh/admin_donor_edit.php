<?php
// admin_donor_edit.php
require_once 'config.php';
$user = requireRole($pdo, ['admin','staff']);

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM donors WHERE id = ?");
$stmt->execute([$id]);
$donor = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$donor) {
    die('Không tìm thấy người hiến.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $dob       = $_POST['date_of_birth'] ?? '';
    $gender    = $_POST['gender'] ?? '';
    $address   = trim($_POST['address'] ?? '');
    $blood     = $_POST['blood_type'] ?? '';
    $weight    = $_POST['weight'] !== '' ? (float)$_POST['weight'] : null;

    if ($full_name === '' || $phone === '' || $dob === '' || $gender === '' || $blood === '') {
        $errors[] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
    }

    if (!$errors) {
        $stmtUp = $pdo->prepare("
            UPDATE donors
            SET full_name = ?, phone = ?, email = ?, date_of_birth = ?,
                gender = ?, address = ?, blood_type = ?, weight = ?
            WHERE id = ?
        ");
        $stmtUp->execute([
            $full_name,
            $phone,
            $email ?: null,
            $dob,
            $gender,
            $address ?: null,
            $blood,
            $weight,
            $id
        ]);
        header('Location: admin_donor_view.php?id=' . $id);
        exit;
    }
}

include 'header.php';
?>
<div class="card">
    <h2>Sửa thông tin người hiến</h2>
    <?php if ($errors): ?>
        <div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars',$errors)); ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group"><label>Họ tên *</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($donor['full_name']) ?>"></div>
        <div class="form-group"><label>SĐT *</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($donor['phone']) ?>"></div>
        <div class="form-group"><label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($donor['email']) ?>"></div>
        <div class="form-group"><label>Ngày sinh *</label>
            <input type="date" name="date_of_birth" value="<?= $donor['date_of_birth'] ?>"></div>
        <div class="form-group">
            <label>Giới tính *</label>
            <select name="gender">
                <option value="male"   <?= $donor['gender']==='male'?'selected':'' ?>>Nam</option>
                <option value="female" <?= $donor['gender']==='female'?'selected':'' ?>>Nữ</option>
                <option value="other"  <?= $donor['gender']==='other'?'selected':'' ?>>Khác</option>
            </select>
        </div>
        <div class="form-group"><label>Địa chỉ</label>
            <textarea name="address" rows="2"><?= htmlspecialchars($donor['address']) ?></textarea></div>
        <div class="form-group">
            <label>Nhóm máu *</label>
            <select name="blood_type">
                <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt): ?>
                    <option value="<?= $bt ?>" <?= $donor['blood_type']===$bt?'selected':'' ?>>
                        <?= $bt ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Cân nặng (kg)</label>
            <input type="number" step="0.1" name="weight" value="<?= $donor['weight'] ?>"></div>
        <button class="btn-primary" type="submit">Lưu</button>
    </form>
</div>
<?php include 'footer.php'; ?>
