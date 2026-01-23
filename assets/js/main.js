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

// ===== CONTACT FORM SUBMIT (REAL DATA) =====
const form = document.getElementById("contact-form");

// Tạo div hiển thị message nếu chưa có
let msgDiv = document.getElementById("form-message");
if (!msgDiv) {
  msgDiv = document.createElement("div");
  msgDiv.id = "form-message";
  msgDiv.style.display = "none";
  form.parentNode.insertBefore(msgDiv, form.nextSibling);
}

// ===== HÀM HIỂN THỊ LỖI (Helper) =====
function showFieldError(input, message) {
  // 1. Thêm class lỗi cho input (viền đỏ)
  input.classList.add("input-error");

  // 2. Kiểm tra xem đã có tin nhắn lỗi chưa, nếu chưa thì tạo mới
  let errorSpan = input.parentNode.querySelector(".field-error-msg");
  if (!errorSpan) {
    errorSpan = document.createElement("span");
    errorSpan.className = "field-error-msg";
    input.parentNode.appendChild(errorSpan);
  }

  // 3. Gán nội dung lỗi
  errorSpan.innerText = message;
}

// ===== HÀM XÓA LỖI (Helper) =====
function clearFieldError(input) {
  input.classList.remove("input-error");
  const errorSpan = input.parentNode.querySelector(".field-error-msg");
  if (errorSpan) {
    errorSpan.remove();
  }
}

