(function () {
  const inPublicDir = window.location.pathname.toLowerCase().includes("/public/");
  const basePath =
    typeof window.apiBasePath === "string"
      ? window.apiBasePath
      : inPublicDir
        ? ""
        : "public/";

  if (typeof window.apiBasePath !== "string") {
    window.apiBasePath = basePath;
  }
  if (typeof window.isLoggedIn !== "boolean") {
    window.isLoggedIn = false;
  }
  if (!Array.isArray(window.servicesData)) {
    window.servicesData = [
      { id: 1, name: "Giao chậm", type_key: "slow", base_price: 20000.0 },
      { id: 2, name: "Giao tiêu chuẩn", type_key: "standard", base_price: 30000.0 },
      { id: 3, name: "Giao nhanh", type_key: "fast", base_price: 40000.0 },
      { id: 4, name: "Giao hỏa tốc", type_key: "express", base_price: 50000.0 },
    ];
  }
  if (!window.pricingConfig || typeof window.pricingConfig !== "object") {
    window.pricingConfig = { weight_free: 2, weight_price: 5000, cod_min: 5000 };
  }

  const partialUrl = `${basePath}assets/partials/shared-modals.html`;
  const deliveryModalId = "booking-modal-delivery";
  const movingModalId = "booking-modal-moving";
  let initialized = false;
  const deliveryItemOptionsByType = {
    thuong: [
      "Quần áo/vải vóc",
      "Giày dép/túi xách",
      "Sách vở/văn phòng phẩm",
      "Đồ chơi nhựa",
      "Đồ gia dụng nhựa/inox",
      "Phụ kiện điện tử đơn giản",
    ],
    "gia-tri-cao": [
      "Điện thoại/máy tính bảng",
      "Laptop/máy ảnh",
      "Đồng hồ thông minh/tai nghe cao cấp",
      "Mỹ phẩm chính hãng",
      "Nước hoa",
      "Trang sức/đá quý",
    ],
    "de-vo": [
      "Đồ gốm sứ/chén dĩa",
      "Bình thủy tinh",
      "Màn hình TV/máy tính",
      "Gương soi",
      "Tượng đá/đồ thủ công mỹ nghệ",
      "Đèn trang trí/đèn chùm",
    ],
    "chat-long": [
      "Dầu ăn/nước mắm",
      "Mật ong/rượu vang",
      "Sữa nước/đồ uống đóng chai",
      "Hóa chất công nghiệp/sơn/dung môi",
      "Dầu nhớt",
      "Nước hoa",
    ],
    "pin-lithium": [
      "Sạc dự phòng",
      "Pin xe máy điện",
      "Xe điện",
      "Quạt tích điện",
      "Đèn pin",
    ],
    "dong-lanh": [
      "Thịt/cá/hải sản tươi sống",
      "Thực phẩm đông lạnh",
      "Rau củ/trái cây tươi",
      "Vaccine cần bảo quản lạnh",
      "Dược phẩm cần bảo quản lạnh",
    ],
    "cong-kenh": [
      "Sofa/tủ quần áo/giường gỗ",
      "Lốp xe tải",
      "Máy móc công trình",
      "Bồn nước inox",
      "Cuộn cáp điện lớn",
    ],
  };

  function isMovingType(typeKey) {
    if (
      window.serviceHelper &&
      typeof window.serviceHelper.isMovingService === "function"
    ) {
      return window.serviceHelper.isMovingService(typeKey);
    }
    return String(typeKey || "").startsWith("moving_");
  }

  function isDeliveryType(typeKey) {
    const value = String(typeKey || "").trim().toLowerCase();
    return [
      "slow",
      "standard",
      "fast",
      "express",
      "intl_economy",
      "intl_express",
    ].includes(value);
  }

  function isInternationalDeliveryType(typeKey) {
    const value = String(typeKey || "").trim().toLowerCase();
    return ["intl_economy", "intl_express"].includes(value);
  }

  function ensureModalStyles() {
    if (document.getElementById("shared-modal-inline-styles")) return;
    const style = document.createElement("style");
    style.id = "shared-modal-inline-styles";
    style.textContent = `
      .form-section {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px dashed #eee;
      }
      .form-section:last-child {
        border-bottom: none;
      }
      .form-section h3 {
        color: #0a2a66;
        margin-bottom: 15px;
        font-size: 16px;
        border-left: 4px solid #ff7a00;
        padding-left: 10px;
        background: #f8faff;
        padding-top: 5px;
        padding-bottom: 5px;
      }
      .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
      }
      .booking-order-form label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #0a2a66;
        margin-bottom: 5px;
      }
      .booking-order-form input,
      .booking-order-form select,
      .booking-order-form textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
      }
      .booking-order-form textarea {
        min-height: 80px;
        font-family: inherit;
      }
      .booking-order-form input[type="checkbox"],
      .booking-order-form input[type="radio"] {
        width: 16px;
        height: 16px;
        padding: 0;
        margin: 0;
        flex: 0 0 16px;
        accent-color: #0a2a66;
      }
      .booking-order-form .checkbox-inline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0;
        cursor: pointer;
      }
      .booking-order-form .checkbox-stack {
        display: flex;
        flex-direction: column;
        gap: 8px;
      }
      .booking-order-form .checkbox-center {
        justify-content: center;
      }
      .booking-order-form .modal-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        justify-content: flex-end;
        margin-top: 4px;
      }
      .booking-order-form .modal-actions .btn-secondary,
      .booking-order-form .modal-actions .btn-primary {
        min-width: 140px;
        height: 42px;
        padding: 0 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-weight: 600;
      }
      .modal-close-btn {
        position: absolute;
        right: 16px;
        top: 14px;
        cursor: pointer;
        font-size: 24px;
        font-weight: 700;
        width: 36px;
        height: 36px;
        line-height: 36px;
        text-align: center;
        color: #1f3f75;
        background: linear-gradient(180deg, #ffffff 0%, #eef4ff 100%);
        border: 1px solid #d7e4ff;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(10, 42, 102, 0.12);
        transition: transform 0.18s ease, box-shadow 0.18s ease,
          background-color 0.18s ease, color 0.18s ease;
        user-select: none;
      }
      .modal-close-btn:hover {
        background: #0a2a66;
        color: #fff;
        transform: translateY(-1px) scale(1.03);
        box-shadow: 0 8px 18px rgba(10, 42, 102, 0.28);
      }
      .modal-close-btn:active {
        transform: scale(0.97);
        box-shadow: 0 3px 8px rgba(10, 42, 102, 0.22);
      }
      .modal-close-btn:focus-visible {
        outline: 3px solid rgba(255, 122, 0, 0.45);
        outline-offset: 2px;
      }
      @media (max-width: 768px) {
        .form-grid {
          grid-template-columns: 1fr;
        }
        .modal-content {
          width: 95% !important;
          padding: 15px !important;
          margin: 15% auto !important;
        }
        .modal-close-btn {
          right: 10px;
          top: 10px;
          width: 34px;
          height: 34px;
          line-height: 34px;
          font-size: 22px;
        }
        .booking-order-form .modal-actions {
          flex-direction: column-reverse;
          align-items: stretch;
        }
        .booking-order-form .modal-actions .btn-secondary,
        .booking-order-form .modal-actions .btn-primary {
          width: 100%;
          min-width: 0;
        }
      }
    `;
    document.head.appendChild(style);
  }

  function ensureModalMarkup() {
    const hasDelivery = !!document.getElementById(deliveryModalId);
    const hasMoving = !!document.getElementById(movingModalId);
    if (hasDelivery && hasMoving) return true;

    try {
      const xhr = new XMLHttpRequest();
      xhr.open("GET", partialUrl, false);
      xhr.send(null);
      if (xhr.status >= 200 && xhr.status < 300 && xhr.responseText.trim()) {
        ensureModalStyles();
        document.body.insertAdjacentHTML("beforeend", xhr.responseText);
        return true;
      }
      console.error("Cannot load shared modals:", partialUrl, xhr.status);
    } catch (err) {
      console.error("Cannot load shared modals:", err);
    }
    return false;
  }

  function getModal(kind) {
    if (kind === "delivery") return document.getElementById(deliveryModalId);
    if (kind === "moving") return document.getElementById(movingModalId);
    return null;
  }

  function isVisible(modal) {
    return !!modal && modal.style.display === "block";
  }

  function syncBodyScrollState() {
    const deliveryModal = getModal("delivery");
    const movingModal = getModal("moving");
    const anyOpen = isVisible(deliveryModal) || isVisible(movingModal);
    document.body.style.overflow = anyOpen ? "hidden" : "auto";
  }

  function openModal(kind) {
    const modal = getModal(kind);
    if (!modal) return;
    modal.style.display = "block";
    syncBodyScrollState();
  }

  function closeModal(kind) {
    const modal = getModal(kind);
    if (!modal) return;
    modal.style.display = "none";
    syncBodyScrollState();
  }

  function closeAllModals() {
    closeModal("delivery");
    closeModal("moving");
  }

  function setSelectOptions(selectEl, options, placeholder) {
    if (!selectEl) return;
    const list = Array.isArray(options) ? options : [];
    selectEl.innerHTML = "";
    const placeholderOption = document.createElement("option");
    placeholderOption.value = "";
    placeholderOption.textContent = placeholder || "Vui lòng chọn";
    selectEl.appendChild(placeholderOption);

    list.forEach((value) => {
      const option = document.createElement("option");
      option.value = value;
      option.textContent = value;
      selectEl.appendChild(option);
    });
  }

  function normalizeLocationKey(value) {
    return String(value || "")
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/\./g, "")
      .replace(/\s+/g, " ")
      .trim()
      .toLowerCase();
  }

  function toUniqueSortedLocations(list) {
    const map = new Map();
    (Array.isArray(list) ? list : []).forEach((item) => {
      const label = String(item || "").trim();
      if (!label) return;
      const key = normalizeLocationKey(label);
      if (!map.has(key)) {
        map.set(key, label);
      }
    });
    return Array.from(map.values()).sort((a, b) =>
      a.localeCompare(b, "vi", { sensitivity: "base" }),
    );
  }

  function initDeliveryItemFields() {
    const itemType = document.getElementById("delivery-item-type");
    const itemName = document.getElementById("delivery-item-name");
    if (!itemType || !itemName) return;

    const applyState = () => {
      const key = String(itemType.value || "").trim().toLowerCase();
      const options = deliveryItemOptionsByType[key] || [];
      setSelectOptions(itemName, options, "Chọn tên hàng");
      itemName.disabled = options.length === 0;
      if (options.length === 0) {
        setSelectOptions(itemName, [], "Chọn nhóm hàng trước");
      }
    };

    itemType.addEventListener("change", applyState);
    applyState();
  }

  function getRouteLocationSource() {
    const quoteData =
      window.QUOTE_SHIPPING_DATA && typeof window.QUOTE_SHIPPING_DATA === "object"
        ? window.QUOTE_SHIPPING_DATA
        : {};
    const rawCityMap =
      quoteData.cities && typeof quoteData.cities === "object"
        ? quoteData.cities
        : {};
    const domesticData =
      quoteData.domestic && typeof quoteData.domestic === "object"
        ? quoteData.domestic
        : {};
    const cityOptions = toUniqueSortedLocations(
      Array.isArray(domesticData.cityOptions) ? domesticData.cityOptions : [],
    );
    const cityNames = Object.keys(rawCityMap);
    const fallbackCities = [
      "TP Hồ Chí Minh",
      "Hà Nội",
      "Đà Nẵng",
      "Cần Thơ",
      "Hải Phòng",
    ];
    const cities = cityOptions.length
      ? cityOptions
      : cityNames.length
        ? cityNames
        : fallbackCities;

    const cityMap = {};
    cities.forEach((city) => {
      cityMap[city] = Array.isArray(rawCityMap[city]) ? rawCityMap[city] : [];
    });
    return { cityMap, cities };
  }

  function bindCityDistrictFields(
    citySelect,
    districtSelect,
    cityPlaceholder,
    districtPlaceholder,
    cities,
    cityMap,
  ) {
    if (!citySelect || !districtSelect) return;

    const currentCity = citySelect.value;
    setSelectOptions(citySelect, cities, cityPlaceholder);
    if (currentCity && cities.includes(currentCity)) {
      citySelect.value = currentCity;
    }

    const applyDistrict = () => {
      const city = citySelect.value;
      const districts = Array.isArray(cityMap[city]) ? cityMap[city] : [];
      const previousDistrict = districtSelect.value;
      setSelectOptions(districtSelect, districts, districtPlaceholder);
      districtSelect.disabled = districts.length === 0;
      if (previousDistrict && districts.includes(previousDistrict)) {
        districtSelect.value = previousDistrict;
      }
    };

    citySelect.addEventListener("change", applyDistrict);
    applyDistrict();
  }

  function initDeliveryRouteFields() {
    const pickupCity = document.getElementById("pickup-city");
    const pickupDistrict = document.getElementById("pickup-district");
    const deliveryCity = document.getElementById("delivery-city");
    const deliveryDistrict = document.getElementById("delivery-district");

    if (!pickupCity || !pickupDistrict || !deliveryCity || !deliveryDistrict) return;

    const { cityMap, cities } = getRouteLocationSource();

    bindCityDistrictFields(
      pickupCity,
      pickupDistrict,
      "Chọn tỉnh/thành phố gửi",
      "Chọn quận/huyện gửi",
      cities,
      cityMap,
    );
    bindCityDistrictFields(
      deliveryCity,
      deliveryDistrict,
      "Chọn tỉnh/thành phố nhận",
      "Chọn quận/huyện nhận",
      cities,
      cityMap,
    );
  }

  function initInternationalDestinationFields() {
    const countrySelect = document.getElementById("delivery-intl-country");
    const provinceSelect = document.getElementById("delivery-intl-province");
    if (!countrySelect || !provinceSelect) return;

    const quoteData =
      window.QUOTE_SHIPPING_DATA && typeof window.QUOTE_SHIPPING_DATA === "object"
        ? window.QUOTE_SHIPPING_DATA
        : {};
    const intlData =
      quoteData.international && typeof quoteData.international === "object"
        ? quoteData.international
        : {};
    const countries = toUniqueSortedLocations(
      Array.isArray(intlData.countries) ? intlData.countries : [],
    );
    const destinationRegions =
      intlData.destinationRegions && typeof intlData.destinationRegions === "object"
        ? intlData.destinationRegions
        : {};
    const countryZoneMap =
      intlData.countryZoneMap && typeof intlData.countryZoneMap === "object"
        ? intlData.countryZoneMap
        : {};
    const regionCountries = toUniqueSortedLocations(Object.keys(destinationRegions));
    const zoneCountries = toUniqueSortedLocations(Object.keys(countryZoneMap));

    // Chỉ giữ quốc gia đồng nhất giữa danh sách hiển thị + vùng đến + zone tính giá.
    const regionSet = new Set(regionCountries.map(normalizeLocationKey));
    const zoneSet = new Set(zoneCountries.map(normalizeLocationKey));
    const countryOptions = countries.filter(
      (c) =>
        regionSet.has(normalizeLocationKey(c)) &&
        zoneSet.has(normalizeLocationKey(c)),
    );

    setSelectOptions(countrySelect, countryOptions, "Chọn quốc gia nhận");
    setSelectOptions(provinceSelect, [], "Chọn tỉnh/thành phố nhận");
    provinceSelect.disabled = true;

    const applyRegions = () => {
      const country = countrySelect.value;
      const regions = toUniqueSortedLocations(
        Array.isArray(destinationRegions[country])
          ? destinationRegions[country]
          : [],
      );
      setSelectOptions(provinceSelect, regions, "Chọn tỉnh/thành phố nhận");
      provinceSelect.disabled = regions.length === 0;
    };

    countrySelect.addEventListener("change", applyRegions);
    applyRegions();
  }

  function initDeliveryServiceMode() {
    const serviceSelect = document.getElementById("order-service-type");
    if (!serviceSelect) return;

    const deliveryCityGroup = document.getElementById("delivery-domestic-city-group");
    const deliveryDistrictGroup = document.getElementById(
      "delivery-domestic-district-group",
    );
    const intlCountryGroup = document.getElementById("delivery-intl-country-group");
    const intlProvinceGroup = document.getElementById("delivery-intl-province-group");
    const intlCountrySelect = document.getElementById("delivery-intl-country");
    const codField = document.getElementById("cod-field-group");
    const codInput = document.getElementById("cod_amount");

    const applyState = () => {
      const isIntl = isInternationalDeliveryType(serviceSelect.value);
      if (deliveryCityGroup) deliveryCityGroup.style.display = isIntl ? "none" : "";
      if (deliveryDistrictGroup) {
        deliveryDistrictGroup.style.display = isIntl ? "none" : "";
      }
      if (intlCountryGroup) intlCountryGroup.style.display = isIntl ? "" : "none";
      if (intlProvinceGroup) intlProvinceGroup.style.display = isIntl ? "" : "none";
      if (intlCountrySelect) {
        intlCountrySelect.required = isIntl;
      }
      if (codField) codField.style.display = isIntl ? "none" : "";
      if (codInput) {
        if (isIntl) {
          codInput.value = "0";
          codInput.disabled = true;
        } else {
          codInput.disabled = false;
        }
      }
    };

    serviceSelect.addEventListener("change", applyState);
    applyState();
  }

  function initMovingRouteFields() {
    const pickupCity = document.getElementById("pickup-city-moving");
    const pickupDistrict = document.getElementById("pickup-district-moving");
    const deliveryCity = document.getElementById("delivery-city-moving");
    const deliveryDistrict = document.getElementById("delivery-district-moving");

    if (!pickupCity || !pickupDistrict || !deliveryCity || !deliveryDistrict) return;

    const { cityMap, cities } = getRouteLocationSource();

    bindCityDistrictFields(
      pickupCity,
      pickupDistrict,
      "Chọn tỉnh/thành phố lấy hàng",
      "Chọn quận/huyện lấy hàng",
      cities,
      cityMap,
    );
    bindCityDistrictFields(
      deliveryCity,
      deliveryDistrict,
      "Chọn tỉnh/thành phố giao hàng",
      "Chọn quận/huyện giao hàng",
      cities,
      cityMap,
    );
  }

  function initCorporateSection(checkboxId, fieldsId) {
    const checkbox = document.getElementById(checkboxId);
    const fields = document.getElementById(fieldsId);
    if (!checkbox || !fields) return;

    const applyState = () => {
      fields.style.display = checkbox.checked ? "block" : "none";
    };

    checkbox.addEventListener("change", applyState);
    applyState();
  }

  function initMovingServiceDetails() {
    const serviceSelect = document.getElementById("order-service-type-moving");
    if (!serviceSelect) return;

    const details = Array.from(
      document.querySelectorAll(".moving-detail[data-moving-service]"),
    );
    const applyState = () => {
      const selected = String(serviceSelect.value || "").trim().toLowerCase();
      details.forEach((block) => {
        const key = String(block.dataset.movingService || "").toLowerCase();
        block.style.display = key === selected ? "block" : "none";
      });
    };

    serviceSelect.addEventListener("change", applyState);
    applyState();
  }

  function ensureCoreBindings() {
    if (
      window.FastGoCore &&
      typeof window.FastGoCore.bindOrderShippingInputs === "function"
    ) {
      window.FastGoCore.bindOrderShippingInputs();
    }
  }

  function initModalBindings() {
    if (initialized) return;
    initialized = true;

    initCorporateSection(
      "is_corporate_checkbox_delivery",
      "corporate-fields-delivery",
    );
    initCorporateSection(
      "is_corporate_checkbox_moving",
      "corporate-fields-moving",
    );
    initMovingServiceDetails();
    initDeliveryItemFields();
    initDeliveryRouteFields();
    initInternationalDestinationFields();
    initDeliveryServiceMode();
    initMovingRouteFields();
    ensureCoreBindings();

    window.addEventListener("click", function (event) {
      const deliveryModal = getModal("delivery");
      const movingModal = getModal("moving");
      if (event.target === deliveryModal) closeModal("delivery");
      if (event.target === movingModal) closeModal("moving");
    });
  }

  window.openBookingModal = function (serviceType) {
    if (!ensureModalMarkup()) return;
    initModalBindings();

    const normalized = String(serviceType || "").trim().toLowerCase();
    const openingMoving = isMovingType(normalized);
    closeAllModals();

    if (openingMoving) {
      const movingSelect = document.getElementById("order-service-type-moving");
      if (
        movingSelect &&
        normalized &&
        ["moving_house", "moving_office", "moving_warehouse"].includes(normalized)
      ) {
        movingSelect.value = normalized;
        movingSelect.dispatchEvent(new Event("change"));
      }
      openModal("moving");
      return;
    }

    const deliverySelect = document.getElementById("order-service-type");
    if (deliverySelect && isDeliveryType(normalized)) {
      deliverySelect.value = normalized;
      deliverySelect.dispatchEvent(new Event("change"));
    }
    openModal("delivery");
  };

  window.closeBookingModal = function (modalType) {
    if (modalType === "delivery") {
      closeModal("delivery");
      return;
    }
    if (modalType === "moving") {
      closeModal("moving");
      return;
    }
    closeAllModals();
  };

  if (ensureModalMarkup()) initModalBindings();

  document.addEventListener("DOMContentLoaded", function () {
    if (ensureModalMarkup()) initModalBindings();

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("open_booking") === "true") {
      window.openBookingModal(urlParams.get("service") || "");
    }
  });
})();
