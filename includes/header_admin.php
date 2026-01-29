<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- FIX: Kiểm tra tài khoản bị khóa (Force Logout) ---
if (isset($_SESSION['user_id']) && isset($conn)) {
    $stmt_lock = $conn->prepare("SELECT is_locked FROM users WHERE id = ?");
    $stmt_lock->bind_param("i", $_SESSION['user_id']);
    $stmt_lock->execute();
    $res_lock = $stmt_lock->get_result();
    if ($res_lock && $row_lock = $res_lock->fetch_assoc()) {
        if ($row_lock['is_locked'] == 1) {
            echo '<script>alert("Tài khoản của bạn đã bị khóa."); window.location.href="logout.php";</script>';
            exit;
        }
    }
    $stmt_lock->close();
}
?>
<link rel="stylesheet" href="assets/css/admin_styles.css?v=<?php echo time(); ?>">
<header id="header" style="background-color: #1a1a1a; border-bottom: 3px solid #ff7a00;">
    <nav class="navbar">
        <div class="logo">
            <h1>
                <a href="orders_manage.php" style="color: white; text-decoration: none;">
                    FastGo <span style="color:#ff7a00;">Admin</span>
                </a>
            </h1>
        </div>

        <button class="hamburger-menu" id="hamburger-btn">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <ul class="nav-menu" id="nav-menu">
            <li><a href="orders_manage.php" style="color: #fff;">Quản lý đơn hàng</a></li>
            <li><a href="users_manage.php" style="color: #fff;">Quản lý User</a></li>
            <li><a href="services_manage.php" style="color: #fff;">Dịch vụ & Giá</a></li>
            <li><a href="admin_stats.php" style="color: #fff;">Thống kê</a></li>
            <li><a href="admin_refund_report.php" style="color: #fff;">Báo cáo hoàn tiền</a></li>
            <li><a href="contact_manage.php" style="color: #fff;">Khiếu nại</a></li>
            <li><a href="testimonials_manage.php" style="color: #fff;">Đánh giá</a></li>
            <li><a href="index.php" target="_blank" style="color: #ff7a00;">Xem trang chủ ↗</a></li>
            <li style="margin-left: 15px;">
                <a href="logout.php"
                    style="color: #fff; background: #d9534f; padding: 6px 12px; border-radius: 4px;">Đăng xuất</a>
            </li>
        </ul>
    </nav>
</header>