// ===== HÀM KHỬ MÃ ĐỘC XSS (Helper) =====
function escapeHtml(text) {
  if (!text) return "";
  return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

// ===== TÍNH TIỀN SHIP TỰ ĐỘNG CHO FORM ĐẶT HÀNG =====
function calculateOrderShipping() {
  const pickupVal = document.getElementById("pickup-addr").value.toLowerCase();
  const deliveryVal = document
    .getElementById("delivery-addr")
    .value.toLowerCase();
  const serviceType = document.getElementById("order-service-type").value;
  const pricePreview = document.getElementById("price-preview");
  const feeDisplay = document.getElementById("shipping-fee-display");
  const feeInput = document.getElementById("shipping-fee-input");
  const weightInput = document.getElementById("weight");
  const codInput = document.getElementById("cod_amount");

  // Lấy config từ PHP (hoặc fallback mặc định)
  const config = window.pricingConfig || {
    weight_free: 2,
    weight_price: 5000,
    cod_min: 5000,
  };

  // Mặc định
  let price = 0;

  // Chỉ tính khi đã nhập cả 2 địa chỉ
  if (pickupVal.length > 5 && deliveryVal.length > 5) {
    // Giá cơ bản
    let service = null;
    if (window.servicesData) {
      service = window.servicesData.find((s) => s.type_key === serviceType);
    }

    if (service) {
      if (service.base_price == 0) {
        // Nếu giá = 0 (Liên hệ/Bulk)
        pricePreview.style.display = "block";
        feeDisplay.innerText = "Liên hệ";
        feeInput.value = 0;
        return;
      }
      price = parseFloat(service.base_price);
    } else {
      // Fallback nếu không có data
      if (serviceType === "standard") price = 30000;
      else if (serviceType === "express") price = 50000;
    }

    // --- 1. TÍNH PHÍ VÙNG MIỀN ---
    // Đã bỏ tính năng phí vùng miền theo yêu cầu
    let regionFee = 0;

    // --- 2. TÍNH PHÍ KHỐI LƯỢNG ---
    let weight = parseFloat(weightInput ? weightInput.value : 1) || 1;
    let weightFee = 0;
    if (weight > config.weight_free) {
      weightFee = Math.ceil(weight - config.weight_free) * config.weight_price;
    }

    // --- 3. TÍNH PHÍ COD ---
    let codAmount = parseFloat(codInput ? codInput.value : 0) || 0;
    let codFee = 0;
    if (codAmount > 0) {
      codFee = Math.max(parseFloat(config.cod_min), codAmount * 0.01);
    }

    // TỔNG CỘNG
    const total = price + regionFee + weightFee + codFee;

    // Hiển thị
    pricePreview.style.display = "block";
    feeDisplay.innerText = total.toLocaleString();
    feeInput.value = total;
  } else {
    pricePreview.style.display = "none";
    feeInput.value = 0;
  }
}

// Gắn sự kiện tính tiền vào các ô input của form đặt hàng
const orderInputs = [
  document.getElementById("pickup-addr"),
  document.getElementById("delivery-addr"),
  document.getElementById("order-service-type"),
  document.getElementById("weight"),
  document.getElementById("cod_amount"),
];

if (orderInputs[0]) {
  orderInputs.forEach((input) => {
    if (input) {
      input.addEventListener("input", calculateOrderShipping);
      input.addEventListener("change", calculateOrderShipping);
    }
  });
}

if (form) {
  form.addEventListener("submit", function (e) {
    e.preventDefault(); // chặn reload

    // ===== KIỂM TRA ĐĂNG NHẬP =====
    // Nếu chưa đăng nhập, hiện modal và dừng lại
    if (!window.isLoggedIn) {
      document.getElementById("auth-modal").style.display = "block";
      return;
    }

    const btn = form.querySelector("button");
    btn.innerText = "Đang tạo đơn hàng...";
    btn.disabled = true;

    // ===== 1. VALIDATE DỮ LIỆU =====
    let isValid = true;

    // Lấy các input
    const nameInp = form.querySelector("[name=name]");
    const phoneInp = form.querySelector("[name=phone]");
    const receiverNameInp = form.querySelector("[name=receiver_name]");
    const receiverPhoneInp = form.querySelector("[name=receiver_phone]");
    const pickupInp = form.querySelector("[name=pickup]");
    const deliveryInp = form.querySelector("[name=delivery]");
    const weightInp = form.querySelector("[name=weight]");
    const codInp = form.querySelector("[name=cod_amount]");

    // Reset lỗi cũ trước khi check
    [
      nameInp,
      phoneInp,
      receiverNameInp,
      receiverPhoneInp,
      pickupInp,
      deliveryInp,
      weightInp,
      codInp,
    ].forEach((inp) => {
      if (inp) clearFieldError(inp);
    });

    // Check Họ tên
    if (!nameInp.value.trim()) {
      showFieldError(nameInp, "Vui lòng nhập họ và tên");
      isValid = false;
    }

    // Check Số điện thoại (10-11 số, bắt đầu bằng 0)
    const phoneRegex = /^0[0-9]{9,10}$/;
    if (!phoneInp.value.trim()) {
      showFieldError(phoneInp, "Vui lòng nhập số điện thoại");
      isValid = false;
    } else if (!phoneRegex.test(phoneInp.value.trim())) {
      showFieldError(phoneInp, "SĐT không hợp lệ (phải bắt đầu bằng 0)");
      isValid = false;
    }

    // Check Người nhận
    if (!receiverNameInp.value.trim()) {
      showFieldError(receiverNameInp, "Vui lòng nhập tên người nhận");
      isValid = false;
    }
    if (!receiverPhoneInp.value.trim()) {
      showFieldError(receiverPhoneInp, "Vui lòng nhập SĐT người nhận");
      isValid = false;
    } else if (!phoneRegex.test(receiverPhoneInp.value.trim())) {
      showFieldError(receiverPhoneInp, "SĐT người nhận không hợp lệ");
      isValid = false;
    }

    // Regex kiểm tra địa chỉ phải có Quận/Huyện/TP/Phường/Xã (hoặc viết tắt Q., P.)
    const addressRegex = /(quận|huyện|tp|thành phố|phường|xã|q\.|p\.|q\d)/i;

    // Check Địa chỉ
    if (!pickupInp.value.trim()) {
      showFieldError(pickupInp, "Vui lòng nhập địa chỉ lấy hàng");
      isValid = false;
    } else if (pickupInp.value.trim().length < 10) {
      showFieldError(pickupInp, "Địa chỉ quá ngắn (cần số nhà, tên đường...)");
      isValid = false;
    } else if (!addressRegex.test(pickupInp.value)) {
      showFieldError(pickupInp, "Vui lòng ghi rõ Quận/Huyện (VD: Quận 1)");
      isValid = false;
    }

    if (!deliveryInp.value.trim()) {
      showFieldError(deliveryInp, "Vui lòng nhập địa chỉ giao hàng");
      isValid = false;
    } else if (deliveryInp.value.trim().length < 10) {
      showFieldError(
        deliveryInp,
        "Địa chỉ quá ngắn (cần số nhà, tên đường...)",
      );
      isValid = false;
    } else if (!addressRegex.test(deliveryInp.value)) {
      showFieldError(deliveryInp, "Vui lòng ghi rõ Quận/Huyện (VD: Quận 1)");
      isValid = false;
    }

    // Check Cân nặng (nếu có nhập thì phải >= 0)
    if (weightInp.value && parseFloat(weightInp.value) < 0) {
      showFieldError(weightInp, "Khối lượng không được âm");
      isValid = false;
    }

    // Check Tiền thu hộ (nếu có nhập thì phải >= 0)
    if (codInp && codInp.value && parseFloat(codInp.value) < 0) {
      showFieldError(codInp, "Tiền thu hộ không được âm");
      isValid = false;
    }

    // Nếu có lỗi thì dừng lại, không gửi fetch
    if (!isValid) {
      btn.innerText = "Xác nhận đặt đơn";
      btn.disabled = false;
      return;
    }

    // Xác nhận trước khi gửi đơn hàng quan trọng
    if (!confirm("Bạn có chắc chắn muốn xác nhận đặt đơn hàng này không?")) {
      btn.innerText = "Xác nhận đặt đơn";
      btn.disabled = false;
      return;
    }

    // ===== 2. GỬI DỮ LIỆU KHI ĐÃ HỢP LỆ =====
    const formData = new FormData(form);

    fetch("order.php", {
      // lưu ý sửa path chính xác
      method: "POST",
      body: formData,
    })
      .then((res) => res.text())
      .then((result) => {
        // reset class và hiển thị
        msgDiv.style.display = "block";
        msgDiv.className = "";

        if (result.trim() === "SUCCESS") {
          msgDiv.classList.add("success");
          // Escape dữ liệu trước khi hiển thị để chống XSS
          const name = escapeHtml(form.querySelector("[name=name]").value);
          const receiverName = escapeHtml(
            form.querySelector("[name=receiver_name]").value,
          );
          const pickup = escapeHtml(form.querySelector("[name=pickup]").value);
          const delivery = escapeHtml(
            form.querySelector("[name=delivery]").value,
          );
          const packageType = form.querySelector("[name=package_type]")
            .selectedOptions[0].text;
          const codInpEl = form.querySelector("[name=cod_amount]");
          const codAmount = codInpEl ? codInpEl.value : "";
          const shipFee = document.getElementById("shipping-fee-input").value;

          msgDiv.innerHTML = `
            <div class="success-message">
              <div class="check-icon">✓</div>
              <h3>Đã tạo đơn thành công!</h3>
              <p>Chào <strong>${name}</strong>, đơn hàng <strong>${packageType}</strong> của bạn đang được xử lý.</p>
              <div style="text-align:left; font-size:14px; background:#fff; padding:10px; border-radius:5px;">
                <p>🚩 <strong>Lấy tại:</strong> ${pickup}</p>
                <p>🏁 <strong>Giao đến:</strong> ${delivery}</p>
                <p>👤 <strong>Người nhận:</strong> ${receiverName}</p>
                <p>💵 <strong>Phí ship:</strong> ${parseInt(shipFee).toLocaleString()}đ</p>
                ${codAmount ? `<p>💰 <strong>Thu hộ:</strong> ${parseInt(codAmount).toLocaleString()}đ</p>` : ""}
              </div>
              <p style="margin-top:15px;">Chúng tôi sẽ liên hệ xác nhận sớm nhất.</p>
              <button onclick="location.reload()" class="btn-secondary">Quay lại</button>
            </div>
          `;

          form.reset(); // xóa dữ liệu form sau khi submit thành công
        } else {
          msgDiv.classList.add("error");
          msgDiv.innerHTML = `<strong>Có lỗi xảy ra:</strong><br>${result}`;
        }

        btn.innerText = "Xác nhận đặt đơn";
        btn.disabled = false;
      })
      .catch((error) => {
        msgDiv.style.display = "block";
        msgDiv.className = "error";
        msgDiv.innerHTML =
          "<strong>Không thể gửi đơn hàng. Vui lòng thử lại.</strong>";
        console.error(error);
        btn.innerText = "Xác nhận đặt đơn";
        btn.disabled = false;
      });
  });
}

// ===== XỬ LÝ MODAL & LOGIN AJAX =====
const modal = document.getElementById("auth-modal");
const closeModal = document.querySelector(".close-modal");

if (modal && closeModal) {
  // Đóng modal khi click X
  closeModal.onclick = function () {
    modal.style.display = "none";
  };
  // Đóng modal khi click ra ngoài
  window.onclick = function (event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  };

  // ===== LOGIC CHUYỂN ĐỔI LOGIN <-> REGISTER =====
  const loginView = document.getElementById("login-view");
  const registerView = document.getElementById("register-view");
  const forgotView = document.getElementById("forgot-view");

  const showRegisterBtn = document.getElementById("show-register-btn");
  const showLoginBtn = document.getElementById("show-login-btn");
  const showForgotBtn = document.getElementById("show-forgot-btn");
  const backToLoginBtn = document.getElementById("back-to-login-btn");

  showRegisterBtn.addEventListener("click", function (e) {
    e.preventDefault();
    loginView.style.display = "none";
    forgotView.style.display = "none";
    registerView.style.display = "block";
  });

  showLoginBtn.addEventListener("click", function (e) {
    e.preventDefault();
    registerView.style.display = "none";
    forgotView.style.display = "none";
    loginView.style.display = "block";
  });

  showForgotBtn.addEventListener("click", function (e) {
    e.preventDefault();
    loginView.style.display = "none";
    forgotView.style.display = "block";
  });

  backToLoginBtn.addEventListener("click", function (e) {
    e.preventDefault();
    forgotView.style.display = "none";
    loginView.style.display = "block";
  });

  // Xử lý form login trong modal
  const loginForm = document.getElementById("ajax-login-form");
  loginForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const loginBtn = loginForm.querySelector("button");
    const errorDiv = document.getElementById("login-error");

    loginBtn.innerText = "Đang xử lý...";
    loginBtn.disabled = true;
    errorDiv.style.display = "none";

    const formData = new FormData(loginForm);

    fetch("login_ajax.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "success") {
          // Đăng nhập thành công
          window.isLoggedIn = true; // Cập nhật trạng thái
          modal.style.display = "none"; // Ẩn modal

          // Tự động điền thông tin user vào form đặt hàng nếu form đang trống
          const nameInp = document.getElementById("name");
          const phoneInp = document.getElementById("phone");
          if (!nameInp.value) nameInp.value = data.user.fullname;
          if (!phoneInp.value) phoneInp.value = data.user.phone;

          // Tự động kích hoạt lại sự kiện submit form đặt hàng
          form.dispatchEvent(new Event("submit"));
        } else {
          errorDiv.innerText = data.message;
          errorDiv.style.display = "block";
          loginBtn.innerText = "Đăng Nhập & Gửi Đơn";
          loginBtn.disabled = false;
        }
      })
      .catch((err) => {
        console.error(err);
        errorDiv.innerText = "Lỗi kết nối.";
        errorDiv.style.display = "block";
        loginBtn.innerText = "Đăng Nhập & Gửi Đơn";
        loginBtn.disabled = false;
      });
  });

  // ===== XỬ LÝ FORM REGISTER AJAX =====
  const registerForm = document.getElementById("ajax-register-form");
  registerForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const regBtn = registerForm.querySelector("button");
    const errorDiv = document.getElementById("register-error");

    regBtn.innerText = "Đang xử lý...";
    regBtn.disabled = true;
    errorDiv.style.display = "none";

    const formData = new FormData(registerForm);

    fetch("register_ajax.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "success") {
          // Đăng ký thành công
          window.isLoggedIn = true;
          modal.style.display = "none";

          // Auto-fill thông tin vào form đặt hàng
          const nameInp = document.getElementById("name");
          const phoneInp = document.getElementById("phone");
          if (!nameInp.value) nameInp.value = data.user.fullname;
          if (!phoneInp.value) phoneInp.value = data.user.phone;

          // Gửi đơn hàng luôn
          form.dispatchEvent(new Event("submit"));
        } else {
          errorDiv.innerText = data.message;
          errorDiv.style.display = "block";
          regBtn.innerText = "Đăng Ký & Gửi Đơn";
          regBtn.disabled = false;
        }
      })
      .catch((err) => {
        console.error(err);
        errorDiv.innerText = "Lỗi kết nối.";
        errorDiv.style.display = "block";
        regBtn.innerText = "Đăng Ký & Gửi Đơn";
        regBtn.disabled = false;
      });
  });

  // ===== XỬ LÝ FORM QUÊN MẬT KHẨU AJAX =====
  const forgotForm = document.getElementById("ajax-forgot-form");
  if (forgotForm) {
    forgotForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const btn = forgotForm.querySelector("button");
      const msgDiv = document.getElementById("forgot-message");

      btn.innerText = "Đang gửi...";
      btn.disabled = true;
      msgDiv.style.display = "none";
      msgDiv.className = ""; // reset class

      const formData = new FormData(forgotForm);

      fetch("forgot_password_ajax.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          msgDiv.style.display = "block";
          if (data.status === "success") {
            msgDiv.style.color = "green";
            msgDiv.innerText = data.message;
            forgotForm.reset();
          } else {
            msgDiv.style.color = "red";
            msgDiv.innerText = data.message;
          }
          btn.innerText = "Gửi yêu cầu";
          btn.disabled = false;
        })
        .catch((err) => {
          console.error(err);
          msgDiv.style.display = "block";
          msgDiv.style.color = "red";
          msgDiv.innerText = "Lỗi kết nối.";
          btn.innerText = "Gửi yêu cầu";
          btn.disabled = false;
        });
    });
  }
}

