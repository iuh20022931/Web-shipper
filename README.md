# 🚚 FastGo - Hệ Thống Quản Lý Vận Chuyển & Giao Hàng

> **FastGo** là một nền tảng web quản lý dịch vụ giao hàng (Logistics/Shipper) toàn diện, được xây dựng bằng **PHP thuần** và **MySQL**. Hệ thống mô phỏng quy trình vận hành thực tế của một công ty vận chuyển: từ lúc khách đặt đơn, admin điều phối, tài xế (shipper) đi giao, đến khi hoàn tất và báo cáo doanh thu.

---

## 📋 Mục Lục

1. [Tổng Quan Dự Án](#-tổng-quan-dự-án)
2. [Tính Năng Chi Tiết](#-tính-năng-chi-tiết)
3. [Công Nghệ Sử Dụng](#-công-nghệ-sử-dụng)
4. [Cơ Sở Dữ Liệu](#-cơ-sở-dữ-liệu)
5. [Hướng Dẫn Cài Đặt](#-hướng-dẫn-cài-đặt)
6. [Cấu Trúc Thư Mục](#-cấu-trúc-thư-mục)
7. [Thông Tin Tác Giả](#-thông-tin-tác-giả)

---

## 🌟 Tổng Quan Dự Án

Hệ thống được thiết kế để giải quyết các bài toán cốt lõi trong vận hành giao nhận:

- **Tự động hóa quy trình:** Khách đặt đơn -> Hệ thống ghi nhận -> Admin phân công -> Shipper nhận việc -> Giao hàng & Chụp ảnh bằng chứng (POD).
- **Minh bạch thông tin:** Khách hàng có thể theo dõi hành trình đơn hàng (Tracking Timeline) chi tiết từng phút.
- **Quản lý tập trung:** Admin nắm toàn bộ số liệu, doanh thu, hiệu suất nhân viên qua Dashboard trực quan.

### ✨ Điểm Nổi Bật

- ✅ **Timeline Tracking:** Theo dõi trạng thái đơn hàng dạng dòng thời gian dọc (Vertical Timeline) hiện đại.
- ✅ **Proof of Delivery (POD):** Shipper bắt buộc phải tải lên ảnh chụp khi hoàn tất đơn hàng.
- ✅ **Tính giá tự động:** Hệ thống tự tính phí ship dựa trên khoảng cách (Nội/Ngoại thành), khối lượng và phí COD.
- ✅ **AJAX Experience:** Đăng nhập, Đăng ký, Tra cứu đơn hàng, Tính giá... đều xử lý không cần tải lại trang.
- ✅ **Responsive Design:** Giao diện tối ưu hoàn toàn cho Mobile (đặc biệt là giao diện Shipper).

---

## 🚀 Tính Năng Chi Tiết

Hệ thống phân chia thành 4 nhóm người dùng chính:

### 1. Khách Vãng Lai (Guest)

- **Trang chủ (Landing Page):** Giới thiệu dịch vụ, bảng giá, quy trình làm việc.
- **Tính giá cước nhanh (Quick Quote):** Công cụ ước tính phí vận chuyển dựa trên điểm đi/đến và loại dịch vụ (AJAX).
- **Tra cứu đơn hàng (Tracking):** Xem trạng thái đơn hàng bằng mã vận đơn mà không cần đăng nhập.
- **Đăng ký / Đăng nhập:** Hệ thống xác thực bảo mật (Popup Modal AJAX).

### 2. Khách Hàng (Customer)

- **Đặt hàng trực tuyến:** Form đặt hàng chi tiết, tự động điền thông tin cá nhân, hỗ trợ chọn dịch vụ (Tiêu chuẩn, Hỏa tốc, COD...).
- **Quản lý đơn hàng:**
  - Xem lịch sử đơn hàng đã đặt.
  - Bộ lọc tìm kiếm theo trạng thái, ngày tháng.
  - **Đặt lại (Re-order):** Tạo đơn mới nhanh chóng từ thông tin đơn cũ.
- **Chi tiết đơn hàng:**
  - Xem **Timeline hành trình** chi tiết (Ngày giờ, trạng thái, ghi chú).
  - Xem ảnh bằng chứng giao hàng (POD) khi đơn hoàn tất.
  - **In hóa đơn:** Xuất phiếu gửi hàng để dán lên kiện hàng.
  - **Đánh giá & Phản hồi:** Chấm điểm sao và gửi nhận xét về dịch vụ.
- **Hồ sơ cá nhân:** Cập nhật thông tin, đổi mật khẩu.

### 3. Tài Xế (Shipper)

- **Dashboard riêng biệt:**
  - Xem danh sách đơn hàng được Admin phân công.
  - Bộ lọc trạng thái: Chờ lấy hàng, Đang giao, Hoàn tất.
  - **Thông báo:** Nhận cảnh báo khi có đơn mới hoặc Admin thay đổi trạng thái.
- **Xử lý đơn hàng:**
  - Xem chi tiết: Địa chỉ (tích hợp link Google Maps), SĐT khách (Click-to-call).
  - **Cập nhật trạng thái:** Chuyển đổi trạng thái theo quy trình (Đã lấy -> Đang giao -> Hoàn tất/Hủy).
  - **Upload POD:** Bắt buộc chụp ảnh giao hàng thành công để hoàn tất đơn.
  - Ghi chú sự cố (Shipper Note).
- **Thống kê thu nhập:** Xem tổng số đơn đã giao, tổng thu nhập, tỷ lệ hoàn thành.

### 4. Quản Trị Viên (Admin)

- **Dashboard Thống kê (Analytics):**
  - KPIs: Tổng doanh thu, Tổng đơn hàng, Khách hàng mới.
  - Biểu đồ (Chart.js): Doanh thu 7 ngày gần nhất, Phân loại dịch vụ, Top khách hàng thân thiết.
- **Quản lý đơn hàng:**
  - Xem danh sách toàn bộ đơn hàng.
  - **Phân công Shipper:** Chỉ định tài xế cho từng đơn.
  - **Can thiệp trạng thái:** Có quyền Override (ghi đè) trạng thái đơn hàng khi cần thiết.
  - Xem Log lịch sử thay đổi của đơn hàng.
- **Quản lý người dùng:** Thêm/Sửa/Xóa/Phân quyền (Customer <-> Shipper <-> Admin).
- **Quản lý Dịch vụ:** Thêm/Sửa/Xóa các gói cước và bảng giá.

---

## Công Nghệ Sử Dụng

- **Backend:** PHP (Native - Không Framework) - Dễ dàng triển khai và tùy biến.
- **Database:** MySQL (Sử dụng Prepared Statements để bảo mật).
- **Frontend:** HTML5, CSS3 (Flexbox/Grid), JavaScript (Vanilla).
- **Thư viện:**
  - `Chart.js`: Vẽ biểu đồ thống kê.
  - `Google Fonts`: Font Poppins & Inter.
- **Kỹ thuật nổi bật:**
  - **AJAX:** Xử lý bất đồng bộ cho trải nghiệm mượt mà.
  - **Security:** Password Hashing (Bcrypt), chống SQL Injection, XSS Protection.
  - **Session Management:** Quản lý phiên đăng nhập và phân quyền.

---

## 🗄 Cơ Sở Dữ Liệu

Hệ thống sử dụng 4 bảng chính:

1.  **`users`**: Lưu thông tin người dùng (Admin, Shipper, Customer).
2.  **`orders`**: Lưu thông tin đơn hàng (Mã đơn, người gửi/nhận, trạng thái, phí ship, COD, ảnh POD...).
3.  **`services`**: Lưu cấu hình các gói dịch vụ và giá cước cơ bản.
4.  **`order_logs`**: Lưu lịch sử thay đổi trạng thái của đơn hàng (Ai đổi? Đổi khi nào? Từ trạng thái nào sang trạng thái nào?).

---

## 💻 Hướng Dẫn Cài Đặt

### Yêu cầu hệ thống

- Web Server: XAMPP, WAMP, Laragon hoặc Docker.
- PHP Version: 7.4 trở lên.
- MySQL/MariaDB.

### Các bước cài đặt

1.  **Clone dự án:**
    Tải mã nguồn về thư mục `htdocs` (XAMPP) hoặc `www` (WAMP).

2.  **Cài đặt Database:**
    - Mở phpMyAdmin (thường là `http://localhost/phpmyadmin`).
    - Tạo database mới tên: `shipper_db`.
    - Import file `database.sql` (đã đính kèm trong mã nguồn) vào database vừa tạo.

3.  **Cấu hình kết nối:**
    Mở file `config/db.php` và chỉnh sửa thông tin:

    ```php
    $host = "localhost";
    $user = "root";      // Username database
    $pass = "";          // Password database
    $db = "shipper_db";  // Tên database
    ```

4.  **Tạo tài khoản Admin:**
    - Đăng ký một tài khoản mới qua giao diện web.
    - Vào phpMyAdmin, bảng `users`, sửa cột `role` của tài khoản vừa tạo thành `admin`.

5.  **Chạy dự án:**
    Truy cập `http://localhost/Web%20shipper/` (hoặc đường dẫn tương ứng).

---

## Cấu Trúc Thư Mục

```
Web shipper/
├──  assets/                  # Tài nguyên tĩnh
│   ├── 📁 css/                 # Stylesheets (styles.css, admin.css)
│   ├── 📁 js/                  # JavaScript (main.js)
│   ├── 📁 images/              # Hình ảnh giao diện
│   └── 📁 uploads/             # Ảnh POD do shipper tải lên
├── 📁 config/                  # Cấu hình hệ thống (db.php)
├── 📁 includes/                # Các đoạn mã dùng chung (Header, Footer)
│
├── 📄 index.php                # Trang chủ (Landing Page)
├── 📄 login.php                # Trang đăng nhập
├── 📄 register.php             # Trang đăng ký
├── 📄 tracking.php             # Trang tra cứu đơn hàng (Public)
│
├── 📄 dashboard.php            # Dashboard Khách hàng
├── 📄 order.php                # Xử lý tạo đơn hàng
├── 📄 order_history.php        # Lịch sử đơn hàng
├── 📄 customer_order_detail.php # Chi tiết đơn hàng (cho Khách)
├── 📄 print_invoice.php        # Trang in hóa đơn
├── 📄 profile.php              # Hồ sơ khách hàng
│
├── 📄 shipper_dashboard.php    # Dashboard Shipper
├── 📄 shipper_order_detail.php # Chi tiết & Xử lý đơn (cho Shipper)
├── 📄 shipper_profile.php      # Hồ sơ & Thống kê Shipper
│
├── 📄 orders_manage.php        # Quản lý đơn hàng (Admin)
├── 📄 order_detail.php         # Chi tiết & Phân công đơn (Admin)
├── 📄 users_manage.php         # Quản lý người dùng (Admin)
├── 📄 services_manage.php      # Quản lý dịch vụ (Admin)
├── 📄 admin_stats.php          # Báo cáo thống kê (Admin)
├── 📄 admin_pricing_guide.php  # Hướng dẫn tính phí (Admin)
│
└── ... (các file xử lý AJAX: login_ajax.php, tracking_ajax.php...)
```

### Pricing Logic

```
Base Price:
- Standard: 30,000đ
- Express: 50,000đ

Surcharge:
- Outer district: +10,000đ
- COD fee: +5,000đ

Total = Base + Surcharge(s)
```

---

## ⚙️ Cài Đặt Mô Tả

Không cần cài đặt thêm! Chỉ cần:

1. Download/Clone project
2. Mở `index.html` trong trình duyệt
3. Tất cả tính năng hoạt động ngay

---

## 📞 Liên Hệ

**FastGo Services**

- 📧 Email: contact@fastgo.vn
- ☎️ Hotline: 0123 456 789
- 📍 Địa chỉ: TP. Hồ Chí Minh
- 🕒 Hỗ trợ: 24/7

---

## 📄 Ghi Chú

- Tất cả dữ liệu tracking & quote là **mô phỏng** (mock data)
- Trong production, cần kết nối backend API
- Form data cần gửi đến server để xử lý

---

**Cập nhật lần cuối:** 23/01/2026  
**Phiên bản:** 1.2  
**Trạng thái:** ✅ Hoàn thành - Responsive - Phân trang & Thống kê

- 📋 Menu điều hướng responsive với dropdown
- 📦 Phần giới thiệu các dịch vụ giao hàng:
  - Giao tiêu chuẩn
  - Giao hỏa tốc
  - Giao COD (thu tiền tận nơi)
  - Giao số lượng lớn
  - Dịch vụ doanh nghiệp
  - Chuyển nhà / vận chuyển lớn
- 💰 Bảng giá dịch vụ chi tiết
- 🌟 Phần "Why Us" - Những lý do chọn FastGo
- ❓ FAQ Accordion tương tác
- 📞 Form liên hệ với xác thực dữ liệu
- 📄 **Phân trang (Pagination)**: Áp dụng cho Admin, Shipper và Lịch sử đơn hàng.
- 📊 **Thống kê Shipper**: Trang hồ sơ riêng xem thu nhập và hiệu suất.
- 🔔 **Thông báo**: Cảnh báo đơn mới cho Shipper.

#### 2. **Trang Theo Dõi Đơn Hàng (tracking.php)**

- 🔍 Tra cứu đơn hàng đơn lẻ
- 📊 Tra cứu đơn số lượng lớn
- 💳 Tra cứu đơn COD
- 📍 Hiển thị trạng thái vận chuyển

#### 3. **Chức Năng JavaScript (main.js)**

- ✅ Xác thực form liên hệ (kiểm tra họ tên, số điện thoại)
- 📱 Accordion FAQ mở/đóng tương tác
- 🔎 Hệ thống tracking đơn hàng
- 📢 Thông báo user-friendly

---

## 💻 Công Nghệ Sử Dụng

| Công Nghệ                | Phiên Bản | Mục Đích                            |
| ------------------------ | --------- | ----------------------------------- |
| **HTML5**                | -         | Cấu trúc semantic của ứng dụng      |
| **CSS3**                 | -         | Styling responsive & hiệu ứng       |
| **JavaScript (Vanilla)** | ES6+      | Xử lý logic & tương tác người dùng  |
| **Responsive Design**    | -         | Tương thích Mobile, Tablet, Desktop |

### Các Tính Năng CSS

- 🎨 Flexbox & Grid layout
- 📱 Mobile-first responsive design
- ⚡ Smooth transitions & animations
- 🌈 Color scheme chuyên nghiệp

---

## 👤 Thông Tin Tác Giả

**Tên dự án:** FastGo - Nền tảng giao hàng  
**Phiên bản:** 1.2  
**Ngày tạo:** 2026  
**Mục đích:** Dự án thực tập lập trình web
