<!doctype html>
<?php
// Logic lấy thông tin user nếu đã đăng nhập để auto-fill
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_info = ['fullname' => '', 'phone' => ''];
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT fullname, phone FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0)
        $user_info = $res->fetch_assoc();
    $stmt->close();
}

// --- LOGIC ĐẶT LẠI ĐƠN (RE-ORDER) ---
$reorder_data = [];
if (isset($_GET['reorder_id']) && isset($_SESSION['user_id'])) {
    $rid = intval($_GET['reorder_id']);
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $rid, $_SESSION['user_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $reorder_data = $res->fetch_assoc();
    }
}
// ------------------------------------

// --- LẤY DANH SÁCH DỊCH VỤ TỪ DB ---
$services_list = [];
$svc_res = $conn->query("SELECT * FROM services ORDER BY base_price ASC");
if ($svc_res) {
    while ($r = $svc_res->fetch_assoc()) $services_list[] = $r;
}

// Cấu hình phí cứng (vì đã bỏ DB settings)
$pricing_config = ['weight_free'=>2, 'weight_price'=>5000, 'cod_min'=>5000];
?>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <title>Dịch vụ Shipper | FastGo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>" />
</head>

<body>
    <!-- HEADER -->
    <?php include 'includes/header.php'; ?>

    <!-- HERO SECTION -->
    <section id="hero" class="hero-section">
        <div class="container hero-container">
            <div class="hero-content">
                <h1 class="animate-top">Giao hàng nhanh – An toàn – Đúng giờ</h1>
                <p class="animate-bottom">
                    Dịch vụ giao hàng nội thành & liên tỉnh, hỗ trợ 24/7. Nhận hàng ngay sau 15 phút!
                </p>
                <div class="hero-btns animate-bottom">
                    <a href="#quick-quote" class="btn-primary">Tính giá ngay</a>
                    <a href="#contact" class="btn-secondary">Liên hệ đặt ship</a>
                </div>
            </div>
            <div class="hero-image animate-right">
                <img src="assets/images/hero.png" alt="FastGo Shipper" />
            </div>
        </div>
    </section>

    <!-- PROCESS -->
    <section id="process" class="process-section">
        <div class="container">
            <h2 class="section-title">Quy trình giao hàng đơn giản</h2>
            <p class="section-subtitle">Chỉ với 3 bước, hàng hóa của bạn sẽ được giao đến nơi an toàn</p>
            <div class="process-container">
                <div class="process-item animate-up">
                    <img src="assets/images/order.png" alt="Đặt đơn" class="process-img" />
                    <h3>Đặt đơn trực tuyến</h3>
                    <p>Nhập thông tin người gửi, người nhận và loại dịch vụ ngay trên website.</p>
                </div>
                <div class="process-arrow">➔</div>
                <div class="process-item animate-up" style="animation-delay: 0.2s">
                    <img src="assets/images/hero-shipper.png" alt="Lấy hàng" class="process-img" />
                    <h3>Lấy hàng trong 15p</h3>
                    <p>Shipper gần nhất sẽ đến nhận hàng chỉ sau vài phút xác nhận đơn.</p>
                </div>
                <div class="process-arrow">➔</div>
                <div class="process-item animate-up" style="animation-delay: 0.4s">
                    <img src="assets/images/package.png" alt="Giao hàng" class="process-img" />
                    <h3>Giao tận tay</h3>
                    <p>Hàng hóa được vận chuyển siêu tốc và giao tận tay người nhận an toàn.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICES -->
    <section id="services">
        <h2 class="section-title">Dịch vụ của chúng tôi</h2>
        <p class="section-desc">FastGo cung cấp đa dạng dịch vụ giao hàng, đáp ứng mọi nhu cầu của bạn.</p>
        <div class="service-list" id="services-list">
            <div class="service-card" id="standard-delivery">
                <h3>Giao nội thành</h3>
                <p>Giao hàng nhanh trong khu vực nội thành chỉ từ 30–60 phút.</p>
            </div>
            <div class="service-card" id="express-delivery">
                <h3>Giao hỏa tốc</h3>
                <p>Ưu tiên đơn gấp, giao ngay trong thời gian sớm nhất.</p>
            </div>
            <div class="service-card" id="cod-delivery">
                <h3>Giao COD</h3>
                <p>Thu hộ tiền mặt an toàn, minh bạch và nhanh chóng.</p>
            </div>
            <div class="service-card" id="bulk-delivery">
                <h3>Giao hàng số lượng lớn</h3>
                <p>Hỗ trợ doanh nghiệp và shop online giao nhiều đơn cùng lúc.</p>
            </div>
            <div class="service-card" id="corporate-delivery">
                <h3>Dịch vụ doanh nghiệp</h3>
                <p>Giải pháp giao hàng chuyên nghiệp cho doanh nghiệp.</p>
            </div>
            <div class="service-card" id="moving-service">
                <h3>Chuyển nhà / Vận chuyển lớn</h3>
                <p>Hỗ trợ vận chuyển hàng hóa lớn, chuyển nhà, văn phòng.</p>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section id="features">
        <h2 class="section-title">Vì sao chọn FastGo?</h2>
        <div class="feature-list">
            <div class="feature-item"><span class="feature-icon">🚀</span>
                <h3>Nhanh & đúng giờ</h3>
                <p>Thời gian giao hàng được tối ưu, đảm bảo đúng hẹn.</p>
            </div>
            <div class="feature-item"><span class="feature-icon">📦</span>
                <h3>Theo dõi đơn hàng</h3>
                <p>Khách hàng dễ dàng theo dõi trạng thái đơn hàng.</p>
            </div>
            <div class="feature-item"><span class="feature-icon">👨‍✈️</span>
                <h3>Shipper chuyên nghiệp</h3>
                <p>Đội ngũ shipper được đào tạo bài bản.</p>
            </div>
            <div class="feature-item"><span class="feature-icon">☎️</span>
                <h3>Hỗ trợ 24/7</h3>
                <p>Sẵn sàng hỗ trợ khách hàng mọi lúc, mọi nơi.</p>
            </div>
        </div>
    </section>

    <!-- PRICING -->
    <section id="pricing">
        <h2 class="section-title">Bảng giá tham khảo</h2>
        <div class="pricing-table-wrapper">
            <table class="pricing-table">
                <thead>
                    <tr>
                        <th>Dịch vụ</th>
                        <th>Phương tiện</th>
                        <th>Khu vực</th>
                        <th>Giá</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services_list as $service): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($service['name']); ?></td>
                        <td>
                            <?php
                                // Giả định phương tiện dựa trên loại dịch vụ
                                if ($service['type_key'] == 'bulk') {
                                    echo 'Ô tô';
                                } else {
                                    echo 'Xe máy';
                                }
                                ?>
                        </td>
                        <td>Nội thành</td>
                        <td>
                            <?php
                                if ($service['base_price'] > 0) {
                                    echo number_format($service['base_price']) . 'đ';
                                } else {
                                    echo 'Liên hệ'; // Hiển thị 'Liên hệ' nếu giá là 0 hoặc không xác định
                                }
                                ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <!-- Dòng phụ phí COD có thể giữ lại để cung cấp thông tin -->
                    <tr>
                        <td>Giao COD</td>
                        <td>Xe máy</td>
                        <td>Nội thành</td>
                        <td>+5.000đ</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- QUICK QUOTE -->
    <section id="quick-quote">
        <h2 class="section-title">Tính giá cước nhanh</h2>
        <form id="quick-quote-form">
            <input type="text" id="from-location" list="districts-list" placeholder="Điểm đi (Quận/Huyện)" required />
            <input type="text" id="to-location" list="districts-list" placeholder="Điểm đến (Quận/Huyện)" required />
            <select id="service-type" required>
                <option value="">-- Chọn loại dịch vụ --</option>
                <?php foreach($services_list as $svc): ?>
                <option value="<?php echo $svc['type_key']; ?>">
                    <?php echo $svc['name']; ?>
                    (<?php echo ($svc['base_price'] > 0) ? number_format($svc['base_price']).'đ' : 'Liên hệ'; ?>)
                </option>
                <?php endforeach; ?>
            </select>
            <label class="checkbox-label"><input type="checkbox" id="is-cod" /> Có thu hộ COD (+5k)</label>
            <button type="submit" class="btn-primary">Tính giá ngay</button>
        </form>
        <datalist id="districts-list">
            <option value="Quận 1" />
            <option value="Quận 2" />
            <option value="Quận 3" />
            <option value="Quận 4" />
            <option value="Quận 5" />
            <option value="Quận 7" />
            <option value="Bình Thạnh" />
            <option value="Tân Bình" />
            <option value="Thủ Đức" />
        </datalist>
        <div id="quote-result" class="quote-result"></div>
    </section>

    <!-- CONTACT -->
    <section id="contact">
        <h2 class="section-title">Liên hệ đặt ship</h2>
        <form id="contact-form" method="POST" action="order.php" novalidate>
            <div class="form-section">
                <h4><i class="icon">👤</i> Thông tin người gửi</h4>
                <div class="form-group">
                    <div><input type="text" id="name" name="name" placeholder="Họ và tên" required
                            value="<?php echo htmlspecialchars($reorder_data['name'] ?? $user_info['fullname']); ?>" />
                    </div>
                    <div><input type="tel" id="phone" name="phone" placeholder="Số điện thoại" required
                            value="<?php echo htmlspecialchars($reorder_data['phone'] ?? $user_info['phone']); ?>" />
                    </div>
                </div>
            </div>
            <div class="form-section">
                <h4><i class="icon">👤</i> Thông tin người nhận</h4>
                <div class="form-group">
                    <div><input type="text" name="receiver_name" placeholder="Tên người nhận" required
                            value="<?php echo htmlspecialchars($reorder_data['receiver_name'] ?? ''); ?>" /></div>
                    <div><input type="tel" name="receiver_phone" placeholder="SĐT người nhận" required
                            value="<?php echo htmlspecialchars($reorder_data['receiver_phone'] ?? ''); ?>" /></div>
                </div>
            </div>
            <div class="form-section">
                <h4><i class="icon">📍</i> Địa chỉ giao nhận</h4>
                <div class="form-group"><input type="text" id="pickup-addr" name="pickup" placeholder="Địa chỉ lấy hàng"
                        required value="<?php echo htmlspecialchars($reorder_data['pickup_address'] ?? ''); ?>" /></div>
                <div class="form-group"><input type="text" id="delivery-addr" name="delivery"
                        placeholder="Địa chỉ giao hàng" required
                        value="<?php echo htmlspecialchars($reorder_data['delivery_address'] ?? ''); ?>" /></div>
            </div>
            <div class="form-section">
                <h4><i class="icon">📦</i> Thông tin hàng hóa</h4>
                <div class="form-row">
                    <div>
                        <select id="package-type" name="package_type">
                            <option value="document"
                                <?php if(($reorder_data['package_type']??'')=='document') echo 'selected'; ?>>Tài
                                liệu/Hồ sơ</option>
                            <option value="food"
                                <?php if(($reorder_data['package_type']??'')=='food') echo 'selected'; ?>>Đồ ăn/Thức
                                uống</option>
                            <option value="clothes"
                                <?php if(($reorder_data['package_type']??'')=='clothes') echo 'selected'; ?>>Quần áo/Mỹ
                                phẩm</option>
                            <option value="electronic"
                                <?php if(($reorder_data['package_type']??'')=='electronic') echo 'selected'; ?>>Đồ điện
                                tử</option>
                            <option value="other"
                                <?php if(($reorder_data['package_type']??'')=='other') echo 'selected'; ?>>Khác...
                            </option>
                        </select>
                    </div>
                    <div><input type="number" id="weight" name="weight" placeholder="Khối lượng (kg)"
                            value="<?php echo htmlspecialchars($reorder_data['weight'] ?? ''); ?>" /></div>
                </div>
                <div class="form-group">
                    <input type="number" name="cod_amount" placeholder="Tiền thu hộ (VNĐ) - Nếu có"
                        value="<?php echo htmlspecialchars($reorder_data['cod_amount'] ?? ''); ?>" />
                </div>

                <!-- Thêm chọn dịch vụ để tính giá -->
                <div class="form-group">
                    <select id="order-service-type" name="service_type">
                        <?php foreach($services_list as $svc): ?>
                        <option value="<?php echo $svc['type_key']; ?>"
                            <?php if(($reorder_data['service_type']??'') == $svc['type_key']) echo 'selected'; ?>>
                            <?php echo $svc['name']; ?>
                            (<?php echo ($svc['base_price'] > 0) ? number_format($svc['base_price']).'đ' : 'Liên hệ'; ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <textarea id="note" name="note"
                    placeholder="Ghi chú cho shipper..."><?php echo htmlspecialchars($reorder_data['note'] ?? ''); ?></textarea>
            </div>

            <!-- Hiển thị giá tạm tính -->
            <div id="price-preview"
                style="margin-bottom: 15px; padding: 10px; background: #e8f4f8; border-radius: 8px; color: #0a2a66; font-weight: bold; display: none;">
                💰 Phí ship dự kiến: <span id="shipping-fee-display">0</span>đ
            </div>
            <input type="hidden" name="shipping_fee" id="shipping-fee-input" value="0">

            <button type="submit" class="btn-primary">Xác nhận đặt đơn</button>
            <div id="form-message"></div>
        </form>
    </section>

    <!-- FAQ -->
    <section id="faq">
        <h2 class="section-title">FAQs / Hỗ trợ</h2>
        <div class="faq-list">
            <div class="faq-item">
                <h3 class="faq-question">FastGo giao hàng trong bao lâu?</h3>
                <p class="faq-answer">Thời gian giao hàng nội thành: 30–60 phút, liên tỉnh: 1–3 ngày.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-question">Có thể hủy hoặc thay đổi đơn không?</h3>
                <p class="faq-answer">Vui lòng liên hệ hotline trước khi đơn được shipper nhận.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-question">FastGo có thu hộ COD không?</h3>
                <p class="faq-answer">Có, chúng tôi hỗ trợ dịch vụ thu hộ tiền mặt minh bạch.</p>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>

    <!-- AUTH MODAL (Popup Đăng nhập) -->
    <div id="auth-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>

            <!-- VIEW 1: ĐĂNG NHẬP -->
            <div id="login-view">
                <h2 style="text-align:center; color:#0a2a66; margin-bottom:20px;">Bạn cần đăng nhập</h2>
                <p style="text-align:center; margin-bottom:20px; color:#666;">Vui lòng đăng nhập để hoàn tất đơn hàng.
                </p>

                <form id="ajax-login-form">
                    <div class="form-group">
                        <input type="text" name="username" placeholder="Tên đăng nhập" required
                            style="width:100%; padding:12px; margin-bottom:10px; border:1px solid #ccc; border-radius:6px;">
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Mật khẩu" required
                            style="width:100%; padding:12px; margin-bottom:10px; border:1px solid #ccc; border-radius:6px;">
                    </div>
                    <div style="text-align:right; margin-bottom:15px;">
                        <a href="#" id="show-forgot-btn" style="color:#666; font-size:13px; text-decoration:none;">Quên
                            mật khẩu?</a>
                    </div>
                    <div id="login-error" style="color:red; text-align:center; margin-bottom:10px; display:none;"></div>
                    <button type="submit" class="btn-primary" style="width:100%;">Đăng Nhập & Gửi Đơn</button>
                </form>

                <div style="text-align:center; margin-top:15px; font-size:14px;">
                    Chưa có tài khoản? <a href="#" id="show-register-btn" style="color:#ff7a00; font-weight:bold;">Đăng
                        ký ngay</a>
                </div>
            </div>

            <!-- VIEW 2: ĐĂNG KÝ (Mặc định ẩn) -->
            <div id="register-view" style="display:none;">
                <h2 style="text-align:center; color:#0a2a66; margin-bottom:20px;">Đăng Ký Nhanh</h2>

                <form id="ajax-register-form">
                    <div class="form-group"><input type="text" name="username" placeholder="Tên đăng nhập" required
                            style="width:100%; padding:10px; margin-bottom:8px; border:1px solid #ccc; border-radius:6px;">
                    </div>
                    <div class="form-group"><input type="text" name="fullname" placeholder="Họ và tên" required
                            style="width:100%; padding:10px; margin-bottom:8px; border:1px solid #ccc; border-radius:6px;">
                    </div>
                    <div class="form-group"><input type="email" name="email" placeholder="Email" required
                            style="width:100%; padding:10px; margin-bottom:8px; border:1px solid #ccc; border-radius:6px;">
                    </div>
                    <div class="form-group"><input type="tel" name="phone" placeholder="Số điện thoại" required
                            style="width:100%; padding:10px; margin-bottom:8px; border:1px solid #ccc; border-radius:6px;">
                    </div>
                    <div class="form-group" style="display:flex; gap:10px;">
                        <input type="password" name="password" placeholder="Mật khẩu" required
                            style="width:100%; padding:10px; margin-bottom:8px; border:1px solid #ccc; border-radius:6px;">
                        <input type="password" name="confirm_password" placeholder="Nhập lại MK" required
                            style="width:100%; padding:10px; margin-bottom:8px; border:1px solid #ccc; border-radius:6px;">
                    </div>

                    <div id="register-error" style="color:red; text-align:center; margin-bottom:10px; display:none;">
                    </div>
                    <button type="submit" class="btn-primary" style="width:100%;">Đăng Ký & Gửi Đơn</button>
                </form>

                <div style="text-align:center; margin-top:15px; font-size:14px;">
                    Đã có tài khoản? <a href="#" id="show-login-btn" style="color:#ff7a00; font-weight:bold;">Đăng
                        nhập</a>
                </div>
            </div>

            <!-- VIEW 3: QUÊN MẬT KHẨU (Mặc định ẩn) -->
            <div id="forgot-view" style="display:none;">
                <h2 style="text-align:center; color:#0a2a66; margin-bottom:20px;">Khôi phục mật khẩu</h2>
                <p style="text-align:center; margin-bottom:20px; color:#666;">Nhập email đã đăng ký để nhận hướng dẫn.
                </p>

                <form id="ajax-forgot-form">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Nhập email của bạn" required
                            style="width:100%; padding:12px; margin-bottom:10px; border:1px solid #ccc; border-radius:6px;">
                    </div>
                    <div id="forgot-message" style="text-align:center; margin-bottom:10px; display:none;"></div>
                    <button type="submit" class="btn-primary" style="width:100%;">Gửi yêu cầu</button>
                </form>

                <div style="text-align:center; margin-top:15px; font-size:14px;">
                    <a href="#" id="back-to-login-btn" style="color:#0a2a66; font-weight:bold;">← Quay lại đăng nhập</a>
                </div>
            </div>

        </div>
    </div>

    <!-- Biến JS để kiểm tra trạng thái login -->
    <script>
    window.isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    window.servicesData =
        <?php echo json_encode($services_list, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    window.pricingConfig =
        <?php echo json_encode($pricing_config, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    </script>
    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>

</html>