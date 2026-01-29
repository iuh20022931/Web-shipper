<?php
session_start();
// Chỉ cho phép 'customer' truy cập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    // Nếu chưa đăng nhập, chuyển đến trang login và đính kèm trang này làm redirect
    header("Location: login.php?redirect=" . urlencode('create_order.php'));
    exit;
}

require_once 'config/db.php';

// Lấy thông tin user để auto-fill
$user_info = ['fullname' => '', 'phone' => '', 'email' => '', 'company_name' => '', 'tax_code' => '', 'company_address' => ''];
$stmt = $conn->prepare("SELECT fullname, phone, email, company_name, tax_code, company_address FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    $user_info = $res->fetch_assoc();
}
$stmt->close();

// Lấy danh sách địa chỉ đã lưu (MỚI)
$saved_addresses = [];
$addr_res = $conn->query("SELECT * FROM saved_addresses WHERE user_id = " . $_SESSION['user_id']);
if ($addr_res) {
    while ($r = $addr_res->fetch_assoc()) {
        $saved_addresses[] = $r;
    }
}

// Lấy danh sách dịch vụ từ DB
$services_list = [];
$svc_res = $conn->query("SELECT * FROM services ORDER BY base_price ASC");
if ($svc_res) {
    while ($r = $svc_res->fetch_assoc()) {
        $services_list[] = $r;
    }
}

