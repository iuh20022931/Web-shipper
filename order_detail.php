<?php
session_start();
require_once 'config/db.php';

// Kiểm tra quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$msg = "";

// Xử lý phân công Shipper
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_shipper'])) {
    $shipper_id = intval($_POST['shipper_id']);
    $stmt = $conn->prepare("UPDATE orders SET shipper_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $shipper_id, $id);
    if ($stmt->execute()) {
        $msg = "Đã phân công shipper thành công!";
        // Refresh lại trang để lấy dữ liệu mới
        header("Refresh:0");
    } else {
        $msg = "Lỗi: " . $conn->error;
    }
}

// Xử lý Cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $override = isset($_POST['override_status']); // Checkbox override

    // 1. Lấy trạng thái hiện tại để kiểm tra
    $check_stmt = $conn->prepare("SELECT status FROM orders WHERE id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $curr = $check_stmt->get_result()->fetch_assoc();
    $old_status = $curr['status'];
    $check_stmt->close();

    // 2. Kiểm tra Workflow (Quy trình chặt chẽ)
    $allowed = false;
    if ($override || $old_status === $new_status) {
        $allowed = true;
    } else {
        // Quy tắc chuyển đổi hợp lệ
        switch ($old_status) {
            case 'pending': // Chờ xử lý -> Chỉ được sang Đang giao hoặc Hủy
                if (in_array($new_status, ['shipping', 'cancelled'])) $allowed = true;
                break;
            case 'shipping': // Đang giao -> Chỉ được sang Hoàn tất hoặc Hủy
                if (in_array($new_status, ['completed', 'cancelled'])) $allowed = true;
                break;
            default: // completed, cancelled -> Không được đổi tiếp nếu không tick Override
                $allowed = false;
                break;
        }
    }

    if ($allowed) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $id);
        if ($stmt->execute()) {
            $msg = "Cập nhật trạng thái thành công!";
            // 3. Ghi Log thay đổi
            $admin_id = $_SESSION['user_id'];
            $conn->query("INSERT INTO order_logs (order_id, user_id, old_status, new_status) VALUES ($id, $admin_id, '$old_status', '$new_status')");
        } else {
            $msg = "Lỗi hệ thống: " . $conn->error;
        }
    } else {
        $msg = "Không thể chuyển từ <b>$old_status</b> sang <b>$new_status</b> theo quy trình. Vui lòng chọn 'Cho phép sửa trạng thái bất kỳ' nếu cần thiết.";
    }
}

// Lấy thông tin đơn hàng
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die("Đơn hàng không tồn tại.");
}

// Lấy lịch sử thay đổi (Log)
$logs = [];
$log_res = $conn->query("SELECT l.*, u.fullname FROM order_logs l LEFT JOIN users u ON l.user_id = u.id WHERE l.order_id = $id ORDER BY l.changed_at DESC");
if ($log_res) while ($r = $log_res->fetch_assoc()) $logs[] = $r;

// Lấy danh sách Shipper
$shippers = [];
$s_res = $conn->query("SELECT id, fullname, phone FROM users WHERE role = 'shipper'");
if($s_res) while($r = $s_res->fetch_assoc()) $shippers[] = $r;

// Helper maps (dùng chung logic hiển thị)
$pkg_map = [
    'document' => 'Tài liệu',
    'food' => 'Đồ ăn',
    'clothes' => 'Quần áo',
    'electronic' => 'Điện tử',
    'other' => 'Khác'
];
$svc_map = [
    'standard' => 'Tiêu chuẩn',
    'express' => 'Hỏa tốc',
    'bulk' => 'Số lượng lớn'
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #
        <?php echo $order['order_code']; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/admin.css?v=<?php echo time(); ?>">
    <style>
    .detail-container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-top: 20px;
    }

    .detail-row {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }

    .detail-col {
        flex: 1;
        min-width: 300px;
        padding-right: 20px;
    }

    .detail-col h3 {
        color: #0a2a66;
        margin-bottom: 15px;
        font-size: 18px;
        border-bottom: 2px solid #ff7a00;
        display: inline-block;
        padding-bottom: 5px;
    }

    .info-group {
        margin-bottom: 10px;
        font-size: 15px;
    }

    .info-label {
        font-weight: 600;
        color: #555;
        width: 130px;
        display: inline-block;
    }

    .status-form {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        margin-top: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .log-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 14px;
    }

    .log-table th,
    .log-table td {
        padding: 10px;
        border-bottom: 1px solid #eee;
        text-align: left;
    }

    .log-table th {
        background: #f5f7fb;
        color: #0a2a66;
    }

    .log-section {
        margin-top: 30px;
        border-top: 1px solid #eee;
        padding-top: 20px;
    }

    .checkbox-override {
        font-size: 13px;
        color: #d9534f;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 8px;
    }
    </style>
</head>

