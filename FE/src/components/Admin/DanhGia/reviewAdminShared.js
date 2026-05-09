export const API_BASE = "/api";

const REVIEW_GRADIENTS = [
  "linear-gradient(135deg, #4338ca 0%, #2563eb 100%)",
  "linear-gradient(135deg, #0f766e 0%, #14b8a6 100%)",
  "linear-gradient(135deg, #d97706 0%, #f97316 100%)",
  "linear-gradient(135deg, #be123c 0%, #ec4899 100%)",
];

export function normalizeCollection(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  if (Array.isArray(payload?.result)) return payload.result;
  return [];
}

export function normalizeRecord(payload) {
  return payload?.data?.data || payload?.data || payload || null;
}

export function normalizeReviewId(value) {
  return String(value || "").trim();
}

export function formatDateDisplay(value) {
  if (!value) return "--";
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return "--";
  return date.toLocaleDateString("vi-VN", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
}

export function formatDateTimeDisplay(value) {
  if (!value) return "--";
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return "--";
  return date.toLocaleString("vi-VN", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

export function buildInitials(name) {
  return String(name || "DG")
    .split(" ")
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase() || "")
    .join("");
}

export function gradientFromId(id) {
  const numericId = Number.parseInt(String(id || "").replace(/\D/g, ""), 10);
  const index = Number.isFinite(numericId) ? numericId % REVIEW_GRADIENTS.length : 0;
  return REVIEW_GRADIENTS[index];
}

export function buildStars(value) {
  const safeValue = Math.max(1, Math.min(5, Number(value) || 0));
  return `${"★".repeat(safeValue)}${"☆".repeat(5 - safeValue)}`;
}

export function previewContent(value, maxLength = 120) {
  const content = String(value || "").trim();
  if (!content) return "Chưa có nội dung đánh giá.";
  if (content.length <= maxLength) return content;
  return `${content.slice(0, maxLength).trim()}...`;
}

export function mapReview(item) {
  const id = String(item?.Ma_danh_gia ?? item?.id ?? "");
  const customerName = item?.khach_hang?.Ho_va_ten || "Khách hàng chưa xác định";
  const rating = Math.max(1, Math.min(5, Number(item?.so_sao) || 0));

  return {
    id,
    code: id ? `DG ${id}` : "--",
    customerId: String(item?.Ma_khach_hang ?? item?.khach_hang?.Ma_khach_hang ?? "--"),
    customerName,
    customerEmail: item?.khach_hang?.Email || "--",
    customerPhone: item?.khach_hang?.so_dien_thoai || "--",
    customerInitials: buildInitials(customerName),
    locationId: String(item?.ma_dia_diem ?? item?.dia_diem?.ma_dia_diem ?? "--"),
    locationName: item?.dia_diem?.ten_dia_diem || "Địa điểm chưa xác định",
    rating,
    ratingLabel: `${rating}/5 sao`,
    stars: buildStars(rating),
    content: String(item?.noi_dung || "").trim(),
    contentPreview: previewContent(item?.noi_dung),
    createdAtLabel: formatDateTimeDisplay(item?.created_at),
    updatedAtLabel: formatDateTimeDisplay(item?.updated_at),
    avatarGradient: gradientFromId(id),
  };
}

