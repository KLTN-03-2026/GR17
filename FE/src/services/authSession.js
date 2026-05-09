const SESSION_KEYS = Object.freeze({
  token: "token",
  user: "user",
  authType: "auth_type",
  preferredLocale: "preferred_locale",
});

function getStorage(storage) {
  if (storage && typeof storage.getItem === "function") {
    return storage;
  }

  if (typeof window !== "undefined" && window.localStorage) {
    return window.localStorage;
  }

  return null;
}

function safeParseUser(rawUser) {
  if (!rawUser) return null;

  try {
    return JSON.parse(rawUser);
  } catch {
    return null;
  }
}

function dispatchSessionEvent() {
  if (typeof window === "undefined") return;

  window.dispatchEvent(new Event("storage"));
  window.dispatchEvent(new CustomEvent("auth-session-changed"));
}

export function inferAuthType(user, explicitType = "") {
  const normalizedExplicitType = String(explicitType || "").toLowerCase().trim();
  if (normalizedExplicitType) return normalizedExplicitType;

  if (user?.Ma_doi_tac || user?.ma_doi_tac) return "partner";
  if (user?.Ma_quan_tri || user?.ma_quan_tri || user?.Vai_tro === "admin") return "admin";
  if (user?.Ma_khach_hang || user?.ma_khach_hang) return "customer";

  return "";
}

export function readAuthSession(storage) {
  const activeStorage = getStorage(storage);
  if (!activeStorage) {
    return {
      token: "",
      authType: "",
      user: null,
      isAuthenticated: false,
    };
  }

  const token = String(activeStorage.getItem(SESSION_KEYS.token) || "").trim();
  const user = safeParseUser(activeStorage.getItem(SESSION_KEYS.user));
  const authType = inferAuthType(user, activeStorage.getItem(SESSION_KEYS.authType));

  return {
    token,
    authType,
    user,
    isAuthenticated: Boolean(token),
  };
}

export function getAuthToken(storage) {
  return readAuthSession(storage).token;
}

export function getAuthType(storage) {
  return readAuthSession(storage).authType;
}

export function getSessionUser(storage) {
  return readAuthSession(storage).user;
}

export function getStoredCustomerId(storage) {
  const user = getSessionUser(storage);
  if (!user || typeof user !== "object") return "";

  return String(user.Ma_khach_hang || user.ma_khach_hang || user.id || user.ID || "").trim();
}

export function saveAuthSession({ token = "", user = null, authType = "", type = "" } = {}, storage) {
  const activeStorage = getStorage(storage);
  if (!activeStorage) return;

  const normalizedToken = String(token || "").trim();
  const normalizedAuthType = inferAuthType(user, authType || type);

  if (normalizedToken) {
    activeStorage.setItem(SESSION_KEYS.token, normalizedToken);
  } else {
    activeStorage.removeItem(SESSION_KEYS.token);
  }

  if (normalizedAuthType) {
    activeStorage.setItem(SESSION_KEYS.authType, normalizedAuthType);
  } else {
    activeStorage.removeItem(SESSION_KEYS.authType);
  }

  if (user && typeof user === "object") {
    activeStorage.setItem(SESSION_KEYS.user, JSON.stringify(user));
  } else {
    activeStorage.removeItem(SESSION_KEYS.user);
  }

  dispatchSessionEvent();
}

export function clearAuthSession(storage) {
  const activeStorage = getStorage(storage);
  if (!activeStorage) return;

  activeStorage.removeItem(SESSION_KEYS.token);
  activeStorage.removeItem(SESSION_KEYS.user);
  activeStorage.removeItem(SESSION_KEYS.authType);

  dispatchSessionEvent();
}

export function getPreferredLocale(storage) {
  const activeStorage = getStorage(storage);
  const locale = String(activeStorage?.getItem(SESSION_KEYS.preferredLocale) || "").toLowerCase();
  return locale === "en" ? "en" : "vi";
}

export function setPreferredLocale(locale, storage) {
  const activeStorage = getStorage(storage);
  if (!activeStorage) return;

  activeStorage.setItem(SESSION_KEYS.preferredLocale, locale === "en" ? "en" : "vi");
  dispatchSessionEvent();
}

export function buildAuthHeaders(includeJson = false, storage) {
  const { token, authType } = readAuthSession(storage);
  const headers = {
    Accept: "application/json",
  };

  if (includeJson) {
    headers["Content-Type"] = "application/json";
  }

  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }

  if (authType) {
    headers["X-Auth-Type"] = authType;
  }

  return headers;
}

export { SESSION_KEYS };