// ===== FAQ ACCORDION =====
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
// ===== TRACKING FUNCTIONALITY (REAL DATABASE via AJAX) =====
function trackOrder(event, type) {
  event.preventDefault();
  console.log("Đang tra cứu đơn hàng loại:", type); // Debug log

  // 1. Xác định các phần tử
  const spinner = document.getElementById(`loading-spinner-${type}`);
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

  // Kiểm tra xem spinner và resultDiv có tồn tại không
  if (!spinner || !resultDiv) {
    console.error(
      "Không tìm thấy phần tử hiển thị kết quả (spinner/resultDiv)",
    );
    return;
  }

  // 4. Hiện hiệu ứng Loading và xóa kết quả cũ
  spinner.style.display = "block";
  resultDiv.innerHTML = "";

  // 5. Gửi request AJAX đến server
  const formData = new FormData();
  formData.append("code", code);
  formData.append("search_type", type); // Gửi thêm loại tra cứu (standard/bulk/cod)

  fetch("tracking_ajax.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Kết quả từ server:", data); // Debug log
      spinner.style.display = "none"; // Tắt loading

      if (data.status === "success") {
        const order = data.data;
        let cancelBtn = "";
        // Chỉ hiện nút hủy nếu trạng thái là pending (Chờ xử lý)
        if (order.status_raw === "pending") {
          cancelBtn = `<button onclick="cancelOrder('${order.order_code}')" style="margin-top:15px; background:#d9534f; color:white; border:none; padding:8px 16px; border-radius:4px; cursor:pointer; font-weight:600;">Hủy đơn hàng này</button>`;
        }

        // Tạo HTML cho Timeline
        let timelineHtml = '<div class="tracking-timeline">';
        if (order.timeline && order.timeline.length > 0) {
          order.timeline.forEach((item) => {
            timelineHtml += `
                    <div class="timeline-item">
                        <div class="timeline-icon">${item.icon}</div>
                        <div class="timeline-content">
                            <div class="timeline-time">${item.time}</div>
                            <div class="timeline-text">${item.text}</div>
                        </div>
                    </div>
                `;
          });
        }
        timelineHtml += "</div>";

        resultDiv.innerHTML = `
        <div style="background-color: #e8f4f8; border-left: 4px solid ${order.color}; padding: 20px; border-radius: 8px; margin-top: 15px; text-align: left;">
          <p><strong>Mã đơn:</strong> ${order.order_code}</p>
          <p><strong>Loại hàng:</strong> ${order.type}</p>
          <p style="font-size: 18px; color: ${order.color}; margin-top: 12px;">
            <strong>${order.icon} Trạng thái: ${order.status_text}</strong>
          </p>
          
          ${timelineHtml}

          ${cancelBtn}
        </div>
      `;
        // Lưu vào lịch sử
        if (typeof saveToHistory === "function") {
          saveToHistory(code);
        }
      } else {
        // Không tìm thấy đơn hàng
        resultDiv.innerHTML = `
        <div style="background-color: #f8e8e8; border-left: 4px solid #d9534f; padding: 20px; border-radius: 8px; margin-top: 15px; text-align: left;">
          <p style="color: #d9534f;"><strong>❌ Lỗi:</strong> ${data.message}</p>
          <p style="color: #999; font-size: 14px; margin-top: 8px;">Vui lòng kiểm tra lại mã đơn hàng (VD: FAST-XXXXXX).</p>
        </div>
      `;
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      spinner.style.display = "none";
      resultDiv.innerHTML = `
      <div style="background-color: #f8e8e8; border-left: 4px solid #d9534f; padding: 20px; border-radius: 8px; margin-top: 15px; text-align: left;">
        <p style="color: #d9534f;"><strong>❌ Lỗi kết nối:</strong> ${error.message}</p>
      </div>
    `;
    });
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

