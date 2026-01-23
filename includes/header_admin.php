<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
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
            <li><a href="index.php" target="_blank" style="color: #ff7a00;">Xem trang chủ ↗</a></li>
            <li style="margin-left: 15px;">
                <a href="logout.php"
                    style="color: #fff; background: #d9534f; padding: 6px 12px; border-radius: 4px;">Đăng xuất</a>
            </li>
        </ul>
    </nav>
</header>