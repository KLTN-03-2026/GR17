export const API_BASE = "/api";

export const DAY_TYPE_OPTIONS = [
  { value: 1, label: "Ngày lễ chính thức", shortLabel: "Chính thức", accent: "blue" },
  { value: 2, label: "Ngày kỷ niệm", shortLabel: "Kỷ niệm", accent: "amber" },
  { value: 3, label: "Ngày đặc biệt", shortLabel: "Đặc biệt", accent: "violet" },
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

export function formatDateForInput(value) {
  if (!value) return "";
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return "";
  const year = date.getFullYear();
  const month = `${date.getMonth() + 1}`.padStart(2, "0");
  const day = `${date.getDate()}`.padStart(2, "0");
  return `${year}-${month}-${day}`;
}

export function getDayTypeMeta(value) {
  return DAY_TYPE_OPTIONS.find((option) => option.value === Number(value)) || DAY_TYPE_OPTIONS[0];
}

export function mapDayConfig(item) {
  const typeMeta = getDayTypeMeta(item?.loai_ngay_le);
  const rawDate = item?.ngay || "";
  const date = formatDateForInput(rawDate);
  const eventName = String(item?.ten_ngay_le || "").trim();

  return {
    id: String(item?.ma_cau_hinh_ngay ?? item?.id ?? ""),
    code: item?.ma_cau_hinh_ngay ? `CHN ${item.ma_cau_hinh_ngay}` : "--",
    type: Number(item?.loai_ngay_le) || typeMeta.value,
    typeLabel: typeMeta.label,
    typeShortLabel: typeMeta.shortLabel,
    typeAccent: typeMeta.accent,
    name: eventName,
    nameLabel: eventName || "Chưa đặt tên ngày",
    date,
    dateLabel: formatDateDisplay(rawDate),
    createdAtLabel: formatDateTimeDisplay(item?.created_at),
    updatedAtLabel: formatDateTimeDisplay(item?.updated_at),
  };
}

