# 🚚 FastGo - Nền Tảng Quản Lý Dịch Vụ Giao Hàng

> Giải pháp quản lý vận chuyển hiện đại với giao diện thân thiện và tính năng theo dõi đơn hàng toàn diện.

## 📋 Mục Lục
- [Giới Thiệu](#-giới-thiệu)
- [Cấu Trúc Thư Mục](#-cấu-trúc-thư-mục)
- [Tính Năng](#-tính-năng)
- [Công Nghệ Sử Dụng](#-công-nghệ-sử-dụng)
- [Hướng Dẫn Kiểm Tra](#-hướng-dẫn-kiểm-tra)
- [Thông Tin Tác Giả](#-thông-tin-tác-giả)

---

## 💼 Giới Thiệu

**FastGo** là nền tảng web dịch vụ giao hàng toàn diện, cung cấp các giải pháp vận chuyển linh hoạt cho cá nhân và doanh nghiệp. Ứng dụng cho phép khách hàng tra cứu đơn hàng, quản lý bảng giá, và trải nghiệm dịch vụ giao hàng chất lượng cao.

### Các Lợi Thế Chính
✅ Giao diện responsive, tương thích mọi thiết bị  
✅ Tra cứu đơn hàng nhanh chóng và chính xác  
✅ Hỗ trợ đa dạng hình thức giao hàng  
✅ Form liên hệ tích hợp với xác thực dữ liệu  
✅ Accordion FAQ tương tác

---

## 📁 Cấu Trúc Thư Mục

```
Web shipper/
├── 📄 index.html              # Trang chủ - Giới thiệu dịch vụ
├── 📄 tracking.html           # Trang theo dõi đơn hàng
├── 📄 README.md               # Tài liệu hướng dẫn
├── 📁 assets/
│   ├── 📁 css/
│   │   └── styles.css         # Stylesheet toàn bộ ứng dụng
│   ├── 📁 images/             # Thư mục lưu trữ hình ảnh
│   └── 📁 js/
│       └── main.js            # JavaScript xử lý chức năng
```

---

## 🛠 Tính Năng

### ✨ Các Chức Năng Đã Hoàn Thành

#### 1. **Trang Chủ (index.html)**
- 🎯 Hero section với slogan nổi bật
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

#### 2. **Trang Theo Dõi Đơn Hàng (tracking.html)**
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

| Công Nghệ | Phiên Bản | Mục Đích |
|-----------|----------|---------|
| **HTML5** | - | Cấu trúc semantic của ứng dụng |
| **CSS3** | - | Styling responsive & hiệu ứng |
| **JavaScript (Vanilla)** | ES6+ | Xử lý logic & tương tác người dùng |
| **Responsive Design** | - | Tương thích Mobile, Tablet, Desktop |

### Các Tính Năng CSS
- 🎨 Flexbox & Grid layout
- 📱 Mobile-first responsive design
- ⚡ Smooth transitions & animations
- 🌈 Color scheme chuyên nghiệp

---

## 🧪 Hướng Dẫn Kiểm Tra (Test Cases)

### 1. **Kiểm Tra Trang Chủ**

| Test Case | Cách Thực Hiện | Kết Quả Mong Đợi |
|-----------|---------------|-----------------|
| **Menu responsive** | Mở index.html trên điện thoại, kích menu hamburger | Menu hiện/ẩn mượt mà |
| **Dropdown dịch vụ** | Hover chuột lên "Dịch vụ" | Danh sách con hiện ra |
| **Cuộn trang** | Cuộn xuống trang | Header sticky, nav menu luôn hiển thị |
| **Hero section** | Xem phần đầu trang | Ảnh nền, text nổi bật, button CTA rõ |

### 2. **Kiểm Tra Form Liên Hệ**

| Test Case | Dữ Liệu Nhập | Kết Quả Mong Đợi |
|-----------|-------------|-----------------|
| **Họ tên rỗng** | Để trống họ tên, submit | Cảnh báo: "❌ Vui lòng nhập họ tên" |
| **Số điện thoại rỗng** | Nhập tên, để trống SĐT, submit | Cảnh báo: "❌ Vui lòng nhập số điện thoại" |
| **SĐT sai định dạng** | Nhập 123456, submit | Cảnh báo: "❌ Số điện thoại phải có đúng 10 chữ số" |
| **Form hợp lệ** | Họ tên: "Nguyễn Văn A", SĐT: "0987654321", submit | Hiển thị ✅ Thông báo thành công, form reset |

### 3. **Kiểm Tra FAQ Accordion**

| Test Case | Cách Thực Hiện | Kết Quả Mong Đợi |
|-----------|---------------|-----------------|
| **Mở câu hỏi** | Kích câu hỏi FAQ | Câu trả lời xuất hiện mượt mà |
| **Đóng câu hỏi** | Kích lại câu hỏi đang mở | Câu trả lời ẩn đi |
| **Switch câu hỏi** | Mở câu hỏi 1, sau đó kích câu hỏi 2 | Câu hỏi 1 đóng, câu hỏi 2 mở |

### 4. **Kiểm Tra Tracking Page**

| Test Case | Cách Thực Hiện | Kết Quả Mong Đợi |
|-----------|---------------|-----------------|
| **Navigate từ menu** | Kích "Tracking" từ menu index.html | Chuyển đến tracking.html |
| **Dropdown tracking** | Hover chuột lên "Tracking" | Hiển thị 3 tùy chọn tra cứu |
| **Tracking form** | Nhập mã đơn hàng, submit | Hiển thị thông tin đơn hàng |

### 5. **Kiểm Tra Responsive**

| Thiết Bị | Độ Rộng | Kiểm Tra |
|---------|--------|---------|
| **Mobile** | ≤480px | Menu collapsed, text responsive |
| **Tablet** | 481px - 768px | Layout 2-3 cột, menu hiển thị |
| **Desktop** | ≥769px | Layout đầy đủ, header sticky |

---

## 👤 Thông Tin Tác Giả

**Tên dự án:** FastGo - Nền tảng giao hàng  
**Phiên bản:** 1.0.0  
**Ngày tạo:** 2026  
**Mục đích:** Dự án thực tập lập trình web

### Chú ý
Đây là dự án web tĩnh sử dụng HTML5, CSS3 và Vanilla JavaScript. Phù hợp cho việc học tập và triển khai các tính năng cơ bản của ứng dụng web.

---

<div align="center">

**Cảm ơn bạn đã sử dụng FastGo! 🚀**

Để biết thêm thông tin, vui lòng liên hệ qua form trên trang chủ.

</div>
