// ===== HAMBURGER MENU TOGGLE =====
const hamburgerBtn = document.getElementById("hamburger-btn");
const navMenu = document.getElementById("nav-menu");

if (hamburgerBtn && navMenu) {
  hamburgerBtn.addEventListener("click", function (e) {
    e.stopPropagation();
    hamburgerBtn.classList.toggle("active");
    navMenu.classList.toggle("active");
  });

  // ===== DROPDOWN TOGGLE (MOBILE) =====
  document.querySelectorAll(".dropdown > a").forEach((link) => {
    link.addEventListener("click", function (e) {
      if (window.innerWidth <= 480) {
        e.preventDefault(); // không nhảy link #
        e.stopPropagation();

        const dropdownMenu = this.nextElementSibling;
        if (!dropdownMenu) return;

        // Đóng tất cả dropdown khác
        document.querySelectorAll(".dropdown-menu").forEach((menu) => {
          if (menu !== dropdownMenu) {
            menu.classList.remove("active");
          }
        });

        // Toggle dropdown hiện tại
        dropdownMenu.classList.toggle("active");
      }
    });
  });

  // ===== CLOSE MENU WHEN CLICK NORMAL LINK =====
  document.querySelectorAll(".nav-menu > li > a").forEach((link) => {
    link.addEventListener("click", function () {
      if (
        window.innerWidth <= 480 &&
        !this.parentElement.classList.contains("dropdown")
      ) {
        hamburgerBtn.classList.remove("active");
        navMenu.classList.remove("active");

        // Đóng luôn dropdown nếu có
        document.querySelectorAll(".dropdown-menu").forEach((menu) => {
          menu.classList.remove("active");
        });
      }
    });
  });
}

// ===== CLICK OUTSIDE TO CLOSE DROPDOWN (MOBILE) =====
document.addEventListener("click", function (e) {
  if (window.innerWidth <= 480 && !e.target.closest(".dropdown")) {
    document.querySelectorAll(".dropdown-menu").forEach((menu) => {
      menu.classList.remove("active");
    });
  }
});

// ===== CONTACT FORM SUBMIT =====
const form = document.getElementById("contact-form");

