const FAST_POLL_DELAY = 1500;
const WARM_POLL_DELAY = 3000;
const STEADY_POLL_DELAY = 8000;

export function getAdaptivePaymentPollingDelay(attempt = 0) {
  if (attempt <= 1) return FAST_POLL_DELAY;
  if (attempt <= 4) return WARM_POLL_DELAY;
  return STEADY_POLL_DELAY;
}

export function buildCheckoutPrimaryActionState({
  processingStage = "",
  paymentStatus = "",
  hasActivePendingInvoice = false,
  showRegenerateButton = false,
  hasSchedule = true,
} = {}) {
  if (processingStage === "creating_invoice") {
    return {
      label: "Đang tạo hóa đơn...",
      icon: "fa-spinner fa-spin",
      disabled: true,
      emphasis: "loading",
    };
  }

  if (processingStage === "retrying_invoice") {
    return {
      label: "Đang tạo mã QR mới...",
      icon: "fa-spinner fa-spin",
      disabled: true,
      emphasis: "loading",
    };
  }

  if (!hasSchedule) {
    return {
      label: "Chọn lịch khởi hành",
      icon: "fa-calendar-days",
      disabled: true,
      emphasis: "muted",
    };
  }

  if (paymentStatus === "paid") {
    return {
      label: "Đã thanh toán",
      icon: "fa-circle-check",
      disabled: true,
      emphasis: "success",
    };
  }

  if (hasActivePendingInvoice) {
    return {
      label: "Đang chờ thanh toán",
      icon: "fa-hourglass-half",
      disabled: true,
      emphasis: "pending",
    };
  }

  if (showRegenerateButton) {
    return {
      label: "Tạo mã QR mới",
      icon: "fa-rotate-right",
      disabled: false,
      emphasis: "default",
    };
  }

  return {
    label: "Tạo hóa đơn thanh toán",
    icon: "fa-qrcode",
    disabled: false,
    emphasis: "default",
  };
}

export function buildCheckoutProgressState(processingStage = "") {
  if (processingStage === "creating_invoice") {
    return {
      visible: true,
      eyebrow: "Đang xử lý",
      title: "Hệ thống đang tạo hóa đơn và mã QR cho bạn",
      description: "Vui lòng chờ trong giây lát. Mã QR sẽ tự xuất hiện ngay trên màn hình này.",
    };
  }

  if (processingStage === "retrying_invoice") {
    return {
      visible: true,
      eyebrow: "Đang tạo lại",
      title: "Hệ thống đang làm mới mã QR thanh toán",
      description: "Hóa đơn cũ vẫn được giữ nguyên. Mã QR mới sẽ xuất hiện sau khi làm mới thành công.",
    };
  }

  return {
    visible: false,
    eyebrow: "",
    title: "",
    description: "",
  };
}

function formatTimestamp(value) {
  if (!value) return "";
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return "";

  const time = date.toLocaleTimeString("vi-VN", {
    hour: "2-digit",
    minute: "2-digit",
  });
  const day = date.toLocaleDateString("vi-VN");
  return `${time} ${day}`;
}

export function buildPaymentSuccessSpotlight(paymentInfo = {}) {
  const invoiceCode = paymentInfo?.ma_hoa_don || "";

  if (paymentInfo?.payment_status !== "paid") {
    return {
      visible: false,
      badge: "",
      title: "",
      summary: "",
      timestampLabel: "",
    };
  }

  const paidAtLabel = formatTimestamp(paymentInfo?.paid_at);
  const summary = invoiceCode
    ? `Hóa đơn ${invoiceCode} đã được hệ thống xác nhận thanh toán thành công và đối chiếu tự động.`
    : "Hóa đơn đã được hệ thống xác nhận thanh toán thành công và đối chiếu tự động.";

  return {
    visible: true,
    badge: "Đã xác nhận thành công",
    title: "Thanh toán đã hoàn tất",
    summary,
    timestampLabel: paidAtLabel,
  };
}