// Lấy cấu hình giá
$pricing_config = ['weight_free' => 2, 'weight_price' => 5000, 'cod_min' => 5000];

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tạo đơn hàng mới | FastGo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/admin.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include 'includes/header_user.php'; ?>

    <main class="container">
        <div class="page-header">
            <h2 class="page-title">Tạo đơn hàng mới</h2>
            <a href="dashboard.php" class="back-link">← Quay lại Dashboard</a>
        </div>

        <form id="contact-form" class="order-form-container" method="POST">
            <!-- Thông tin người gửi -->
            <div class="form-section">
                <h3>1. Thông tin người gửi</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Họ và tên</label>
                        <input type="text" id="name" name="name"
                            value="<?php echo htmlspecialchars($user_info['fullname']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" id="phone" name="phone"
                            value="<?php echo htmlspecialchars($user_info['phone']); ?>" required>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1; position: relative;">
                        <div
                            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <label for="pickup-addr" style="margin-bottom:0;">Địa chỉ lấy hàng</label>
                            <?php if (!empty($saved_addresses)): ?>
                                <a href="#" onclick="openAddrModal('pickup'); return false;"
                                    style="font-size:13px; color:#ff7a00; text-decoration:none;">📍 Chọn từ sổ địa chỉ</a>
                            <?php endif; ?>
                        </div>
                        <label for="pickup-addr">Địa chỉ lấy hàng</label>
                        <input type="text" id="pickup-addr" name="pickup"
                            placeholder="Nhập số nhà, tên đường, phường/xã, quận/huyện..." required>
                    </div>
                </div>
            </div>

            <!-- Thông tin người nhận -->
            <div class="form-section">
                <h3>2. Thông tin người nhận</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="receiver_name">Họ và tên người nhận</label>
                        <input type="text" id="receiver_name" name="receiver_name" required>
                    </div>
                    <div class="form-group">
                        <label for="receiver_phone">Số điện thoại người nhận</label>
                        <input type="tel" id="receiver_phone" name="receiver_phone" required>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1; position: relative;">
                        <div
                            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <label for="delivery-addr" style="margin-bottom:0;">Địa chỉ giao hàng</label>
                            <?php if (!empty($saved_addresses)): ?>
                                <a href="#" onclick="openAddrModal('delivery'); return false;"
                                    style="font-size:13px; color:#ff7a00; text-decoration:none;">📍 Chọn từ sổ địa chỉ</a>
                            <?php endif; ?>
                        </div>
                        <label for="delivery-addr">Địa chỉ giao hàng</label>
                        <input type="text" id="delivery-addr" name="delivery"
                            placeholder="Nhập số nhà, tên đường, phường/xã, quận/huyện..." required>
                    </div>
                </div>
            </div>

            <!-- Thông tin gói hàng -->
            <div class="form-section">
                <h3>3. Thông tin gói hàng</h3>
                <div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                    <div class="form-group">
                        <label for="order-service-type">Loại dịch vụ</label>
                        <select id="order-service-type" name="service_type">
                            <?php foreach ($services_list as $svc): ?>
                                <option value="<?php echo $svc['type_key']; ?>">
                                    <?php echo $svc['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="package_type">Loại hàng hóa</label>
                        <select id="package_type" name="package_type">
                            <option value="document">Tài liệu</option>
                            <option value="food">Thực phẩm</option>
                            <option value="clothes">Quần áo</option>
                            <option value="electronic">Đồ điện tử</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="weight">Khối lượng (kg)</label>
                        <input type="number" id="weight" name="weight" value="1" min="0" step="0.5" required>
                    </div>
                    <div class="form-group">
                        <label for="cod_amount">Tiền thu hộ (COD)</label>
                        <input type="number" id="cod_amount" name="cod_amount" value="0" min="0"
                            placeholder="Để trống nếu không có">
                    </div>
                </div>
                <div class="form-group" style="margin-top: 20px;">
                    <label for="note">Ghi chú cho tài xế</label>
                    <textarea id="note" name="note"
                        placeholder="VD: Hàng dễ vỡ, vui lòng gọi trước khi giao..."></textarea>
                </div>
            </div>

            <!-- Thanh toán & Hóa đơn -->
            <div class="form-section">
                <h3>4. Thanh toán & Hóa đơn</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="payment_method">Phương thức thanh toán phí ship</label>
                        <select name="payment_method" id="payment_method">
                            <option value="cod">Thanh toán khi tài xế lấy hàng</option>
                            <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                        </select>
                    </div>
                    <div class="form-group" style="justify-content: center;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="is_corporate" id="is_corporate_checkbox" value="1"
                                style="width: auto; margin-right: 10px;">
                            Yêu cầu xuất hóa đơn công ty
                        </label>
                    </div>
                </div>
                <div id="corporate_info_fields">
                    <p style="font-weight: bold; color: #333; margin-top: 0;">Nhập thông tin công ty</p>
                    <div class="form-group">
                        <input type="text" name="company_name"
                            value="<?php echo htmlspecialchars($user_info['company_name'] ?? ''); ?>"
                            placeholder="Tên công ty (*)">
                    </div>
                    <div class="form-group">
                        <input type="text" name="company_tax_code"
                            value="<?php echo htmlspecialchars($user_info['tax_code'] ?? ''); ?>"
                            placeholder="Mã số thuế (*)">
                    </div>
                    <div class="form-group">
                        <textarea name="company_address"
                            placeholder="Địa chỉ công ty (*)"><?php echo htmlspecialchars($user_info['company_address'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <textarea name="company_bank_info" placeholder="Thông tin tài khoản (tùy chọn)"></textarea>
                    </div>
                </div>
            </div>

            <!-- Phí ship & Submit -->
            <div id="price-preview" style="display: none;">
                Phí vận chuyển dự kiến: <strong id="shipping-fee-display">0</strong>đ
                <input type="hidden" name="shipping_fee" id="shipping-fee-input" value="0">
            </div>
            <div id="form-message" style="display: none; margin-top: 20px;"></div>
            <button type="submit" class="btn-primary"
                style="width: 100%; margin-top: 20px; padding: 15px; font-size: 16px;">Xác nhận đặt đơn</button>
        </form>
    </main>

    <!-- Modal Chọn Địa Chỉ (MỚI) -->
    <div id="addr-modal" class="modal"
        style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div class="modal-content"
            style="background:#fff; margin:10% auto; padding:20px; width:90%; max-width:500px; border-radius:8px; position:relative;">
            <span onclick="document.getElementById('addr-modal').style.display='none'"
                style="position:absolute; right:15px; top:10px; cursor:pointer; font-size:24px;">&times;</span>
            <h3 style="color:#0a2a66; margin-bottom:15px;">Chọn địa chỉ</h3>
            <div style="max-height:300px; overflow-y:auto;">
                <?php foreach ($saved_addresses as $addr): ?>
                    <div class="addr-item"
                        onclick="selectAddr('<?php echo htmlspecialchars(addslashes($addr['address'])); ?>', '<?php echo htmlspecialchars(addslashes($addr['phone'])); ?>')"
                        style="padding:10px; border-bottom:1px solid #eee; cursor:pointer; transition:background 0.2s;">
                        <strong style="color:#0a2a66;"><?php echo htmlspecialchars($addr['name']); ?></strong>
                        <div style="font-size:14px; color:#555;"><?php echo htmlspecialchars($addr['address']); ?></div>
                        <div style="font-size:12px; color:#888;">SĐT: <?php echo htmlspecialchars($addr['phone']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="margin-top:15px; text-align:center;">
                <a href="address_book.php" target="_blank" style="color:#ff7a00; font-size:14px;">+ Quản lý sổ địa
                    chỉ</a>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Biến JS để script `main.js` có thể truy cập
        window.isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        window.servicesData =
            <?php echo json_encode($services_list, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        window.pricingConfig =
            <?php echo json_encode($pricing_config, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    </script>
    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle corporate fields
            const corporateCheckbox = document.getElementById('is_corporate_checkbox');
            if (corporateCheckbox) {
                corporateCheckbox.addEventListener('change', function () {
                    const corporateFields = document.getElementById('corporate_info_fields');
                    const companyNameInput = corporateFields.querySelector('[name="company_name"]');
                    const companyTaxInput = corporateFields.querySelector('[name="company_tax_code"]');
                    const companyAddressInput = corporateFields.querySelector('[name="company_address"]');

                    if (this.checked) {
                        corporateFields.style.display = 'block';
                        companyNameInput.required = true;
                        companyTaxInput.required = true;
                        companyAddressInput.required = true;
                    } else {
                        corporateFields.style.display = 'none';
                        companyNameInput.required = false;
                        companyTaxInput.required = false;
                        companyAddressInput.required = false;
                    }
                });
            }

            // Trigger initial calculation
            if (typeof calculateOrderShipping === 'function') {
                calculateOrderShipping();
            }
        });

        // Logic Modal Địa chỉ
        let currentAddrField = '';

        function openAddrModal(type) {
            currentAddrField = type; // 'pickup' hoặc 'delivery'
            document.getElementById('addr-modal').style.display = 'block';
        }

        function selectAddr(address, phone) {
            if (currentAddrField === 'pickup') {
                document.getElementById('pickup-addr').value = address;
                // Có thể tự điền SĐT người gửi nếu muốn, nhưng thường SĐT người gửi là cố định từ profile
            } else if (currentAddrField === 'delivery') {
                document.getElementById('delivery-addr').value = address;
                document.getElementById('receiver_phone').value = phone; // Điền luôn SĐT người nhận
            }
            document.getElementById('addr-modal').style.display = 'none';
            // Gọi lại hàm tính phí
            if (typeof calculateOrderShipping === 'function') calculateOrderShipping();
        }
    </script>
    <?php
    if (isset($conn) && $conn instanceof mysqli)
        $conn->close();
    ?>
</body>

</html>