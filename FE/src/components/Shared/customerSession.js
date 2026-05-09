import { goiApi } from "../../services/httpClient";
import authStorage from "../../services/authStorage";

export { API_BASE } from "../../services/httpClient";

export function buildHeaders(includeJson = false) {
  const headers = {
    Accept: "application/json",
  };

  if (includeJson) {
    headers["Content-Type"] = "application/json";
  }

  const currentRole = authStorage.getCurrentRoleFromPath();
  const token = authStorage.getToken(currentRole) || localStorage.getItem("token");
  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }

  return headers;
}

export function normalizeCollection(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  if (Array.isArray(payload?.result)) return payload.result;
  return [];
}

export const chuanHoaDanhSach = normalizeCollection;

export function getStoredUser(role) {
  return authStorage.getUser(role) || (role ? null : (function() {
    try {
      const raw = localStorage.getItem("user");
      return raw ? JSON.parse(raw) : null;
    } catch {
      return null;
    }
  })());
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

export function formatDateDisplay(value, includeTime = false) {
  if (!value) return "--";
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return "--";

  if (!includeTime) {
    return date.toLocaleDateString("vi-VN", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });
  }

  const day = date.toLocaleDateString("vi-VN", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
  const time = date.toLocaleTimeString("vi-VN", {
    hour: "2-digit",
    minute: "2-digit",
  });
  return `${time} ${day}`;
}

export function createInitials(name, fallback = "KH") {
  return (
    String(name || "")
      .split(" ")
      .filter(Boolean)
      .slice(0, 2)
      .map((part) => part[0]?.toUpperCase() || "")
      .join("") || fallback
  );
}

/**
 * Perform logout for the customer.
 */
export async function logoutCustomer() {
  const { authService } = await import("../../services/authService");
  return authService.logoutCustomer();
}