form.addEventListener("submit", function (e) {
  e.preventDefault();

  const name = document.getElementById("name").value;
  const pickup = document.getElementById("pickup-addr").value;
  const delivery = document.getElementById("delivery-addr").value;
  const packageType =
    document.getElementById("package-type").options[
      document.getElementById("package-type").selectedIndex
    ].text;

  // Hiệu ứng gửi đơn
  const btn = form.querySelector("button");
  btn.innerText = "Đang tạo đơn hàng...";

  setTimeout(() => {
    form.innerHTML = `
            <div class="success-message">
                <div class="check-icon">✓</div>
                <h3>Đã tạo đơn thành công!</h3>
                <p>Chào <strong>${name}</strong>, đơn hàng <strong>${packageType}</strong> của bạn đang được hệ thống điều phối shipper.</p>
                <div style="text-align:left; font-size:14px; background:#fff; padding:10px; border-radius:5px;">
                    <p>🚩 <strong>Lấy tại:</strong> ${pickup}</p>
                    <p>🏁 <strong>Giao đến:</strong> ${delivery}</p>
                </div>
                <p style="margin-top:15px;">Vui lòng chuẩn bị hàng hóa, chúng tôi sẽ gọi cho bạn ngay!</p>
                <button onclick="location.reload()" class="btn-secondary">Quay lại</button>
            </div>
        `;
  }, 1200);
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
// ===== TRACKING FUNCTIONALITY (Hợp nhất với Loading & Giao diện Card) =====
function trackOrder(event, type) {
  event.preventDefault();

  // 1. Xác định các phần tử
  const spinner = document.getElementById("loading-spinner");
  let resultDiv = document.getElementById(`result-${type}`);
  let code = "";

  // 2. Lấy mã đơn hàng từ ô input tương ứng
  if (type === "standard") {
    code = document.getElementById("standard-code").value.trim().toUpperCase();
  } else if (type === "bulk") {
    code = document.getElementById("bulk-code").value.trim().toUpperCase();
  } else if (type === "cod") {
    code = document.getElementById("cod-code").value.trim().toUpperCase();
  }

  // 3. Nếu không nhập mã, báo lỗi ngay (giữ nguyên style đỏ của bạn)
  if (!code) {
    resultDiv.innerHTML = `
      <div style="background-color: #f8e8e8; border-left: 4px solid #d9534f; padding: 20px; border-radius: 8px; margin-top: 15px;">
        <p style="color: #d9534f;"><strong>❌ Lỗi:</strong> Vui lòng nhập mã đơn hàng!</p>
      </div>`;
    return;
  }

  // 4. Hiện hiệu ứng Loading và xóa kết quả cũ
  spinner.style.display = "block";
  resultDiv.innerHTML = "";

  // 5. Chờ 0.8 giây để "giả lập" quét dữ liệu, sau đó hiện kết quả bạn thích
  setTimeout(() => {
    spinner.style.display = "none"; // Tắt loading

    // Database giả lập
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

    // 6. Hiển thị kết quả theo Style bạn thích
    if (trackingDatabase[code]) {
      const order = trackingDatabase[code];
      resultDiv.innerHTML = `
        <div style="background-color: #e8f4f8; border-left: 4px solid ${order.color}; padding: 20px; border-radius: 8px; margin-top: 15px; text-align: left;">
          <p><strong>Mã đơn:</strong> ${code}</p>
          <p><strong>Loại:</strong> ${order.type}</p>
          <p style="font-size: 18px; color: ${order.color}; margin-top: 12px;">
            <strong>${order.icon} Trạng thái: ${order.status}</strong>
          </p>
        </div>
      `;
      saveToHistory(code); // Lưu vào lịch sử (nếu bạn đã thêm hàm này)
    } else {
      // Style báo lỗi khi không tìm thấy mã
      resultDiv.innerHTML = `
        <div style="background-color: #f8e8e8; border-left: 4px solid #d9534f; padding: 20px; border-radius: 8px; margin-top: 15px; text-align: left;">
          <p style="color: #d9534f;"><strong>❌ Lỗi:</strong> Không tìm thấy đơn hàng với mã <strong>${code}</strong></p>
          <p style="color: #999; font-size: 14px; margin-top: 8px;">Vui lòng kiểm tra lại mã đơn hàng.</p>
        </div>
      `;
    }
  }, 800);
}
// Lưu mã vào lịch sử khi bấm Kiểm tra
function saveToHistory(code) {
  let history = JSON.parse(localStorage.getItem("trackingHistory")) || [];
  if (!history.includes(code)) {
    history.push(code);
    if (history.length > 5) history.shift(); // Lưu tối đa 5 mã gần nhất
    localStorage.setItem("trackingHistory", JSON.stringify(history));
  }
}
// ===== QUICK QUOTE FORM =====
// Mảng danh sách các quận hợp lệ của TP.HCM
const districtGroups = {
  inner: [
    "Quận 1",
    "Quận 3",
    "Quận 4",
    "Quận 5",
    "Quận 6",
    "Quận 10",
    "Quận 11",
    "Phú Nhuận",
    "Bình Thạnh",
    "Gò Vấp",
    "Tân Bình",
    "Tân Phú",
  ],
  outer: [
    "Quận 2",
    "Quận 7",
    "Quận 8",
    "Quận 9",
    "Quận 12",
    "Thủ Đức",
    "Bình Tân",
    "Hóc Môn",
    "Bình Chánh",
    "Nhà Bè",
    "Củ Chi",
    "Cần Giờ",
  ],
};

// Danh sách tất cả để kiểm tra hợp lệ
const allDistricts = [...districtGroups.inner, ...districtGroups.outer];

const quickQuoteForm = document.getElementById("quick-quote-form");

if (quickQuoteForm) {
  quickQuoteForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const from = document.getElementById("from-location").value.trim();
    const to = document.getElementById("to-location").value.trim();
    const service = document.getElementById("service-type").value;
    const isCod = document.getElementById("is-cod").checked;
    const resultDiv = document.getElementById("quote-result");

    // 1. Kiểm tra hợp lệ
    const isFromValid = allDistricts.some(
      (d) => d.toLowerCase() === from.toLowerCase(),
    );
    const isToValid = allDistricts.some(
      (d) => d.toLowerCase() === to.toLowerCase(),
    );

    if (!isFromValid || !isToValid) {
      resultDiv.innerHTML =
        "❌ Khu vực không hợp lệ. Vui lòng chọn quận tại TP.HCM.";
      return;
    }

    // 2. Xác định vùng (Nội hay Ngoại thành)
    const isOuter = districtGroups.outer.some(
      (d) =>
        d.toLowerCase() === from.toLowerCase() ||
        d.toLowerCase() === to.toLowerCase(),
    );

    // 3. Tính giá cước theo Bảng giá của bạn
    let price = 0;
    let vehicle = "Xe máy";

    if (service === "standard") {
      price = 30000;
    } else if (service === "express") {
      price = 50000;
    } else if (service === "bulk") {
      resultDiv.innerHTML =
        "📞 <strong>Giao số lượng lớn:</strong> Vui lòng liên hệ Hotline để có giá tốt nhất cho Ô tô.";
      return;
    }

    // Phụ phí ngoại thành (ví dụ cộng thêm 10k nếu có 1 điểm ở ngoại thành)
    if (isOuter) price += 10000;

    // Phụ phí COD theo bảng giá của bạn
    if (isCod) price += 5000;

    // 4. Hiển thị kết quả xịn xò
    resultDiv.innerHTML = `
    <div class="quote-card">
      <h4>Báo giá dự kiến</h4>
      <p>🚚 Phương tiện: <strong>${vehicle}</strong></p>
      <p>📍 Khu vực: <strong>${isOuter ? "Ngoại thành" : "Nội thành"}</strong></p>
      <p>💰 Tổng cước: <strong style="color: #ff7a00; font-size: 20px;">${price.toLocaleString()}đ</strong></p>
      ${isCod ? "<small>(Đã bao gồm phí COD 5.000đ)</small>" : ""}
    </div>
  `;
    resultDiv.classList.add("show");
  });
}

// Chạy animation khi trang load xong
window.addEventListener("load", () => {
  // Lấy tất cả phần tử có animation
  const animatedElements = document.querySelectorAll(
    ".animate-top, .animate-bottom, .animate-right",
  );

  // Hiện lần lượt từng phần tử cho mượt
  animatedElements.forEach((el, index) => {
    setTimeout(() => {
      el.classList.add("animate-show");
    }, index * 150);
  });
});
