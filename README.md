# 🚚 FastGo - Nền Tảng Giao Hàng Nhanh Chóng

> Giải pháp dịch vụ giao hàng hiện đại với giao diện thân thiện, responsive design, và tính năng theo dõi đơn hàng toàn diện.

## 📋 Mục Lục

- [Giới Thiệu](#-giới-thiệu)
- [Cấu Trúc Thư Mục](#-cấu-trúc-thư-mục)
- [Tính Năng](#-tính-năng)
- [Công Nghệ Sử Dụng](#-công-nghệ-sử-dụng)
- [Responsive Design](#-responsive-design)
- [Hướng Dẫn Sử Dụng](#-hướng-dẫn-sử-dụng)
- [Liên Hệ](#-liên-hệ)

---

## 💼 Giới Thiệu

**FastGo** là nền tảng web dịch vụ giao hàng toàn diện, cung cấp các giải pháp vận chuyển linh hoạt cho cá nhân và doanh nghiệp ở TP. Hồ Chí Minh.

### ✨ Những Ưu Điểm Nổi Bật

✅ **Responsive Design** - Hoạt động trơn tru trên desktop, tablet và mobile  
✅ **Hamburger Menu** - Menu gọn gàng trên điện thoại với animation  
✅ **Tính Cước Nhanh** - Tính giá vận chuyển tức thì theo vùng và dịch vụ  
✅ **Tra Cứu Đơn Hàng** - Hỗ trợ 3 loại đơn (tiêu chuẩn, lớn, COD)  
✅ **Form Liên Hệ** - Xác thực dữ liệu số điện thoại 10 chữ số  
✅ **FAQ Tương Tác** - Accordion FAQ mở/đóng mượt mà  
✅ **Tối Ưu Mobile** - Không có horizontal scroll, font size phù hợp

---

## 📁 Cấu Trúc Thư Mục

```
Web shipper/
├── 📄 index.html              # Trang chủ chính
├── 📄 tracking.html           # Trang tra cứu đơn hàng
├── 📄 README.md               # Tài liệu này
├── 📁 assets/
│   ├── 📁 css/
│   │   └── styles.css         # Stylesheet (1699 dòng)
│   │                          # - Desktop: 40px padding
│   │                          # - Tablet (768px): 20px padding, menu absolute
│   │                          # - Mobile (480px): hamburger menu, responsive grid
│   ├── 📁 images/             # Thư mục hình ảnh
│   └── 📁 js/
│       └── main.js            # JavaScript interactivity (316 dòng)
```

---

## 🛠 Tính Năng

### 📱 Trang Chủ (index.html)

- **Hero Section**: Slogan "Giao hàng nhanh – An toàn – Đúng giờ"
- **Services Section**: 6 loại dịch vụ chính
  - Giao nội thành (30-60 phút)
  - Giao hỏa tốc (ưu tiên)
  - Giao COD (thu hộ tiền)
  - Giao số lượng lớn (doanh nghiệp)
  - Dịch vụ doanh nghiệp
  - Chuyển nhà / Vận chuyển lớn

- **Features Section**: 4 ưu điểm nổi bật
  - 🚀 Nhanh & đúng giờ
  - 📦 Theo dõi đơn hàng
  - 👨‍✈️ Shipper chuyên nghiệp
  - ☎️ Hỗ trợ 24/7

- **Pricing Section**: Bảng giá tham khảo

- **Quick Quote Section**: ✨ **TÍNH CƯỚC NHANH** (Hoạt động đầy đủ)
  - Nhập điểm đi, điểm đến
  - Chọn loại dịch vụ (Giao tiêu chuẩn/Hỏa tốc/Số lượng lớn)
  - Lựa chọn COD (thêm 5k)
  - Hiển thị giá tức thì với chi tiết phí
  - **Tính giá theo vùng:**
    - Nội thành: 30k (giao tiêu chuẩn) / 50k (hỏa tốc)
    - Ngoại thành: +10k phụ phí
    - COD: +5k

- **Contact Form**: Form liên hệ với xác thực
  - Kiểm tra họ tên không rỗng
  - Kiểm tra số điện thoại 10 chữ số
  - Alert thông báo success

- **FAQ Section**: 4 câu hỏi thường gặp với accordion

- **Footer**: Thông tin liên hệ (Email, Hotline, Địa chỉ)

### 📍 Trang Tracking (tracking.html)

- Tra cứu 3 loại đơn hàng:
  - Đơn hàng tiêu chuẩn
  - Đơn số lượng lớn
  - Đơn COD
- Database giả lập với dữ liệu tracking
- Hiển thị status đẹp mắt với icon và màu sắc

---

## 🎨 Công Nghệ Sử Dụng

### Frontend

- **HTML5** - Semantic markup
- **CSS3** - Flexbox, Grid, Media Queries
- **JavaScript (Vanilla)** - Không dùng framework
- **Font**: Poppins (Google Fonts)

### Responsive Breakpoints

```
📱 Mobile: ≤ 480px
💻 Tablet: 768px
🖥️ Desktop: ≥ 1024px
```

---

## 📱 Responsive Design

### ✅ Desktop (≥1024px)

- Menu ngang (flex)
- Services grid 3-6 cột
- Full width content

### ✅ Tablet (768px)

- Menu absolute (position: absolute, top: 100%)
- Service grid 2-3 cột
- Padding giảm (20px)

### ✅ Mobile (≤480px)

- **Hamburger Menu** (3 dòng animation)
  - Click để toggle
  - Click link để đóng
  - Click outside để đóng dropdown
- **Layout Stack**:
  - Services: 1 cột (xếp dọc)
  - Features: 1 cột
  - Form: 100% width
- **Typography**:
  - Hero h2: 22px (từ 42px)
  - Section title: 22px (từ 36px)
  - No horizontal scroll
- **Optimization**:
  - Touch-friendly buttons (12-15px padding)
  - Max-width: 100%
  - Proper spacing

---

## 🎯 Tính Năng JavaScript

### 1. **Hamburger Menu Toggle**

```javascript
- Click nút 3 gạch → toggle class 'active'
- Animation: dòng 1 & 3 quay 45°, dòng 2 biến mất
- Auto close khi click link
```

### 2. **Dropdown Menu Mobile**

```javascript
- On click (mobile ≤480px): toggle dropdown
- Prevents default link behavior
- Close others khi mở dropdown
- Click outside → close
```

### 3. **Contact Form Validation**

```javascript
- Kiểm tra họ tên: not empty
- Kiểm tra phone: exactly 10 digits
- Show success alert
- Reset form
```

### 4. **FAQ Accordion**

```javascript
- Click question → toggle answer
- Close other answers
- Smooth toggle
```

### 5. **Quick Quote Calculator** ⭐

```javascript
- Validate locations (trong danh sách 25 quận/huyện)
- Check from !== to
- Calculate price based on:
  * Service type (standard/express/bulk)
  * Zone (inner/outer TP.HCM)
  * COD fee
- Display formatted price
- Scroll to result
```

### 6. **Order Tracking**

```javascript
- Search by order code
- Mock database with 3 examples:
  * FAST-STD, FAST-BULK, FAST-COD
- Show status with emoji & color
```

---

## 🚀 Hướng Dẫn Sử Dụng

### Chạy Website Locally

```bash
# Mở file index.html trong browser
# hoặc sử dụng Live Server:
# VS Code: Chuột phải > Open with Live Server

# URL:
http://localhost:5500
```

### Thử Tính Cước

```
1. Scroll đến "Tính giá cước nhanh"
2. Nhập: "Quận 1" → "Quận 2"
3. Chọn: "Giao tiêu chuẩn (30k)"
4. Chọn: "Có thu hộ COD (+5k)" (tuỳ chọn)
5. Click: "Tính giá ngay"
6. Xem kết quả: 30k + 0k (nội) + 5k (COD) = 35k
```

### Thử Tracking

```
1. Vào trang "Tracking" (menu hoặc tracking.html)
2. Nhập mã: "FAST-STD", "FAST-BULK", hoặc "FAST-COD"
3. Xem kết quả tracking
```

### Thử Mobile

```
1. Mở DevTools (F12)
2. Toggle Device Toolbar (Ctrl+Shift+M)
3. Chọn kích thước mobile (iPhone, Samsung, v.v.)
4. Kiểm tra:
   - Hamburger menu hoạt động?
   - Không có horizontal scroll?
   - Font size phù hợp?
   - Form có responsive?
```

---

## 🌐 Tối Ưu Performance

- ✅ Vanilla JS (không framework → load nhanh)
- ✅ CSS Flexbox/Grid (layout efficient)
- ✅ Mobile-first media queries
- ✅ Touch-friendly interface
- ✅ Smooth animations
- ✅ Form validation client-side
- ✅ No external dependencies (except Google Fonts)

---

## 📝 CSS Media Queries Summary

| Breakpoint | Platform | Features                    |
| ---------- | -------- | --------------------------- |
| ≥1024px    | Desktop  | Menu flex, full layout      |
| 768px      | Tablet   | Menu absolute, 2-3 col grid |
| ≤480px     | Mobile   | Hamburger menu, 1 col stack |

---

## 🔍 Tính Năng Chi Tiết Tính Cước

### Districts Database

```javascript
Inner TP: Quận 1,3,4,5,6,10,11 + Phú Nhuận, Bình Thạnh, Gò Vấp, Tân Bình, Tân Phú
Outer TP: Quận 2,7,8,9,12 + Thủ Đức, Bình Tân, Hóc Môn, Bình Chánh, Nhà Bè, v.v.
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

**Cập nhật lần cuối:** 17/01/2026  
**Phiên bản:** 1.0  
**Trạng thái:** ✅ Hoàn thành - Responsive

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

## 🧪 Hướng Dẫn Kiểm Tra (Test Cases)

### 1. **Kiểm Tra Trang Chủ**

| Test Case            | Cách Thực Hiện                                     | Kết Quả Mong Đợi                      |
| -------------------- | -------------------------------------------------- | ------------------------------------- |
| **Menu responsive**  | Mở index.html trên điện thoại, kích menu hamburger | Menu hiện/ẩn mượt mà                  |
| **Dropdown dịch vụ** | Hover chuột lên "Dịch vụ"                          | Danh sách con hiện ra                 |
| **Cuộn trang**       | Cuộn xuống trang                                   | Header sticky, nav menu luôn hiển thị |
| **Hero section**     | Xem phần đầu trang                                 | Ảnh nền, text nổi bật, button CTA rõ  |

### 2. **Kiểm Tra Form Liên Hệ**

| Test Case              | Dữ Liệu Nhập                                      | Kết Quả Mong Đợi                                    |
| ---------------------- | ------------------------------------------------- | --------------------------------------------------- |
| **Họ tên rỗng**        | Để trống họ tên, submit                           | Cảnh báo: "❌ Vui lòng nhập họ tên"                 |
| **Số điện thoại rỗng** | Nhập tên, để trống SĐT, submit                    | Cảnh báo: "❌ Vui lòng nhập số điện thoại"          |
| **SĐT sai định dạng**  | Nhập 123456, submit                               | Cảnh báo: "❌ Số điện thoại phải có đúng 10 chữ số" |
| **Form hợp lệ**        | Họ tên: "Nguyễn Văn A", SĐT: "0987654321", submit | Hiển thị ✅ Thông báo thành công, form reset        |

### 3. **Kiểm Tra FAQ Accordion**

| Test Case          | Cách Thực Hiện                      | Kết Quả Mong Đợi              |
| ------------------ | ----------------------------------- | ----------------------------- |
| **Mở câu hỏi**     | Kích câu hỏi FAQ                    | Câu trả lời xuất hiện mượt mà |
| **Đóng câu hỏi**   | Kích lại câu hỏi đang mở            | Câu trả lời ẩn đi             |
| **Switch câu hỏi** | Mở câu hỏi 1, sau đó kích câu hỏi 2 | Câu hỏi 1 đóng, câu hỏi 2 mở  |

### 4. **Kiểm Tra Tracking Page**

| Test Case             | Cách Thực Hiện                     | Kết Quả Mong Đợi            |
| --------------------- | ---------------------------------- | --------------------------- |
| **Navigate từ menu**  | Kích "Tracking" từ menu index.html | Chuyển đến tracking.html    |
| **Dropdown tracking** | Hover chuột lên "Tracking"         | Hiển thị 3 tùy chọn tra cứu |
| **Tracking form**     | Nhập mã đơn hàng, submit           | Hiển thị thông tin đơn hàng |

### 5. **Kiểm Tra Responsive**

| Thiết Bị    | Độ Rộng       | Kiểm Tra                        |
| ----------- | ------------- | ------------------------------- |
| **Mobile**  | ≤480px        | Menu collapsed, text responsive |
| **Tablet**  | 481px - 768px | Layout 2-3 cột, menu hiển thị   |
| **Desktop** | ≥769px        | Layout đầy đủ, header sticky    |

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
