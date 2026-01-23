<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header id="header" style="background-color: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <nav class="navbar">
        <div class="logo">
            <h1>
                <a href="dashboard.php" style="color: #0a2a66; text-decoration: none;">
                    FastGo <span style="font-size:14px; color:#ff7a00; font-weight:400;">Member</span>
                </a>
            </h1>
        </div>

        <button class="hamburger-menu" id="hamburger-btn">
            <span style="background-color: #0a2a66;"></span>
            <span style="background-color: #0a2a66;"></span>
            <span style="background-color: #0a2a66;"></span>
        </button>

        <ul class="nav-menu" id="nav-menu">
            <li><a href="index.php" style="color: #555;">Trang chủ</a></li>
            <li><a href="dashboard.php" style="color: #0a2a66; font-weight:600;">Tổng quan</a></li>
            <li><a href="index.php#contact" style="color: #555;">Đặt đơn mới</a></li>
            <li><a href="order_history.php" style="color: #555;">Lịch sử đơn</a></li>
            <li><a href="profile.php" style="color: #555;">Tài khoản</a></li>
            <li style="margin-left: 10px; display:flex; align-items:center; gap:10px;">
                <span style="color: #0a2a66; font-weight:600;">Hi,
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="logout.php"
                    style="color: #d9534f; font-size:14px; border:1px solid #d9534f; padding:4px 10px; border-radius:4px;">Đăng
                    xuất</a>
            </li>
        </ul>
    </nav>
</header>