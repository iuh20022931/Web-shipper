// ===== HAMBURGER MENU TOGGLE =====
const hamburgerBtn = document.getElementById("hamburger-btn");
const navMenu = document.getElementById("nav-menu");

if (hamburgerBtn && navMenu) {
  hamburgerBtn.addEventListener("click", function () {
    hamburgerBtn.classList.toggle("active");
    navMenu.classList.toggle("active");
  });

  // Close menu when clicking on a link
  document.querySelectorAll(".nav-menu a").forEach((link) => {
    link.addEventListener("click", function () {
      hamburgerBtn.classList.remove("active");
      navMenu.classList.remove("active");
    });
  });
}

// ===== CONTACT FORM SUBMIT =====
const form = document.getElementById("contact-form");

form.addEventListener("submit", function (e) {
  e.preventDefault();

  const name = document.getElementById("name").value.trim();
  const phone = document.getElementById("phone").value.trim();

  // Kiểm tra họ tên không để trống
  if (name === "") {
    alert("❌ Vui lòng nhập họ tên.");
    return;
  }

  // Kiểm tra số điện thoại không để trống
  if (phone === "") {
    alert("❌ Vui lòng nhập số điện thoại.");
    return;
  }

  // Kiểm tra số điện thoại có đúng 10 số không (loại bỏ ký tự không phải số)
  const phoneDigitsOnly = phone.replace(/\D/g, "");
  if (phoneDigitsOnly.length !== 10) {
    alert("❌ Số điện thoại phải có đúng 10 chữ số.");
    return;
  }

  // Nếu hợp lệ, hiển thị thông báo đẹp mắt
  alert(
    `✅ Cảm ơn ${name}, FastGo đã nhận yêu cầu của bạn!\n\nChúng tôi sẽ liên hệ bạn sớm nhất.`,
  );

  // Xóa trắng các ô nhập liệu
  form.reset();
});
// FAQ Accordion
document.querySelectorAll(".faq-question").forEach((q) => {
  q.addEventListener("click", () => {
    const ans = q.nextElementSibling;
    const isVisible = ans.style.display === "block";
    document
      .querySelectorAll(".faq-answer")
      .forEach((a) => (a.style.display = "none"));
    ans.style.display = isVisible ? "none" : "block";
  });
});
// Tracking Functionality
function trackOrder(event, type) {
  event.preventDefault();
  let code = "";
  let resultDiv = null;

  if (type === "standard") {
    code = document.getElementById("standard-code").value.trim().toUpperCase();
    resultDiv = document.getElementById("result-standard");
  } else if (type === "bulk") {
    code = document.getElementById("bulk-code").value.trim().toUpperCase();
    resultDiv = document.getElementById("result-bulk");
  } else if (type === "cod") {
    code = document.getElementById("cod-code").value.trim().toUpperCase();
    resultDiv = document.getElementById("result-cod");
  }

  if (!code) {
    resultDiv.innerHTML =
      '<p style="color: #d9534f;"><strong>❌ Lỗi:</strong> Vui lòng nhập mã đơn hàng!</p>';
    return;
  }

  // Database giả lập cho tracking
  const trackingDatabase = {
    "FAST-STD": {
      type: "Đơn hàng tiêu chuẩn",
      status: "Đang xử lý",
      icon: "⏳",
      color: "#ff7a00",
    },
    "FAST-BULK": {
      type: "Đơn hàng số lượng lớn",
      status: "Đang giao",
      icon: "🚚",
      color: "#0a2a66",
    },
    "FAST-COD": {
      type: "Đơn hàng COD",
      status: "Hoàn tất",
      icon: "✅",
      color: "#27ae60",
    },
  };

  // Kiểm tra mã đơn hàng
  if (trackingDatabase[code]) {
    const order = trackingDatabase[code];
    resultDiv.innerHTML = `
      <div style="background-color: #e8f4f8; border-left: 4px solid ${order.color}; padding: 20px; border-radius: 8px;">
        <p><strong>Mã đơn:</strong> ${code}</p>
        <p><strong>Loại:</strong> ${order.type}</p>
        <p style="font-size: 18px; color: ${order.color}; margin-top: 12px;">
          <strong>${order.icon} Trạng thái: ${order.status}</strong>
        </p>
      </div>
    `;
  } else {
    // Mã không tìm thấy
    resultDiv.innerHTML = `
      <div style="background-color: #f8e8e8; border-left: 4px solid #d9534f; padding: 20px; border-radius: 8px;">
        <p style="color: #d9534f;"><strong>❌ Lỗi:</strong> Không tìm thấy đơn hàng với mã <strong>${code}</strong></p>
        <p style="color: #999; font-size: 14px; margin-top: 8px;">Vui lòng kiểm tra lại mã đơn hàng.</p>
      </div>
    `;
  }
}

