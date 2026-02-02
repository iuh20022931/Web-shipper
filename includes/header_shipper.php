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

// Detect current page for active state
$current_page = basename($_SERVER['PHP_SELF']);

// Count unread notifications
$unread_notifications_count = 0;
if (isset($_SESSION['user_id']) && isset($conn)) {
    $stmt_notify = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
    if ($stmt_notify) {
        $stmt_notify->bind_param("i", $_SESSION['user_id']);
        $stmt_notify->execute();
        $res_notify = $stmt_notify->get_result();
        if ($res_notify && $row_notify = $res_notify->fetch_assoc()) {
            $unread_notifications_count = $row_notify['count'];
        }
        $stmt_notify->close();
    }
}
?>
<link rel="stylesheet" href="assets/css/admin_styles.css?v=<?php echo time(); ?>">
<header id="header" class="header-admin">
    <nav class="navbar">
        <div class="logo">
            <h1>
                <a href="shipper_dashboard.php" class="header-logo-link">
                    FastGo <span class="header-accent">Shipper</span>
                </a>
            </h1>
        </div>

        <button class="hamburger-menu" id="hamburger-btn">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <ul class="nav-menu" id="nav-menu">
            <li class="<?php echo ($current_page === 'shipper_dashboard.php') ? 'active' : ''; ?>">
                <a href="shipper_dashboard.php">📊 Dashboard</a>
            </li>
            <li class="<?php echo ($current_page === 'shipper_orders.php') ? 'active' : ''; ?>">
                <a href="shipper_dashboard.php">📦 Đơn hàng của tôi</a>
            </li>
            
            <!-- Notification Bell -->
            <li class="notification-bell <?php echo ($current_page === 'notifications.php') ? 'active' : ''; ?>">
                <a href="notifications.php" class="notification-link">
                    🔔
                    <?php if ($unread_notifications_count > 0): ?>
                        <span class="notification-badge"><?php echo $unread_notifications_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- Account Dropdown -->
            <li class="has-submenu <?php echo in_array($current_page, ['shipper_profile.php', 'profile.php']) ? 'active' : ''; ?>">
                <a href="#" class="submenu-toggle">
                    👤 <?php echo htmlspecialchars($_SESSION['username'] ?? 'Tài khoản'); ?> <span class="arrow">▼</span>
                </a>
                <ul class="submenu">
                    <li class="<?php echo ($current_page === 'shipper_profile.php' || $current_page === 'profile.php') ? 'active' : ''; ?>">
                        <a href="shipper_profile.php">Hồ sơ</a>
                    </li>
                    <li>
                        <a href="logout.php" class="logout-link">Đăng xuất</a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</header>