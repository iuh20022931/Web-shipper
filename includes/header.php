<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header id="header">
    <nav class="navbar">
        <div class="logo">
            <h1><a href="index.php" style="color: white; text-decoration: none;">FastGo</a></h1>
        </div>

        <button class="hamburger-menu" id="hamburger-btn">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <ul class="nav-menu" id="nav-menu">
            <li><a href="index.php#hero">Trang chủ</a></li>
            <li><a href="index.php#services">Dịch vụ</a></li>
            <li><a href="index.php#pricing">Bảng giá</a></li>
            <li><a href="tracking.php">Tra cứu đơn</a></li>
            <li><a href="index.php#contact">Liên hệ</a></li>
            <li><a href="huong-dan-dat-hang.html">Hướng dẫn</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Nếu đã login, hiện nút vào Dashboard tương ứng -->
                <li>
                    <a href="<?php echo ($_SESSION['role'] == 'admin') ? 'orders_manage.php' : 'dashboard.php'; ?>"
                        class="btn-secondary" style="padding: 8px 15px; border: 1px solid #fff; font-size: 14px;">
                        Vào Dashboard ➔
                    </a>
                </li>
            <?php else: ?>
                <li><a href="login.php">Đăng nhập</a></li>
                <li><a href="register.php" class="btn-primary"
                        style="padding: 8px 20px; background: #ff7a00; border:none; color:white;">Đăng ký</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>