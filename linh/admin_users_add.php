<?php
// admin_users_add.php
require_once 'config.php';

// chỉ admin mới tạo được user quản trị
$currentUser = requireRole($pdo, ['admin']);

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $role     = $_POST['role'] ?? '';

    if ($name === '' || $username === '' || $email === '' || $password === '' || $confirm === '') {
        $errors[] = 'Vui lòng nhập đầy đủ thông tin.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ.';
    } elseif ($password !== $confirm) {
        $errors[] = 'Mật khẩu xác nhận không khớp.';
    } elseif (!in_array($role, ['admin','staff'], true)) {
        $errors[] = 'Vai trò không hợp lệ.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Tên đăng nhập hoặc email đã tồn tại.';
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmtIns = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, name, role, is_active)
            VALUES (?, ?, ?, ?, ?, 1)
        ");
        $stmtIns->execute([$username, $email, $hash, $name, $role]);
        $success = true;
        // reset form
        $_POST = [];
    }
}

include 'header.php';
?>
<div class="card">
    <h2>Tạo tài khoản quản trị / nhân viên</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">
            Đã tạo tài khoản <?= htmlspecialchars($role ?? '') ?> mới thành công.
        </div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="alert alert-error">
            <?= implode('<br>', array_map('htmlspecialchars', $errors)); ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Họ và tên</label>
            <input type="text" name="name"
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Tên đăng nhập</label>
            <input type="text" name="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Mật khẩu</label>
            <input type="password" name="password">
        </div>
        <div class="form-group">
            <label>Nhập lại mật khẩu</label>
            <input type="password" name="confirm">
        </div>
        <div class="form-group">
            <label>Vai trò</label>
            <select name="role">
                <option value="">-- Chọn vai trò --</option>
                <option value="admin" <?= (($_POST['role'] ?? '')==='admin')?'selected':'' ?>>
                    Quản trị (admin)
                </option>
                <option value="staff" <?= (($_POST['role'] ?? '')==='staff')?'selected':'' ?>>
                    Nhân viên (staff)
                </option>
            </select>
        </div>
        <button class="btn-primary" type="submit">Tạo tài khoản</button>
    </form>
</div>
<?php include 'footer.php'; ?>
