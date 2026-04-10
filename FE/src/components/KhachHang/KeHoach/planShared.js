export const API_BASE = "/api";

export function buildHeaders(includeJson = false) {
  const headers = {
    Accept: "application/json",
  };

  if (includeJson) {
    headers["Content-Type"] = "application/json";
  }

  const token = localStorage.getItem("token");
  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }

  return headers;
}

export function chuanHoaDanhSach(duLieuPhanHoi) {
  if (Array.isArray(duLieuPhanHoi)) return duLieuPhanHoi;
  if (Array.isArray(duLieuPhanHoi?.data)) return duLieuPhanHoi.data;
  if (Array.isArray(duLieuPhanHoi?.data?.data)) return duLieuPhanHoi.data.data;
  if (Array.isArray(duLieuPhanHoi?.result)) return duLieuPhanHoi.result;
  return [];
}

export function normalizeRecord(duLieuPhanHoi) {
  return duLieuPhanHoi?.data?.data || duLieuPhanHoi?.data || duLieuPhanHoi || null;
}

export function getStoredUser() {
  try {
    const raw = localStorage.getItem("user");
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

export function getStoredCustomerId() {
  const user = getStoredUser();
  if (!user || typeof user !== "object") return "";

  return String(
    user.Ma_khach_hang ||
      user.ma_khach_hang ||
      user.id ||
      user.ID ||
      "",
  ).trim();
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

export function formatDateInput(value) {
  if (!value) return "";
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return String(value).slice(0, 10);
  }
  return date.toISOString().slice(0, 10);
}

export function formatCurrencyDisplay(value) {
  const amount = Number(value || 0);
  return `${amount.toLocaleString("vi-VN")} VNĐ`;
}

export function normalizeStatus(value) {
  const raw = Number(value);
  if (raw === 2) {
    return {
      value: 2,
      active: false,
      label: "Hoàn tất",
      tone: "slate",
    };
  }

  if (raw === 1) {
    return {
      value: 1,
      active: true,
      label: "Đang hoạt động",
      tone: "green",
    };
  }

  return {
    value: 0,
    active: false,
    label: "Tạm dừng",
    tone: "amber",
  };
}

export function mapGroup(item) {
  return {
    id: String(item?.Ma_nhom || item?.ma_nhom || item?.id || ""),
    name: item?.ten_nhom || item?.Ten_nhom || "Nhóm chưa xác định",
  };
}

export function mapMembershipToGroup(item) {
  const group = item?.nhom || item || {};
  const id = String(
    group?.Ma_nhom ||
      group?.ma_nhom ||
      item?.Ma_nhom ||
      item?.ma_nhom ||
      "",
  ).trim();

  return {
    id,
    name:
      group?.ten_nhom ||
      group?.Ten_nhom ||
      item?.ten_nhom ||
      item?.Ten_nhom ||
      "Nhóm chưa xác định",
  };
}

export function mapGroupsFromMembership(duLieuPhanHoi) {
  const rows = chuanHoaDanhSach(duLieuPhanHoi);
  const byId = new Map();

  rows.forEach((row) => {
    const group = mapMembershipToGroup(row);
    if (group.id && !byId.has(group.id)) {
      byId.set(group.id, group);
    }
  });

  return Array.from(byId.values());
}

export function mapPlan(item) {
  const status = normalizeStatus(item?.trang_thai);
  const budget = Number(
    item?.ngan_sach_du_kien ??
      item?.tong_chi_phi ??
      item?.Tong_chi_phi ??
      0,
  );

  const startDate = item?.ngay_bat_dau || item?.Ngay_bat_dau || "";
  const endDate = item?.ngay_ket_thuc || item?.Ngay_ket_thuc || "";

  let aiData = null;
  const rawAiData = item?.du_lieu_ai || item?.Du_lieu_ai;
  if (rawAiData) {
    try {
      aiData = typeof rawAiData === "string" ? JSON.parse(rawAiData) : rawAiData;
    } catch (e) {
      console.warn("Failed to parse du_lieu_ai", e);
    }
  }

  const rawSource = String(item?.nguon_tao || item?.Nguon_tao || "").trim().toLowerCase();
  const sourceFromBackend = rawSource === "ai" || rawSource === "manual" ? rawSource : "";
  const legacyAiHintText = `${item?.mo_ta || item?.Mo_ta || ""} ${item?.ten_ke_hoach || item?.Ten_ke_hoach || ""}`;
  const legacyAiHint = /ai planner|hành trình ai|hanh trinh ai/i.test(legacyAiHintText);
  const source = sourceFromBackend || (aiData || legacyAiHint ? "ai" : "manual");

  return {
    id: String(item?.ma_ke_hoach || item?.Ma_ke_hoach || item?.id || ""),
    name: item?.ten_ke_hoach || item?.Ten_ke_hoach || "Kế hoạch chưa đặt tên",
    description: item?.mo_ta || item?.Mo_ta || "",
    maNhom: String(item?.ma_nhom || item?.Ma_nhom || item?.nhom?.Ma_nhom || ""),
    tenNhom: item?.nhom?.ten_nhom || item?.ten_nhom || "Nhóm chưa xác định",
    soNguoi: Number(item?.so_nguoi || item?.So_nguoi || 0),
    budget,
    budgetLabel: formatCurrencyDisplay(budget),
    startDate,
    endDate,
    startDateLabel: formatDateDisplay(startDate),
    endDateLabel: formatDateDisplay(endDate),
    dateRangeLabel: `${formatDateDisplay(startDate)} - ${formatDateDisplay(endDate)}`,
    statusValue: status.value,
    statusLabel: status.label,
    statusTone: status.tone,
    isActive: status.active,
    updatedAtLabel: formatDateDisplay(item?.updated_at),
    createdAtLabel: formatDateDisplay(item?.created_at),
    source,
    isAiPlan: source === "ai",
    aiData,
    activities: (item?.hoat_dong_chi_tiets || []).map(ad => ({
      id: ad.ma_hoat_dong_chi_tiet,
      date: ad.ngay_cu_the,
      startTime: ad.gio_bat_dau,
      endTime: ad.gio_ket_thuc,
      locationId: ad.ma_dia_diem,
      locationName: ad.dia_diem?.ten_dia_diem || "Địa điểm chưa xác định",
      locationAddress: ad.dia_diem?.dia_chi || "",
      locationImage: ad.dia_diem?.hinh_anh || "",
      locationDesc: ad.dia_diem?.mo_ta || "",
      duration: ad.dia_diem?.thoi_gian_tham_quan || ""
    }))
  };
}

export function createEmptyPlanForm() {
  return {
    ma_ke_hoach: "",
    ma_nhom: "",
    ten_ke_hoach: "",
    so_nguoi: 1,
    ngay_bat_dau: "",
    ngay_ket_thuc: "",
    ngan_sach_du_kien: "",
    trang_thai: 1,
  };
}

export function mapPlanToForm(plan) {
  return {
    ma_ke_hoach: plan?.id || "",
    ma_nhom: plan?.maNhom || "",
    ten_ke_hoach: plan?.name || "",
    so_nguoi: plan?.soNguoi || 1,
    ngay_bat_dau: formatDateInput(plan?.startDate),
    ngay_ket_thuc: formatDateInput(plan?.endDate),
    ngan_sach_du_kien: plan?.budget ? String(plan.budget) : "",
    trang_thai: plan?.statusValue ?? 1,
  };
}

export function validatePlanForm(form, { requireCode = true } = {}) {
  const errors = {};

  if (requireCode && !String(form.ma_ke_hoach || "").trim()) {
    errors.ma_ke_hoach = "Mã kế hoạch là bắt buộc.";
  }

  if (!String(form.ma_nhom || "").trim()) {
    errors.ma_nhom = "Vui lòng chọn nhóm hành trình.";
  }

  if (!String(form.ten_ke_hoach || "").trim()) {
    errors.ten_ke_hoach = "Tên kế hoạch là bắt buộc.";
  }

  if (Number(form.so_nguoi) < 1) {
    errors.so_nguoi = "Số người phải lớn hơn hoặc bằng 1.";
  }

  if (!form.ngay_bat_dau) {
    errors.ngay_bat_dau = "Ngày bắt đầu là bắt buộc.";
  }

  if (!form.ngay_ket_thuc) {
    errors.ngay_ket_thuc = "Ngày kết thúc là bắt buộc.";
  }

  if (form.ngay_bat_dau && form.ngay_ket_thuc && form.ngay_ket_thuc < form.ngay_bat_dau) {
    errors.ngay_ket_thuc = "Ngày kết thúc phải sau ngày bắt đầu.";
  }

  if (form.ngan_sach_du_kien === "" || Number(form.ngan_sach_du_kien) < 0) {
    errors.ngan_sach_du_kien = "Ngân sách dự kiến phải lớn hơn hoặc bằng 0.";
  }

  return errors;
}
