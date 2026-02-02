<?php
require_once 'config/db.php';
header('Content-Type: application/json');

$code = $_GET['code'] ?? '';

if (empty($code)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã vận đơn']);
    exit;
}

// 1. Tìm đơn hàng trong bảng orders
$stmt = $conn->prepare("SELECT id, order_code, status, pickup_address, delivery_address, created_at, shipping_fee FROM orders WHERE order_code = ?");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng: ' . htmlspecialchars($code)]);
    exit;
}

$order = $result->fetch_assoc();

// 2. Lấy lịch sử hành trình từ order_logs
$logs = [];

// Thêm sự kiện mặc định "Đã tạo" dựa trên created_at của đơn hàng
$logs[] = [
    'status' => 'created',
    'time' => date('H:i d/m/Y', strtotime($order['created_at'])),
    'desc' => 'Đơn hàng đã được tạo'
];

$stmt_logs = $conn->prepare("SELECT new_status, changed_at FROM order_logs WHERE order_id = ? ORDER BY changed_at ASC");
$stmt_logs->bind_param("i", $order['id']);
$stmt_logs->execute();
$res_logs = $stmt_logs->get_result();

while ($row = $res_logs->fetch_assoc()) {
    $logs[] = [
        'status' => $row['new_status'],
        'time' => date('H:i d/m/Y', strtotime($row['changed_at'])),
        'desc' => 'Cập nhật trạng thái'
    ];
}

// Map trạng thái sang tiếng Việt để hiển thị đẹp hơn
$status_map = [
    'pending' => 'Chờ xử lý',
    'assigned' => 'Đã điều phối xe',
    'picked' => 'Đã lấy hàng',
    'delivering' => 'Đang giao hàng',
    'delivered' => 'Giao thành công',
    'cancelled' => 'Đã hủy'
];

$order['status_text'] = $status_map[$order['status']] ?? $order['status'];

echo json_encode([
    'success' => true,
    'data' => $order,
    'timeline' => $logs
]);
?>