<?php
session_start();
// 1. Kết nối database
require_once 'config/db.php';


// 2. Nhận dữ liệu từ form
$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';
$receiver_name = $_POST['receiver_name'] ?? '';
$receiver_phone = $_POST['receiver_phone'] ?? '';
$pickup = $_POST['pickup'] ?? '';
$delivery = $_POST['delivery'] ?? '';
$package_type = $_POST['package_type'] ?? '';
$service_type = $_POST['service_type'] ?? 'standard'; // Nhận loại dịch vụ
$weight = $_POST['weight'] ?? 0;
$cod_amount = $_POST['cod_amount'] ?? 0;
$shipping_fee = $_POST['shipping_fee'] ?? 0; // Nhận phí ship
$pickup_time = $_POST['pickup_time'] ?? '';
$note = $_POST['note'] ?? '';

$errors = [];

// Kiểm tra rỗng
if (empty($name))
    $errors[] = "Chưa nhập họ tên";
if (empty($phone))
    $errors[] = "Chưa nhập số điện thoại";
if (empty($receiver_name))
    $errors[] = "Chưa nhập tên người nhận";
if (empty($receiver_phone))
    $errors[] = "Chưa nhập SĐT người nhận";
if (empty($pickup))
    $errors[] = "Chưa nhập địa chỉ lấy hàng";
elseif (strlen($pickup) < 10)
    $errors[] = "Địa chỉ lấy hàng quá ngắn (tối thiểu 10 ký tự)";
elseif (!preg_match('/(quận|huyện|tp|thành phố|phường|xã|q\.|p\.|q\d)/iu', $pickup))
    $errors[] = "Địa chỉ lấy hàng thiếu Quận/Huyện (VD: Quận 1)";

if (empty($delivery))
    $errors[] = "Chưa nhập địa chỉ giao hàng";
elseif (strlen($delivery) < 10)
    $errors[] = "Địa chỉ giao hàng quá ngắn (tối thiểu 10 ký tự)";
elseif (!preg_match('/(quận|huyện|tp|thành phố|phường|xã|q\.|p\.|q\d)/iu', $delivery))
    $errors[] = "Địa chỉ giao hàng thiếu Quận/Huyện (VD: Quận 1)";

// Kiểm tra số điện thoại hợp lệ
if (!preg_match('/^0[0-9]{9,10}$/', $phone))
    $errors[] = "Số điện thoại không hợp lệ (phải bắt đầu bằng 0)";
if (!preg_match('/^0[0-9]{9,10}$/', $receiver_phone))
    $errors[] = "SĐT người nhận không hợp lệ";

// Kiểm tra weight >=0
if (!is_numeric($weight) || $weight < 0)
    $errors[] = "Khối lượng phải >= 0";

// Kiểm tra COD >= 0
if (!empty($cod_amount) && (!is_numeric($cod_amount) || $cod_amount < 0))
    $errors[] = "Tiền thu hộ phải >= 0";

// Kiểm tra thời gian lấy hàng (nếu có nhập thì không được là quá khứ quá xa - tuỳ logic)
// Ở đây chỉ kiểm tra định dạng cơ bản để tránh SQL injection lạ, dù bind_param đã lo rồi.
// Ví dụ đơn giản: nếu nhập thì phải có độ dài hợp lý
if (!empty($pickup_time) && strlen($pickup_time) > 50)
    $errors[] = "Thời gian lấy hàng không hợp lệ";

// Kiểm tra package_type hợp lệ
$valid_types = ['document', 'food', 'clothes', 'electronic', 'other'];
if (!in_array($package_type, $valid_types))
    $errors[] = "Loại hàng không hợp lệ";

if (count($errors) > 0) {
    foreach ($errors as $err)
        echo $err . "<br>";
    exit; // dừng submit nếu có lỗi
}

// Lấy user_id nếu đã đăng nhập
$user_id = !empty($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Tạo mã đơn hàng (Ví dụ: FAST-A1B2C3)
$order_code = 'FAST-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

// 3. Chèn vào database
// Sử dụng prepared statements để chống SQL Injection
// LƯU Ý: Cần thêm cột `user_id` (INT, NULLABLE) vào bảng `orders` trong Database
$stmt = $conn->prepare("INSERT INTO `orders` 
(order_code, name, phone, receiver_name, receiver_phone, pickup_address, delivery_address, package_type, service_type, weight, cod_amount, shipping_fee, pickup_time, note, user_id) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    error_log("Order Prepare Failed: " . $conn->error);
    die("Lỗi hệ thống. Không thể tạo đơn hàng.");
}
$stmt->bind_param("sssssssssdddssi", $order_code, $name, $phone, $receiver_name, $receiver_phone, $pickup, $delivery, $package_type, $service_type, $weight, $cod_amount, $shipping_fee, $pickup_time, $note, $user_id);

if ($stmt->execute()) {
    echo "SUCCESS";
} else {
    error_log("Order Execute Failed: " . $stmt->error);
    echo "ERROR: Có lỗi xảy ra khi lưu đơn hàng.";
}

// 4. Đóng kết nối

$stmt->close();
$conn->close();
?>