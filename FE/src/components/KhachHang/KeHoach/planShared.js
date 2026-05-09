import {
  API_BASE,
  buildHeaders,
  chuanHoaDanhSach,
  getStoredUser,
  getStoredCustomerId,
  formatDateDisplay,
} from "../../Shared/customerSession";

export {
  API_BASE,
  buildHeaders,
  chuanHoaDanhSach,
  getStoredUser,
  getStoredCustomerId,
  formatDateDisplay,
};

export function normalizeRecord(duLieuPhanHoi) {
  return duLieuPhanHoi?.data?.data || duLieuPhanHoi?.data || duLieuPhanHoi || null;
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

function parseCurrencyValue(value) {
  if (value === null || value === undefined || value === "") return 0;
  if (typeof value === "number") return Number.isFinite(value) ? value : 0;

  const normalized = String(value)
    .replace(/[^\d,.-]/g, "")
    .replace(/\.(?=\d{3}(\D|$))/g, "")
    .replace(",", ".");

  const amount = Number(normalized);
  return Number.isFinite(amount) ? amount : 0;
}

function isActiveService(service = {}) {
  const status = service.trang_thai ?? service.status ?? service.active;
  return !(status === false || status === 0 || status === "0");
}

function normalizeServiceRows(services = []) {
  return (Array.isArray(services) ? services : [])
    .filter(isActiveService)
    .map((service) => {
      const amount = parseCurrencyValue(
        service.gia ??
          service.price ??
          service.gia_dich_vu ??
          service.cost ??
          0,
      );

      return {
        id: service.ma_dich_vu_dia_diem || service.id || service.ma_dich_vu || "",
        name:
          service.ten_dich_vu ||
          service.name ||
          service.ten ||
          "Dịch vụ địa điểm",
        amount,
        value: formatCurrencyDisplay(amount),
      };
    })
    .filter((service) => service.amount > 0);
}

export function extractActivityCost(activity = {}) {
  const location = activity.dia_diem || activity.location || {};
  const baseCost = parseCurrencyValue(
    activity.locationCost ??
      activity.gia_giao_dong ??
      activity.gia ??
      activity.price ??
      activity.baseCost ??
      location.gia_giao_dong ??
      0,
  );

  const services = normalizeServiceRows(
    activity.services ||
      activity.dichVuDiaDiems ||
      activity.dich_vu_dia_diems ||
      location.dichVuDiaDiems ||
      location.dich_vu_dia_diems ||
      [],
  );
  const serviceCost = services.reduce((sum, service) => sum + service.amount, 0);
  const totalCost = baseCost + serviceCost;

  return {
    baseCost,
    baseLabel: formatCurrencyDisplay(baseCost),
    serviceCost,
    serviceLabel: formatCurrencyDisplay(serviceCost),
    totalCost,
    totalLabel: formatCurrencyDisplay(totalCost),
    services,
    hasCost: totalCost > 0,
    note: totalCost > 0 ? "" : "Chưa có chi phí chi tiết cho hoạt động này",
  };
}

function flattenTimelineActivities(timeline = []) {
  const rows = [];

  (Array.isArray(timeline) ? timeline : []).forEach((day, dayIndex) => {
    (day.activities || []).forEach((activity) => {
      rows.push({
        dayIndex,
        dayTitle: day.title || `Ngày ${dayIndex + 1}`,
        dateRaw: day.dateRaw || activity.date || "",
        dateFormatted: day.dateFormatted || formatDateDisplay(day.dateRaw || activity.date),
        ...activity,
      });
    });
  });

  return rows;
}

export function buildBudgetDetails(plan = {}, timeline = []) {
  const plannedBudget = parseCurrencyValue(
    plan.budget ??
      plan.ngan_sach_du_kien ??
      plan.budgetLabel ??
      plan.formattedBudget ??
      0,
  );
  const people = Number(plan.soNguoi ?? plan.so_nguoi ?? plan.people ?? 0) || 0;
  const aiData = plan.aiData || plan.du_lieu_ai || {};
  const aiEstimatedTotal = parseCurrencyValue(
    aiData.tongChiPhi ??
      aiData.tong_chi_phi ??
      aiData.nganSach ??
      aiData.ngan_sach ??
      aiData.budget ??
      0,
  );

  const activityRows = flattenTimelineActivities(timeline).map((activity, index) => {
    const cost = extractActivityCost(activity);
    return {
      index,
      id: activity.id || activity.locationId || `${activity.dateRaw || "day"}-${index}`,
      name: activity.name || activity.locationName || activity.tieuDe || "Hoạt động chưa đặt tên",
      time: activity.time || activity.timeLabel || activity.thoiGian || "",
      dateFormatted: activity.dateFormatted || formatDateDisplay(activity.dateRaw),
      dayTitle: activity.dayTitle,
      baseCost: cost.baseCost,
      baseLabel: cost.baseLabel,
      serviceCost: cost.serviceCost,
      serviceLabel: cost.serviceLabel,
      totalCost: cost.totalCost,
      totalLabel: cost.totalLabel,
      services: cost.services,
      note: cost.note,
      hasCost: cost.hasCost,
    };
  });

  const estimatedActivityTotal = activityRows.reduce((sum, row) => sum + row.totalCost, 0);
  // Ưu tiên lấy con số lớn hơn giữa AI ước tính và tổng chi tiết (vì nhiều điểm AI sinh ra chưa có giá chi tiết trong DB)
  const effectiveTotal = (aiEstimatedTotal > estimatedActivityTotal) ? aiEstimatedTotal : (estimatedActivityTotal || aiEstimatedTotal || 0);
  const remaining = plannedBudget - effectiveTotal;
  const isOverBudget = plannedBudget > 0 && remaining < 0;
  const perPersonEstimate = people > 0 ? Math.round(effectiveTotal / people) : 0;
  const missingCostCount = activityRows.filter((row) => !row.hasCost).length;

  const rows = [
    { label: "Ngân sách dự kiến", value: formatCurrencyDisplay(plannedBudget) },
    ...(aiEstimatedTotal
      ? [{ label: "Ước tính từ AI", value: formatCurrencyDisplay(aiEstimatedTotal) }]
      : []),
    {
      label: "Ước tính theo hoạt động",
      value: formatCurrencyDisplay(estimatedActivityTotal),
    },
    { label: "Số người", value: people > 0 ? `${people} người` : "Chưa có" },
    {
      label: "Ước tính mỗi người",
      value: formatCurrencyDisplay(perPersonEstimate),
    },
    {
      label: isOverBudget ? "Vượt ngân sách" : "Còn lại so với ngân sách",
      value: formatCurrencyDisplay(Math.abs(remaining)),
      tone: isOverBudget ? "danger" : "success",
    },
  ];

  return {
    plannedBudget,
    plannedBudgetLabel: formatCurrencyDisplay(plannedBudget),
    people,
    aiEstimatedTotal,
    aiEstimatedTotalLabel: formatCurrencyDisplay(aiEstimatedTotal),
    estimatedActivityTotal,
    estimatedActivityTotalLabel: formatCurrencyDisplay(estimatedActivityTotal),
    effectiveTotal,
    effectiveTotalLabel: formatCurrencyDisplay(effectiveTotal),
    remaining,
    remainingLabel: formatCurrencyDisplay(Math.abs(remaining)),
    isOverBudget,
    perPersonEstimate,
    perPersonEstimateLabel: formatCurrencyDisplay(perPersonEstimate),
    missingCostCount,
    rows,
    activityRows,
  };
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

function toFiniteNumber(value) {
  if (value === null || value === undefined || value === "") return null;
  const numericValue = Number(value);
  return Number.isFinite(numericValue) ? numericValue : null;
}

export function buildCoordinatesFromLocation(location = {}) {
  const lat = toFiniteNumber(location?.vi_do ?? location?.latitude ?? location?.lat);
  const lng = toFiniteNumber(location?.kinh_do ?? location?.longitude ?? location?.lng);

  if (lat === null || lng === null) return null;
  if (lat < -90 || lat > 90 || lng < -180 || lng > 180) return null;

  return { lat, lng };
}

function normalizeDateKey(value) {
  const normalized = formatDateInput(value);
  return normalized || String(value || "").slice(0, 10);
}

function dateRange(startDate, endDate) {
  const start = normalizeDateKey(startDate);
  const end = normalizeDateKey(endDate);
  if (!start || !end) return [];

  const startTime = Date.parse(`${start}T00:00:00Z`);
  const endTime = Date.parse(`${end}T00:00:00Z`);
  if (!Number.isFinite(startTime) || !Number.isFinite(endTime) || endTime < startTime) {
    return [];
  }

  const days = [];
  const current = new Date(startTime);
  const last = new Date(endTime);

  while (current <= last && days.length < 31) {
    days.push(current.toISOString().slice(0, 10));
    current.setUTCDate(current.getUTCDate() + 1);
  }

  return days;
}

function formatTime(value) {
  const raw = String(value || "").trim();
  const match = raw.match(/(\d{1,2}):(\d{2})/);
  if (!match) return "";
  return `${match[1].padStart(2, "0")}:${match[2]}`;
}

function defaultTimeForSession(value) {
  const raw = String(value || "").trim().toUpperCase();
  if (raw.includes("TRUA") || raw.includes("TRƯA") || raw.includes("CHIEU") || raw.includes("CHIỀU")) {
    return "12:00";
  }
  if (raw.includes("TOI") || raw.includes("TỐI")) {
    return "19:00";
  }
  return "08:00";
}

function addHoursToTime(value, hours = 2) {
  const time = formatTime(value) || "08:00";
  const [hour, minute] = time.split(":").map(Number);
  const date = new Date(Date.UTC(2000, 0, 1, hour, minute));
  date.setUTCHours(date.getUTCHours() + hours);
  return date.toISOString().slice(11, 16);
}

function commonTimelineActivity(activity) {
  const startTime = formatTime(activity.startTime);
  const endTime = formatTime(activity.endTime);
  const timeLabel = activity.timeLabel || startTime || "TRONG NGAY";
  const time = activity.time || (startTime && endTime ? `${startTime} - ${endTime}` : startTime);
  const coordinates = activity.coordinates || buildCoordinatesFromLocation(activity);

  return {
    ...activity,
    startTime,
    endTime,
    timeLabel,
    time,
    name: activity.name || activity.locationName || "",
    description: activity.description || activity.locationDesc || activity.locationAddress || "",
    note: activity.note || activity.ghi_chu || "",
    image: activity.image || activity.locationImage || "",
    locationCost: activity.locationCost ?? activity.gia_giao_dong ?? activity.gia ?? null,
    services: activity.services || activity.dichVuDiaDiems || activity.dich_vu_dia_diems || [],
    coordinates,
  };
}

function persistedActivityToTimeline(activity) {
  return commonTimelineActivity({
    ...activity,
    source: "persisted",
    canEdit: Boolean(activity.id),
    date: normalizeDateKey(activity.date),
    startTime: activity.startTime,
    endTime: activity.endTime,
    locationName: activity.locationName,
    locationAddress: activity.locationAddress,
    locationImage: activity.locationImage,
    locationDesc: activity.locationDesc,
    locationCost: activity.locationCost,
    services: activity.services,
    kinh_do: activity.kinh_do,
    vi_do: activity.vi_do,
  });
}

function getAiDays(aiData = {}) {
  const raw = aiData?.lichTrinh || aiData?.lich_trinh || aiData?.itinerary || [];
  if (Array.isArray(raw)) return raw;
  if (raw && typeof raw === "object") return Object.values(raw);
  return [];
}

function getAiDayActivities(day = {}) {
  const raw =
    day?.danhSachHoatDong ||
    day?.danh_sach_hoat_dong ||
    day?.hoat_dong ||
    day?.activities ||
    [];
  if (Array.isArray(raw)) return raw;
  if (raw && typeof raw === "object") return Object.values(raw);
  return [];
}

function aiActivityToTimeline(activity, dateRaw) {
  const session = activity?.buoi || activity?.session || "";
  const startTime = formatTime(activity?.thoiGian || activity?.thoi_gian || activity?.time) || defaultTimeForSession(session);
  const endTime = formatTime(activity?.gio_ket_thuc || activity?.endTime) || addHoursToTime(startTime);
  const name =
    activity?.tieuDe ||
    activity?.tieu_de ||
    activity?.ten_dia_diem ||
    activity?.name ||
    "";

  return commonTimelineActivity({
    id: activity?.ma_hoat_dong_chi_tiet || null,
    source: "ai",
    canEdit: false,
    date: dateRaw,
    startTime,
    endTime,
    timeLabel: session || startTime || "TRONG NGAY",
    locationId: activity?.ma_dia_diem || null,
    locationName: name,
    locationAddress: activity?.dia_chi || activity?.address || "",
    locationImage: activity?.hinhanh || activity?.hinh_anh || activity?.hinhAnh || activity?.image || "",
    locationDesc: activity?.moTa || activity?.mo_ta || activity?.hoat_dong || activity?.activity || "",
    locationCost: activity?.gia ?? activity?.gia_uoc_luong ?? activity?.price ?? null,
    services: activity?.dich_vu_dia_diems || activity?.dichVuDiaDiems || activity?.services || [],
    kinh_do: activity?.kinh_do ?? null,
    vi_do: activity?.vi_do ?? null,
  });
}

function buildTimelineFromGroupedActivities(plan, activitiesByDate, fallbackDates) {
  const allDates = new Set(fallbackDates);
  Object.keys(activitiesByDate).forEach((date) => allDates.add(date));

  const dates = Array.from(allDates).sort();
  if (dates.length === 0) {
    return [];
  }

  return dates.map((dateRaw, index) => ({
    dateRaw,
    title: `Ngày ${index + 1}`,
    dateFormatted: formatDateDisplay(dateRaw),
    activities: (activitiesByDate[dateRaw] || []).sort((a, b) =>
      String(a.startTime || "").localeCompare(String(b.startTime || "")),
    ),
  }));
}

export function buildPlanTimeline(plan) {
  if (!plan) return [];

  const fallbackDates = dateRange(plan.startDate, plan.endDate);
  const persistedActivities = Array.isArray(plan.activities) ? plan.activities : [];

  if (persistedActivities.length > 0) {
    const groups = {};
    persistedActivities.forEach((activity) => {
      const date = normalizeDateKey(activity.date) || fallbackDates[0] || "Khác";
      if (!groups[date]) groups[date] = [];
      groups[date].push(persistedActivityToTimeline({ ...activity, date }));
    });
    return buildTimelineFromGroupedActivities(plan, groups, fallbackDates);
  }

  const aiDays = getAiDays(plan.aiData);
  if (aiDays.length > 0) {
    return aiDays.map((day, index) => {
      const dateRaw = normalizeDateKey(day?.ngay_cu_the) || fallbackDates[index] || "";
      const activities = getAiDayActivities(day).map((activity) =>
        aiActivityToTimeline(activity, dateRaw),
      );

      return {
        dateRaw,
        title: day?.tieuDe || day?.tieu_de || day?.title || `Ngày ${index + 1}`,
        dateFormatted: formatDateDisplay(dateRaw),
        activities: activities.sort((a, b) =>
          String(a.startTime || "").localeCompare(String(b.startTime || "")),
        ),
      };
    });
  }

  return fallbackDates.map((dateRaw, index) => ({
    dateRaw,
    title: `Ngày ${index + 1}`,
    dateFormatted: formatDateDisplay(dateRaw),
    activities: [],
  }));
}

export function buildPlanMapActivities(timeline = []) {
  const activities = [];

  timeline.forEach((day) => {
    (day.activities || []).forEach((activity) => {
      const coordinates = activity.coordinates || buildCoordinatesFromLocation(activity);
      if (!coordinates) return;

      activities.push({
        tieuDe: activity.name || activity.locationName || "",
        moTa: activity.description || activity.locationDesc || activity.locationAddress || "",
        thoiGian: activity.timeLabel || activity.time || "",
        kinh_do: coordinates.lng,
        vi_do: coordinates.lat,
        coordinates,
      });
    });
  });

  return activities;
}

export function mapPlan(item) {
  const status = normalizeStatus(item?.trang_thai);
  
  const plannedBudget = Number(item?.ngan_sach_du_kien ?? 0);
  const actualCost = Number(item?.tong_chi_phi ?? item?.Tong_chi_phi ?? 0);
  
  // Ưu tiên hiển thị chi phí thực tế nếu nó > 0 (nghĩa là đã được tính toán), 
  // ngược lại dùng ngân sách dự kiến.
  const displayBudget = (actualCost > 0) ? actualCost : plannedBudget;

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
    budget: displayBudget,
    plannedBudget,
    actualCost,
    budgetLabel: formatCurrencyDisplay(displayBudget),
    plannedBudgetLabel: formatCurrencyDisplay(plannedBudget),
    actualCostLabel: formatCurrencyDisplay(actualCost),
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
      locationDesc: ad.ghi_chu || ad.dia_diem?.mo_ta || "",
      ghi_chu: ad.ghi_chu || "",
      duration: ad.dia_diem?.thoi_gian_tham_quan || "",
      locationCost: ad.dia_diem?.gia_giao_dong ?? null,
      services: ad.dia_diem?.dich_vu_dia_diems || ad.dia_diem?.dichVuDiaDiems || [],
      kinh_do: ad.dia_diem?.kinh_do || null,
      vi_do: ad.dia_diem?.vi_do || null,
      tourId: ad.ma_tour || null,
      scheduleId: ad.ma_thoi_gian_tour || null,
      coordinates: buildCoordinatesFromLocation(ad.dia_diem || {}),
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
