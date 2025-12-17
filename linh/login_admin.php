<?php
// login_admin.php
require_once 'config.php';

$current = getCurrentUser($pdo);
if ($current && in_array($current['role'], ['admin','staff'], true)) {
    header('Location: admin_dashboard.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = !empty($_POST['remember']);

    if ($username === '' || $password === '') {
        $errors[] = 'Vui lòng nhập tên đăng nhập và mật khẩu.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // chỉ chấp nhận role = admin hoặc staff
        if (
            !$user ||
            !in_array($user['role'], ['admin','staff'], true) ||
            !password_verify($password, $user['password_hash'])
        ) {
            $errors[] = 'Tài khoản quản trị hoặc mật khẩu không đúng.';
        } else {
            loginUser($pdo, $user, $remember);
            header('Location: admin_dashboard.php');
            exit;
        }
    }
}

include 'header.php';
?>
<div class="card">
    <h2>Đăng nhập quản trị</h2>

    <?php if ($errors): ?>
        <div class="alert alert-error">
            <?= implode('<br>', array_map('htmlspecialchars', $errors)); ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Tên đăng nhập</label>
            <input type="text" name="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Mật khẩu</label>
            <input type="password" name="password">
        </div>
        <div class="form-group checkbox-inline">
            <input type="checkbox" name="remember" id="remember"
                <?= !empty($_POST['remember'])?'checked':'' ?>>
            <label for="remember">Ghi nhớ đăng nhập (cookie)</label>
        </div>
        <button class="btn-primary" type="submit">Đăng nhập</button>
    </form>
</div>
<?php include 'footer.php'; ?>
