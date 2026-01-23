<?php
session_start();
require_once 'config/db.php';

// Kiểm tra quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Xử lý Thêm thành viên mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];

    // Kiểm tra trùng lặp
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $error = "Tên đăng nhập hoặc Email đã tồn tại.";
    } else {
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, fullname, email, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $hashed_pass, $fullname, $email, $phone, $role);
        if ($stmt->execute()) {
            $msg = "Đã tạo tài khoản mới thành công.";
        } else {
            $error = "Lỗi: " . $conn->error;
        }
    }
}

// Xử lý Cập nhật vai trò (Role)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['role'];

    if ($user_id != $_SESSION['user_id']) { // Không cho tự sửa quyền mình
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $new_role, $user_id);
        $stmt->execute();
        $msg = "Đã cập nhật vai trò thành công.";
    }
}

// Xử lý Xóa thành viên
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    // Không cho phép tự xóa chính mình
    if ($delete_id != $_SESSION['user_id']) {
        // Xóa user (Các bảng orders/recipients sẽ tự xử lý theo Foreign Key nếu đã cấu hình, 
        // hoặc user_id trong orders sẽ về NULL nếu set ON DELETE SET NULL)
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $delete_id);

        if ($stmt->execute()) {
            $msg = "Đã xóa thành viên thành công.";
        } else {
            $error = "Lỗi khi xóa: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error = "Không thể xóa tài khoản đang đăng nhập.";
    }
}

// Xử lý Tìm kiếm
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10; // Số thành viên mỗi trang
$offset = ($page - 1) * $limit;
if ($page < 1)
    $page = 1;

// 1. Đếm tổng số bản ghi
$count_sql = "SELECT COUNT(*) as total FROM users WHERE 1=1";
$sql = "SELECT * FROM users WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $condition = " AND (username LIKE ? OR fullname LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $sql .= $condition;
    $count_sql .= $condition;
    $term = "%$search%";
    $params = [$term, $term, $term, $term];
    $types = "ssss";
}

// Thực hiện đếm
$stmt_count = $conn->prepare($count_sql);
if (!empty($params))
    $stmt_count->bind_param($types, ...$params);
$stmt_count->execute();
$total_records = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);
$stmt_count->close();

// 2. Lấy dữ liệu phân trang
$sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
if (!empty($params))
    $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý thành viên | Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/admin.css?v=<?php echo time(); ?>">
    <style>
        .add-user-form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            display: none;
        }

        .add-user-form.active {
            display: block;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .role-select {
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <?php include 'includes/header_admin.php'; ?>

    <main class="admin-container">
        <div class="page-header">
            <h2 class="page-title">Quản lý thành viên</h2>
            <a href="orders_manage.php" class="back-link">← Quay lại Đơn hàng</a>
            <button onclick="document.getElementById('add-user-box').classList.toggle('active')" class="btn-primary"
                style="margin-left: auto;">+ Thêm thành viên</button>
        </div>

        <?php if (isset($msg)): ?>
            <div
                style="padding: 15px; background: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div
                style="padding: 15px; background: #f8d7da; color: #721c24; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Form Thêm User (Mặc định ẩn) -->
        <div id="add-user-box" class="add-user-form">
            <h3 style="margin-bottom: 15px; color: #0a2a66;">Tạo tài khoản mới</h3>
            <form method="POST">
                <div class="form-grid">
                    <input type="text" name="username" placeholder="Tên đăng nhập" required
                        style="padding:10px; border:1px solid #ddd; border-radius:4px;">
                    <input type="password" name="password" placeholder="Mật khẩu" required
                        style="padding:10px; border:1px solid #ddd; border-radius:4px;">
                    <input type="text" name="fullname" placeholder="Họ và tên" required
                        style="padding:10px; border:1px solid #ddd; border-radius:4px;">
                    <input type="email" name="email" placeholder="Email" required
                        style="padding:10px; border:1px solid #ddd; border-radius:4px;">
                    <input type="text" name="phone" placeholder="Số điện thoại" required
                        style="padding:10px; border:1px solid #ddd; border-radius:4px;">
                    <select name="role" style="padding:10px; border:1px solid #ddd; border-radius:4px;">
                        <option value="customer">Khách hàng</option>
                        <option value="shipper">Shipper</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" name="add_user" class="btn-primary">Lưu tài khoản</button>
            </form>
        </div>

        <div class="dashboard-layout">
            <!-- Cột trái: Bảng dữ liệu -->
            <div class="table-section">
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên đăng nhập</th>
                            <th>Họ tên</th>
                            <th>Liên hệ</th>
                            <th>Vai trò</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#
                                    <?php echo $row['id']; ?>
                                </td>
                                <td><strong>
                                        <?php echo htmlspecialchars($row['username']); ?>
                                    </strong></td>
                                <td>
                                    <?php echo htmlspecialchars($row['fullname']); ?>
                                </td>
                                <td>
                                    📧
                                    <?php echo htmlspecialchars($row['email']); ?><br>
                                    📞
                                    <?php echo htmlspecialchars($row['phone']); ?>
                                </td>
                                <td>
                                    <?php if ($row['id'] == $_SESSION['user_id']): ?>
                                        <span class="status-badge status-completed">Admin (Tôi)</span>
                                    <?php else: ?>
                                        <form method="POST" style="display:flex; gap:5px; align-items:center;">
                                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                            <select name="role" class="role-select"
                                                onchange="if(confirm('Đổi vai trò user này?')) this.form.submit()">
                                                <option value="customer" <?php if ($row['role'] == 'customer')
                                                    echo 'selected'; ?>>
                                                    Khách hàng</option>
                                                <option value="shipper" <?php if ($row['role'] == 'shipper')
                                                    echo 'selected'; ?>>
                                                    Shipper</option>
                                                <option value="admin" <?php if ($row['role'] == 'admin')
                                                    echo 'selected'; ?>>Admin
                                                </option>
                                            </select>
                                            <input type="hidden" name="update_role" value="1">
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                        <a href="users_manage.php?delete_id=<?php echo $row['id']; ?>" class="btn-action"
                                            style="color: #d9534f; border-color: #d9534f;"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa thành viên này?');">
                                            Xóa
                                        </a>
                                    <?php else: ?>
                                        <span style="color:#999; font-size:12px;">(Tôi)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <?php if ($total_pages > 1): ?>
                <div style="margin-top: 20px; display: flex; justify-content: center; gap: 5px;">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" class="btn-action"
                            style="text-decoration: none;">&laquo; Trước</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="btn-action"
                            style="text-decoration: none; <?php echo ($i == $page) ? 'background-color: #0a2a66; color: white;' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" class="btn-action"
                            style="text-decoration: none;">Sau &raquo;</a>
                    <?php endif; ?>
                </div>
                <p style="text-align: center; margin-top: 10px; font-size: 14px; color: #666;">Trang
                    <?php echo $page; ?>/<?php echo $total_pages; ?> (Tổng <?php echo $total_records; ?> thành viên)</p>
            <?php endif; ?>

            <!-- Cột phải: Sidebar tìm kiếm -->
            <aside class="filter-sidebar">
                <h3>Tìm kiếm</h3>
                <form class="filter-form" method="GET">
                    <div class="form-group">
                        <label>Từ khóa</label>
                        <input type="text" name="search" placeholder="Tên, Email, SĐT..."
                            value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <button type="submit" class="btn-filter">Tìm kiếm</button>
                    <a href="users_manage.php" class="btn-reset">Đặt lại</a>
                </form>
            </aside>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>

</html>