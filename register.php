<?php
session_start();

// 1. Kết nối database
require_once 'config/db.php';

$error_msg = "";

// 2. Xử lý khi submit form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate cơ bản
    if (empty($username) || empty($password) || empty($email) || empty($phone) || empty($fullname)) {
        $error_msg = "Vui lòng nhập đầy đủ thông tin.";
    } elseif ($password !== $confirm_password) {
        $error_msg = "Mật khẩu xác nhận không khớp.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Email không hợp lệ.";
    } elseif (!preg_match('/^0[0-9]{9,10}$/', $phone)) {
        $error_msg = "Số điện thoại không hợp lệ.";
    } else {
        // Kiểm tra username đã tồn tại chưa
        // Kiểm tra cả username hoặc email đã tồn tại chưa
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_msg = "Tên đăng nhập hoặc Email đã được sử dụng.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'customer'; // Mặc định là khách hàng

            // Insert vào DB
            $insert_stmt = $conn->prepare("INSERT INTO users (username, email, phone, fullname, password, role) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("ssssss", $username, $email, $phone, $fullname, $hashed_password, $role);

            if ($insert_stmt->execute()) {
                // BẢO MẬT: Chống Session Fixation
                session_regenerate_id(true);

                // Đăng ký thành công, tự động đăng nhập và chuyển hướng đến dashboard
                $_SESSION['user_id'] = $insert_stmt->insert_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
                header("Location: dashboard.php");
                exit;
            } else {
                error_log("Register Error: " . $conn->error);
                $error_msg = "Có lỗi xảy ra trong quá trình đăng ký. Vui lòng thử lại.";
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản | FastGo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>">
    <style>
        /* Style riêng cho trang auth để căn giữa */
        body {
            background-color: #f5f7fb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .auth-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .auth-title {
            text-align: center;
            color: #0a2a66;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .error-box {
            background: #fee;
            color: #d9534f;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }

        .auth-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .auth-link a {
            color: #ff7a00;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="auth-card">
        <h2 class="auth-title">Đăng Ký FastGo</h2>

        <?php if (!empty($error_msg)): ?>
            <div class="error-box"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="username" required placeholder="Nhập username"
                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="fullname" required placeholder="Nhập họ tên đầy đủ"
                    value="<?php echo htmlspecialchars($_POST['fullname'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="example@gmail.com"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="tel" name="phone" required placeholder="09xxxxxxxxx"
                    value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" required placeholder="Nhập mật khẩu">
            </div>
            <div class="form-group">
                <label>Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" required placeholder="Nhập lại mật khẩu">
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Đăng Ký</button>
        </form>
        <div class="auth-link">Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></div>
    </div>
</body>
<script src="assets/js/main.js?v=<?php echo time(); ?>"></script>

</html>