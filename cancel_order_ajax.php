<?php
require_once 'config/db.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = trim($_POST['code'] ?? '');

    if (empty($code)) {
        echo json_encode(['status' => 'error', 'message' => 'Thiếu mã đơn hàng.']);
        exit;
    }

    // Kiểm tra trạng thái hiện tại của đơn hàng
    $stmt = $conn->prepare("SELECT id, status FROM orders WHERE order_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Đơn hàng không tồn tại.']);
        exit;
    }

    $order = $res->fetch_assoc();

    // Chỉ cho phép hủy nếu đơn đang "Chờ xử lý"
    if ($order['status'] !== 'pending') {
        echo json_encode(['status' => 'error', 'message' => 'Không thể hủy đơn hàng này (đã giao hoặc đã hủy).']);
        exit;
    }

    // Cập nhật trạng thái thành 'cancelled'
    $update = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
    $update->bind_param("i", $order['id']);

    if ($update->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Đã hủy đơn hàng thành công.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống.']);
    }
}
?>