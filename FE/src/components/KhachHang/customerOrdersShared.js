const CUSTOMER_CANCELLED_REFERENCE = "customer_cancelled";

export const CUSTOMER_ORDER_FILTERS = [
  { key: "all", label: "Tất cả" },
  { key: "pending", label: "Chờ thanh toán" },
  { key: "paid", label: "Đã thanh toán" },
  { key: "expired", label: "Hết hạn" },
  { key: "failed", label: "Thất bại / hủy" },
];

export function normalizeCustomerPaymentStatus(value) {
  const status = String(value || "").trim().toLowerCase();
  if (["pending", "paid", "expired", "failed"].includes(status)) {
    return status;
  }
  return "pending";
}

export function isCustomerCancelled(order = {}) {
  return (
    normalizeCustomerPaymentStatus(order?.payment_status) === "failed" &&
    String(order?.payment_reference || "").trim().toLowerCase() === CUSTOMER_CANCELLED_REFERENCE
  );
}

export function getCustomerOrderStatusMeta(order = {}) {
  const normalized = normalizeCustomerPaymentStatus(order?.payment_status);

  if (normalized === "paid") {
    return {
      key: "paid",
      tone: "paid",
      label: "Đã thanh toán",
      actionLabel: "Xem hóa đơn",
    };
  }

  if (normalized === "expired") {
    return {
      key: "expired",
      tone: "expired",
      label: "Đã hết hạn",
      actionLabel: "Thanh toán lại",
    };
  }

  if (normalized === "failed") {
    if (isCustomerCancelled(order)) {
      return {
        key: "failed",
        tone: "failed",
        label: "Đã hủy",
        actionLabel: "Tạo mã QR mới",
      };
    }

    return {
      key: "failed",
      tone: "failed",
      label: "Thất bại",
      actionLabel: "Thanh toán lại",
    };
  }

  return {
    key: "pending",
    tone: "pending",
    label: "Đang chờ thanh toán",
    actionLabel: "Xem QR",
  };
}

function getComparableTimestamp(order) {
  const raw = order?.created_at || order?.updated_at || "";
  const timestamp = raw ? new Date(raw).getTime() : 0;
  return Number.isFinite(timestamp) ? timestamp : 0;
}

export function sortCustomerOrdersByCreatedAtDesc(orders = []) {
  return [...orders].sort((left, right) => {
    const byTime = getComparableTimestamp(right) - getComparableTimestamp(left);
    if (byTime !== 0) return byTime;
    return String(right?.ma_hoa_don || "").localeCompare(String(left?.ma_hoa_don || ""));
  });
}

export function filterCustomerOrdersByStatus(orders = [], filterKey = "all") {
  const normalizedFilter = String(filterKey || "all").trim().toLowerCase();
  if (!normalizedFilter || normalizedFilter === "all") {
    return [...orders];
  }

  return orders.filter((order) => normalizeCustomerPaymentStatus(order?.payment_status) === normalizedFilter);
}

export function selectCustomerResumeOrder(orders = []) {
  const sorted = sortCustomerOrdersByCreatedAtDesc(orders);

  return (
    sorted.find((order) => normalizeCustomerPaymentStatus(order?.payment_status) === "pending") ||
    sorted.find((order) => normalizeCustomerPaymentStatus(order?.payment_status) === "expired") ||
    sorted.find((order) => normalizeCustomerPaymentStatus(order?.payment_status) === "failed") ||
    null
  );
}

export function buildCustomerPaymentRoute(order) {
  const maTour = String(order?.ma_tour || "").trim();
  const maThoiGianTour = String(order?.ma_thoi_gian_tour || "").trim();
  const maHoaDon = String(order?.ma_hoa_don || "").trim();

  if (!maTour || !maThoiGianTour) return null;

  const query = {
    schedule: maThoiGianTour,
  };

  if (maHoaDon) {
    query.invoice = maHoaDon;
  }

  return {
    path: `/khach-hang/tour/${maTour}/thanh-toan`,
    query,
  };
}

export function buildCustomerInvoiceDetailRoute(order) {
  const maHoaDon = String(order?.ma_hoa_don || "").trim();
  if (!maHoaDon) return null;

  return {
    path: `/khach-hang/hoa-don/${maHoaDon}`,
  };
}

export function buildCustomerOrderActionRoute(order) {
  const status = normalizeCustomerPaymentStatus(order?.payment_status);
  if (status === "paid") {
    return buildCustomerInvoiceDetailRoute(order);
  }

  return buildCustomerPaymentRoute(order);
}

export function buildCustomerDashboardSummary({ orders = [], plans = [], groups = [] } = {}) {
  const pendingOrders = filterCustomerOrdersByStatus(orders, "pending").length;
  const paidOrders = filterCustomerOrdersByStatus(orders, "paid").length;

  return {
    stats: {
      pendingOrders,
      paidOrders,
      planCount: Array.isArray(plans) ? plans.length : 0,
      groupCount: Array.isArray(groups) ? groups.length : 0,
    },
    resumeOrder: selectCustomerResumeOrder(orders),
  };
}

export function enrichCustomerOrder(order = {}) {
  const status = getCustomerOrderStatusMeta(order);
  return {
    ...order,
    payment_status: status.key,
    statusTone: status.tone,
    statusLabel: status.label,
    actionLabel: status.actionLabel,
    actionRoute: buildCustomerOrderActionRoute(order),
    paymentRoute: buildCustomerPaymentRoute(order),
    invoiceRoute: buildCustomerInvoiceDetailRoute(order),
    isCancelled: isCustomerCancelled(order),
  };
}
