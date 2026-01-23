<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header id="header" style="background-color: #0a2a66; border-bottom: 3px solid #ff7a00;">
    <nav class="navbar">
        <div class="logo">
            <h1>
                <a href="shipper_dashboard.php" style="color: white; text-decoration: none;">
                    FastGo <span style="color:#ff7a00;">Shipper</span>
                </a>
            </h1>
        </div>

        <button class="hamburger-menu" id="hamburger-btn">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <ul class="nav-menu" id="nav-menu">
            <li><a href="shipper_dashboard.php" style="color: #fff;">Đơn hàng của tôi</a></li>
            <li style="margin-left: 15px;">
                <a href="logout.php"
                    style="color: #fff; background: #d9534f; padding: 6px 12px; border-radius: 4px;">Đăng xuất</a>
            </li>
        </ul>
    </nav>
</header>