<body>
    <?php include 'includes/header_admin.php'; ?>

    <main class="admin-container">
        <a href="orders_manage.php" class="back-link">← Quay lại danh sách</a>

        <div class="page-header">
            <h2 class="page-title">Chi tiết đơn hàng: <span style="color:#ff7a00">
                    <?php echo $order['order_code']; ?>
                </span></h2>
        </div>

        <?php if ($msg): ?>
        <div
            style="padding: 15px; background: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <?php echo $msg; ?>
        </div>
        <?php endif; ?>

        <div class="detail-container">
            <!-- Row 1: Thông tin chung & Cập nhật trạng thái -->
            <div class="detail-row">
                <div class="detail-col">
                    <h3>Thông tin chung</h3>
                    <div class="info-group"><span class="info-label">Mã đơn:</span> <strong>
                            <?php echo $order['order_code']; ?>
                        </strong></div>
                    <div class="info-group"><span class="info-label">Ngày tạo:</span>
                        <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                    </div>
                    <div class="info-group"><span class="info-label">Loại dịch vụ:</span>
                        <?php echo $svc_map[$order['service_type']] ?? $order['service_type']; ?>
                    </div>

                    <!-- Form Phân công Shipper -->
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #ccc;">
                        <form method="POST" style="display:flex; gap:10px; align-items:center;">
                            <span class="info-label">Shipper:</span>
                            <select name="shipper_id"
                                style="padding: 6px; border-radius: 4px; border: 1px solid #ccc; flex:1;">
                                <option value="0">-- Chưa phân công --</option>
                                <?php foreach($shippers as $s): ?>
                                <option value="<?php echo $s['id']; ?>"
                                    <?php echo $order['shipper_id'] == $s['id'] ? 'selected' : ''; ?>>
                                    <?php echo $s['fullname']; ?> (<?php echo $s['phone']; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="assign_shipper" class="btn-primary"
                                style="padding: 6px 12px; font-size: 13px;">Lưu</button>
                        </form>
                    </div>
                </div>
                <div class="detail-col">
                    <h3>Cập nhật trạng thái</h3>
                    <form method="POST" class="status-form"
                        onsubmit="return confirm('Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng này?');">
                        <select name="status"
                            style="padding: 10px; border-radius: 6px; border: 1px solid #ccc; flex: 1; font-size: 15px;">
                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>⏳ Chờ
                                xử lý</option>
                            <option value="shipping" <?php echo $order['status'] == 'shipping' ? 'selected' : ''; ?>>🚚
                                Đang giao hàng</option>
                            <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>✅
                                Hoàn tất</option>
                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>❌
                                Đã hủy</option>
                        </select>
                        <div style="flex:1">
                            <button type="submit" name="update_status" class="btn-primary"
                                style="padding: 10px 20px; border: none; cursor: pointer; width:100%">Cập nhật</button>
                            <label class="checkbox-override">
                                <input type="checkbox" name="override_status"> Cho phép sửa trạng thái bất kỳ
                            </label>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Row 2: Người gửi & Người nhận -->
            <div class="detail-row">
                <div class="detail-col">
                    <h3>Người gửi</h3>
                    <div class="info-group"><span class="info-label">Họ tên:</span>
                        <?php echo htmlspecialchars($order['name']); ?>
                    </div>
                    <div class="info-group"><span class="info-label">SĐT:</span>
                        <?php echo htmlspecialchars($order['phone']); ?>
                    </div>
                    <div class="info-group"><span class="info-label">Địa chỉ lấy:</span>
                        <?php echo htmlspecialchars($order['pickup_address']); ?>
                    </div>
                </div>
                <div class="detail-col">
                    <h3>Người nhận</h3>
                    <div class="info-group"><span class="info-label">Họ tên:</span>
                        <?php echo htmlspecialchars($order['receiver_name']); ?>
                    </div>
                    <div class="info-group"><span class="info-label">SĐT:</span>
                        <?php echo htmlspecialchars($order['receiver_phone']); ?>
                    </div>
                    <div class="info-group"><span class="info-label">Địa chỉ giao:</span>
                        <?php echo htmlspecialchars($order['delivery_address']); ?>
                    </div>
                </div>
            </div>

            <!-- Row 3: Hàng hóa & Thanh toán -->
            <div class="detail-row" style="border-bottom: none;">
                <div class="detail-col">
                    <h3>Thông tin hàng hóa</h3>
                    <div class="info-group"><span class="info-label">Loại hàng:</span>
                        <?php echo $pkg_map[$order['package_type']] ?? $order['package_type']; ?>
                    </div>
                    <div class="info-group"><span class="info-label">Khối lượng:</span>
                        <?php echo $order['weight']; ?> kg
                    </div>
                    <div class="info-group"><span class="info-label">Ghi chú:</span>
                        <?php echo nl2br(htmlspecialchars($order['note'])); ?>
                    </div>
                    <?php if($order['shipper_note']): ?><div class="info-group"
                        style="margin-top:10px; padding:10px; background:#fff3cd; border-radius:4px;"><span
                            class="info-label">💬 Shipper Note:</span>
                        <strong><?php echo nl2br(htmlspecialchars($order['shipper_note'])); ?></strong></div>
                    <?php endif; ?>
                </div>
                <div class="detail-col">
                    <h3>Thanh toán</h3>
                    <div class="info-group"><span class="info-label">Phí ship:</span> <strong style="color:#d9534f">
                            <?php echo number_format($order['shipping_fee']); ?>đ
                        </strong></div>
                    <div class="info-group"><span class="info-label">Thu hộ (COD):</span>
                        <?php echo number_format($order['cod_amount']); ?>đ
                    </div>
                    <div class="info-group"><span class="info-label">Tổng thu:</span> <strong
                            style="font-size:18px; color:#0a2a66">
                            <?php echo number_format($order['shipping_fee'] + $order['cod_amount']); ?>đ
                        </strong></div>
                </div>
            </div>

            <!-- Row 4: Lịch sử thay đổi -->
            <div class="log-section">
                <h3>📜 Lịch sử thay đổi trạng thái</h3>
                <?php if (!empty($logs)): ?>
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>Người thực hiện</th>
                            <th>Thay đổi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($logs as $log): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($log['changed_at'])); ?></td>
                            <td><?php echo htmlspecialchars($log['fullname']); ?></td>
                            <td>
                                <span
                                    class="status-badge status-<?php echo $log['old_status']; ?>"><?php echo $log['old_status']; ?></span>
                                ➔
                                <span
                                    class="status-badge status-<?php echo $log['new_status']; ?>"><?php echo $log['new_status']; ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="color:#999; font-style:italic;">Chưa có lịch sử thay đổi nào.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>

</html>