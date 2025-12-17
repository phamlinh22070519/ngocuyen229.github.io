<?php
// index.php
require_once 'config.php';
include 'header.php';
?>
<div class="card">
    <h2>Hệ thống quản lý hiến máu</h2>
    <p>Hệ thống hỗ trợ quản lý người hiến máu, kiểm tra sức khỏe, lần hiến, kho máu, lịch hẹn và thông báo cho người hiến.</p>
    <p>Vai trò trong hệ thống:</p>
    <ul style="margin-top:8px;margin-left:18px;">
        <li><strong>Người hiến máu</strong>: Tự đăng ký tài khoản, đăng nhập bằng trang riêng, chỉ được xem thông tin của mình, lịch hẹn, lịch sử hiến và thông báo.</li>
        <li><strong>Nhân viên / Admin</strong>: Đăng nhập bằng trang quản trị riêng, có quyền thêm/sửa/xóa dữ liệu người hiến, kiểm tra sức khỏe, lần hiến, kho máu, lịch hẹn và gửi thông báo.</li>
    </ul>
    <p style="margin-top:10px;">
        Nếu bạn là người hiến máu, vui lòng dùng nút <strong>Đăng nhập người hiến</strong> hoặc <strong>Đăng ký hiến máu</strong> ở thanh trên cùng.  
        Nếu bạn là quản trị viên, dùng liên kết <strong>Đăng nhập quản trị</strong>.
    </p>
</div>
<?php include 'footer.php'; ?>
