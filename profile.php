<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";
$msg_class = "";

// Xử lý Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Cập nhật thông tin
    if (isset($_POST['update_info'])) {
        $fullname = trim($_POST['fullname']);
        $phone = trim($_POST['phone']);

        if (empty($fullname) || empty($phone)) {
            $msg = "Vui lòng nhập đầy đủ họ tên và SĐT.";
            $msg_class = "error";
        } else {
            $stmt = $conn->prepare("UPDATE users SET fullname = ?, phone = ? WHERE id = ?");
            $stmt->bind_param("ssi", $fullname, $phone, $user_id);
            if ($stmt->execute()) {
                $msg = "Cập nhật thông tin thành công!";
                $msg_class = "success";
            } else {
                $msg = "Lỗi hệ thống: " . $conn->error;
                $msg_class = "error";
            }
        }
    }

    // 2. Đổi mật khẩu
    if (isset($_POST['change_pass'])) {
        $old_pass = $_POST['old_pass'];
        $new_pass = $_POST['new_pass'];
        $confirm_pass = $_POST['confirm_pass'];

        // Lấy pass cũ từ DB
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();

        if (!password_verify($old_pass, $user['password'])) {
            $msg = "Mật khẩu cũ không chính xác.";
            $msg_class = "error";
        } elseif ($new_pass !== $confirm_pass) {
            $msg = "Mật khẩu xác nhận không khớp.";
            $msg_class = "error";
        } elseif (strlen($new_pass) < 6) {
            $msg = "Mật khẩu mới phải từ 6 ký tự trở lên.";
            $msg_class = "error";
        } else {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $upd->bind_param("si", $hashed, $user_id);
            if ($upd->execute()) {
                $msg = "Đổi mật khẩu thành công!";
                $msg_class = "success";
            }
        }
    }
}

// Lấy thông tin hiển thị
$stmt = $conn->prepare("SELECT username, email, fullname, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_info = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thông tin tài khoản | FastGo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>">
    <style>
    .profile-card {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: 600;
        color: #0a2a66;
        margin-bottom: 5px;
        display: block;
    }

    .msg-box {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
    }

    .msg-box.success {
        background: #d4edda;
        color: #155724;
    }

    .msg-box.error {
        background: #f8d7da;
        color: #721c24;
    }
    </style>
</head>

<body>
    <?php include 'includes/header_user.php'; ?>

    <main class="container" style="padding: 40px 20px; max-width: 800px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 class="section-title" style="margin:0;">Hồ sơ cá nhân</h2>
            <a href="dashboard.php" class="btn-secondary"
                style="color:#0a2a66; border-color:#0a2a66; padding:8px 15px;">← Quay lại</a>
        </div>

        <?php if ($msg): ?>
        <div class="msg-box <?php echo $msg_class; ?>">
            <?php echo $msg; ?>
        </div>
        <?php endif; ?>

        <div class="profile-card">
            <h3 style="margin-bottom:20px; color:#ff7a00;">Thông tin chung</h3>
            <form method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn lưu thay đổi thông tin cá nhân?');">
                <div class="form-group"><label>Tên đăng nhập</label><input type="text"
                        value="<?php echo htmlspecialchars($user_info['username']); ?>" disabled
                        style="background:#eee;"></div>
                <div class="form-group"><label>Email</label><input type="text"
                        value="<?php echo htmlspecialchars($user_info['email']); ?>" disabled style="background:#eee;">
                </div>
                <div class="form-group"><label>Họ và tên</label><input type="text" name="fullname"
                        value="<?php echo htmlspecialchars($user_info['fullname']); ?>" required></div>
                <div class="form-group"><label>Số điện thoại</label><input type="text" name="phone"
                        value="<?php echo htmlspecialchars($user_info['phone']); ?>" required></div>
                <button type="submit" name="update_info" class="btn-primary" style="margin-top:15px;">Cập nhật thông
                    tin</button>
            </form>
        </div>

        <div class="profile-card">
            <h3 style="margin-bottom:20px; color:#ff7a00;">Đổi mật khẩu</h3>
            <form method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn đổi mật khẩu không?');">
                <div class="form-group"><label>Mật khẩu cũ</label><input type="password" name="old_pass" required></div>
                <div class="form-group"><label>Mật khẩu mới</label><input type="password" name="new_pass" required>
                </div>
                <div class="form-group"><label>Nhập lại mật khẩu mới</label><input type="password" name="confirm_pass"
                        required></div>
                <button type="submit" name="change_pass" class="btn-secondary"
                    style="margin-top:15px; background:#0a2a66; color:white; border:none;">Đổi mật khẩu</button>
            </form>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>

</html>