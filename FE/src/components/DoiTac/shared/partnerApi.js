import { goiApi } from "../../../services/httpClient";

function collectValidationErrors(errors) {
  if (!errors || typeof errors !== "object") return "";

  const messages = Object.values(errors)
    .flatMap((value) => (Array.isArray(value) ? value : [value]))
    .map((value) => String(value || "").trim())
    .filter(Boolean);

  if (!messages.length) return "";
  return messages.join("\n");
}

function resolveApiErrorMessage(payload, fallbackMessage) {
  const validationMessage = collectValidationErrors(payload?.errors);
  if (validationMessage) return validationMessage;

  const primaryMessage = String(payload?.message || "").trim();
  if (primaryMessage) return primaryMessage;

  return fallbackMessage;
}

async function requestJson(url, init = {}, fallbackMessage = "Có lỗi xảy ra.") {
  const response = await goiApi(url, {
    headers: {
      Accept: "application/json",
      ...(init.headers || {}),
    },
    ...init,
  });

  const payload = await response.json().catch(() => ({}));

  if (!response.ok) {
    throw new Error(resolveApiErrorMessage(payload, fallbackMessage));
  }

  return payload;
}

export function mapStatusLabel(status) {
  const key = String(status || "").toLowerCase();
  if (key === "pending_approval") return "Chờ duyệt";
  if (key === "approved") return "Đã duyệt";
  if (key === "rejected") return "Từ chối";
  if (key === "draft") return "Bản nháp";
  return "Không xác định";
}

export function mapStatusTone(status) {
  const key = String(status || "").toLowerCase();
  if (key === "approved") return "approved";
  if (key === "rejected") return "rejected";
  if (key === "pending_approval") return "pending";
  return "draft";
}

export function formatCurrency(value) {
  const number = Number(value || 0);
  if (Number.isNaN(number)) return "0 đ";
  return `${number.toLocaleString("vi-VN")} đ`;
}

export function formatDateTime(value) {
  if (!value) return "-";
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return "-";
  return date.toLocaleString("vi-VN");
}

export function parseCollection(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
}

export async function fetchPartnerSession() {
  const payload = await requestJson("/api/doi-tac/check-login", {}, "Phiên đăng nhập đối tác không hợp lệ.");
  return payload?.data || null;
}

export async function fetchPartnerTours() {
  const payload = await requestJson("/api/doi-tac/tour", {}, "Không tải được danh sách tour.");
  return parseCollection(payload);
}

export async function fetchPartnerTour(maTour) {
  const payload = await requestJson(`/api/doi-tac/tour/${maTour}`, {}, "Không tải được thông tin tour.");
  return payload?.data || null;
}

export async function createPartnerTour(formData) {
  const payload = await requestJson(
    "/api/doi-tac/tour",
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(formData),
    },
    "Không thể tạo tour mới."
  );
  return payload?.data || null;
}

export async function updatePartnerTour(maTour, formData) {
  const payload = await requestJson(
    `/api/doi-tac/tour/${maTour}`,
    {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(formData),
    },
    "Không thể cập nhật tour."
  );
  return payload?.data || null;
}

export async function deletePartnerTour(maTour) {
  await requestJson(
    `/api/doi-tac/tour/${maTour}`,
    { method: "DELETE" },
    "Không thể xóa tour."
  );
}

export async function submitPartnerTour(maTour) {
  const payload = await requestJson(
    `/api/doi-tac/tour/${maTour}/submit`,
    { method: "POST" },
    "Không thể gửi duyệt tour."
  );
  return payload?.data || null;
}

export async function fetchPartnerLocations() {
  const payload = await requestJson("/api/doi-tac/dia-diem", {}, "Không tải được danh sách địa điểm.");
  return parseCollection(payload);
}

export async function createPartnerLocation(formData) {
  return requestJson(
    "/api/doi-tac/dia-diem",
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(formData),
    },
    "Không thể tạo địa điểm."
  );
}

export async function updatePartnerLocation(maDiaDiem, formData) {
  return requestJson(
    `/api/doi-tac/dia-diem/${maDiaDiem}`,
    {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(formData),
    },
    "Không thể cập nhật địa điểm."
  );
}

export async function deletePartnerLocation(maDiaDiem) {
  await requestJson(
    `/api/doi-tac/dia-diem/${maDiaDiem}`,
    { method: "DELETE" },
    "Không thể xóa địa điểm."
  );
}

export async function fetchTourItinerary(maTour) {
  const payload = await requestJson(
    `/api/doi-tac/tour/${maTour}/dia-diem`,
    {},
    "Không tải được hành trình tour."
  );
  return parseCollection(payload);
}

export async function attachExistingLocationToTour(maTour, formData) {
  const payload = await requestJson(
    `/api/doi-tac/tour/${maTour}/dia-diem/existing`,
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(formData),
    },
    "Không thể gắn địa điểm có sẵn vào tour."
  );
  return payload?.data || null;
}

export async function createAndAttachLocationToTour(maTour, formData) {
  const payload = await requestJson(
    `/api/doi-tac/tour/${maTour}/dia-diem/create-and-attach`,
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(formData),
    },
    "Không thể tạo địa điểm mới cho hành trình."
  );
  return payload?.data || null;
}

export async function updateTourItineraryItem(maTour, maChiTietTour, formData) {
  const payload = await requestJson(
    `/api/doi-tac/tour/${maTour}/dia-diem/${maChiTietTour}`,
    {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(formData),
    },
    "Không thể cập nhật hành trình."
  );
  return payload?.data || null;
}

export async function removeTourItineraryItem(maTour, maChiTietTour) {
  await requestJson(
    `/api/doi-tac/tour/${maTour}/dia-diem/${maChiTietTour}`,
    { method: "DELETE" },
    "Không thể xóa địa điểm khỏi hành trình."
  );
}