// ===== HỦY ĐƠN HÀNG (AJAX) =====
function cancelOrder(code) {
  if (!confirm("Bạn có chắc chắn muốn hủy đơn hàng " + code + " không?"))
    return;

  const formData = new FormData();
  formData.append("code", code);

  fetch("cancel_order_ajax.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      alert(data.message);
      if (data.status === "success") {
        location.reload(); // Tải lại trang để cập nhật trạng thái mới
      }
    })
    .catch((err) => console.error(err));
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

    // Lấy config
    const config = window.pricingConfig || {
      weight_free: 2,
      weight_price: 5000,
      cod_min: 5000,
    };

    const from = document.getElementById("from-location").value.trim();
    const to = document.getElementById("to-location").value.trim();
    const service = document.getElementById("service-type").value;
    const isCod = document.getElementById("is-cod").checked;
    // Giả lập input weight/cod cho Quick Quote (hoặc lấy mặc định nếu chưa có input HTML)
    const weight = 1;
    const codAmount = isCod ? 1000000 : 0; // Giả định thu hộ 1tr nếu tick COD để demo phí
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

    // 2. Logic Vùng miền
    let regionFee = 0;
    let regionText = "Tiêu chuẩn"; // Đổi text hiển thị mặc định

    // 3. Tính giá cước theo Bảng giá của bạn
    let price = 0;
    let vehicle = "Xe máy";

    // Tìm dịch vụ trong data
    let svcData = null;
    if (window.servicesData) {
      svcData = window.servicesData.find((s) => s.type_key === service);
    }

    if (svcData) {
      if (svcData.base_price == 0) {
        resultDiv.innerHTML = `📞 <strong>${svcData.name}:</strong> Vui lòng liên hệ Hotline để có giá tốt nhất.`;
        return;
      }
      price = parseFloat(svcData.base_price);
    } else {
      // Fallback cũ
      if (service === "standard") price = 30000;
      else if (service === "express") price = 50000;
    }

    // Phụ phí Khối lượng (Mặc định 1kg cho Quick Quote)
    let weightFee = 0;
    if (weight > config.weight_free) {
      weightFee = Math.ceil(weight - config.weight_free) * config.weight_price;
    }

    // Phụ phí COD
    let codFee = 0;
    if (isCod) {
      // Nếu chỉ tick checkbox mà không có số tiền cụ thể, lấy phí tối thiểu
      codFee = parseFloat(config.cod_min);
    }

    const total = price + regionFee + weightFee + codFee;

    // 4. Hiển thị kết quả chi tiết
    resultDiv.innerHTML = `
    <div class="quote-card">
      <h4>Báo giá dự kiến</h4>
      <p>🚚 Phương tiện: <strong>${vehicle}</strong></p>
      <p>📍 Khu vực: <strong>${regionText}</strong></p>
      <hr style="border: 0; border-top: 1px dashed #eee; margin: 10px 0;">
      <div style="font-size: 14px; color: #333;">
          <p>🔹 Phí cơ bản: ${price.toLocaleString()}đ</p>
          ${weightFee > 0 ? `<p>🔹 Phí quá tải (${weight}kg): ${weightFee.toLocaleString()}đ</p>` : ""}
          ${codFee > 0 ? `<p>🔹 Phí COD: ${codFee.toLocaleString()}đ</p>` : ""}
      </div>
      <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;">
      <p>💰 Tổng cộng: <strong style="color: #ff7a00; font-size: 22px;">${total.toLocaleString()}đ</strong></p>
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
