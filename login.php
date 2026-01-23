<?php
session_start();
require_once 'config/db.php';

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_msg = "Vui lòng nhập tên đăng nhập và mật khẩu.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Đăng nhập thành công
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Phân quyền chuyển hướng
                if ($user['role'] === 'admin') {
                    header("Location: orders_manage.php");
                } elseif ($user['role'] === 'shipper') {
                    header("Location: shipper_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit;
            } else {
                $error_msg = "Mật khẩu không chính xác.";
            }
        } else {
            $error_msg = "Tên đăng nhập không tồn tại.";
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
    <title>Đăng nhập | FastGo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>">
    <style>
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

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
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
        <h2 class="auth-title">Đăng Nhập FastGo</h2>
        <?php if (!empty($error_msg)): ?>
            <div class="error-box">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="username" required
                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Đăng Nhập</button>
        </form>
        <div class="auth-link">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></div>
    </div>
</body>

</html>