// ===== QUICK QUOTE FORM =====
// Mảng danh sách các quận hợp lệ của TP.HCM
const validDistricts = [
  "Quận 1",
  "Quận 2",
  "Quận 3",
  "Quận 4",
  "Quận 5",
  "Quận 6",
  "Quận 7",
  "Quận 8",
  "Quận 9",
  "Quận 10",
  "Quận 11",
  "Quận 12",
  "Bình Thạnh",
  "Bình Tân",
  "Gò Vấp",
  "Phú Nhuận",
  "Tân Bình",
  "Tân Phú",
  "Thủ Đức",
  "Hóc Môn",
  "Cần Thơ",
  "Huyện Bình Chánh",
  "Huyện Cần Giờ",
  "Huyện Nhà Bè",
];

const quickQuoteForm = document.getElementById("quick-quote-form");

if (quickQuoteForm) {
  quickQuoteForm.addEventListener("submit", function (e) {
    e.preventDefault();

    // Lấy giá trị từ form
    const fromLocation = document.getElementById("from-location").value.trim();
    const toLocation = document.getElementById("to-location").value.trim();
    const serviceType = document.getElementById("service-type").value;
    const resultDiv = document.getElementById("quote-result");

    // Kiểm tra dữ liệu
    if (!fromLocation || !toLocation || !serviceType) {
      resultDiv.innerHTML =
        '<p style="color: #d9534f;"><strong>❌ Lỗi:</strong> Vui lòng nhập đầy đủ thông tin!</p>';
      resultDiv.classList.add("show");
      return;
    }

    // Kiểm tra điểm đi và điểm đến có giống nhau không
    if (fromLocation.toLowerCase() === toLocation.toLowerCase()) {
      resultDiv.innerHTML =
        '<p style="color: #d9534f;"><strong>❌ Lỗi:</strong> Điểm đi và điểm đến không thể giống nhau!</p>';
      resultDiv.classList.add("show");
      return;
    }

    // Kiểm tra xem địa chỉ có hợp lệ không (phải nằm trong mảng validDistricts)
    const isFromValid = validDistricts.some(
      (district) => district.toLowerCase() === fromLocation.toLowerCase(),
    );
    const isToValid = validDistricts.some(
      (district) => district.toLowerCase() === toLocation.toLowerCase(),
    );

    if (!isFromValid || !isToValid) {
      resultDiv.innerHTML =
        '<p style="color: #d9534f;"><strong>❌ Lỗi:</strong> FastGo hiện chưa hỗ trợ khu vực này, vui lòng chọn quận từ danh sách gợi ý.</p>';
      resultDiv.classList.add("show");
      return;
    }

    // Tính giá tiền dựa theo loại dịch vụ
    let basePrice = 0;
    let serviceName = "";

    if (serviceType === "express") {
      basePrice = 30000;
      serviceName = "Giao nhanh";
    } else if (serviceType === "standard") {
      basePrice = 15000;
      serviceName = "Giao tiết kiệm";
    }

    // Tính phí hành chính 5% dựa trên phí cơ bản
    const adminFee = Math.round(basePrice * 0.05);
    const totalPrice = basePrice + adminFee;

    // Hiển thị kết quả
    resultDiv.innerHTML = `
      <div>
        <p><strong>📍 Từ:</strong> ${fromLocation}</p>
        <p><strong>📍 Đến:</strong> ${toLocation}</p>
        <p><strong>📦 Loại dịch vụ:</strong> ${serviceName}</p>
        <hr style="margin: 16px 0; border: none; border-top: 1px solid #e0e0e0;">
        <p><strong>💰 Báo giá:</strong></p>
        <p>Phí cơ bản: <strong>${basePrice.toLocaleString(
          "vi-VN",
        )}đ</strong></p>
        <p>Phí hành chính (5%): <strong>${adminFee.toLocaleString(
          "vi-VN",
        )}đ</strong></p>
        <p><strong>💵 Tổng cộng: ${totalPrice.toLocaleString(
          "vi-VN",
        )}đ</strong></p>
        <button class="btn-order" onclick="alert('Cảm ơn! Yêu cầu của bạn sẽ được xử lý sớm nhất.')">Đặt đơn ngay</button>
      </div>
    `;
    resultDiv.classList.add("show");
  });
}
