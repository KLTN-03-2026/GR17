import axios from "axios";

const VITE_DEV_PORTS = new Set(["3000", "3001", "4173", "4174", "5173", "5174"]);

function giaiMaPathAnToan(segment = "") {
  try {
    return decodeURIComponent(segment);
  } catch {
    return segment;
  }
}

function taoPathDaMaHoaMacDinh(segments = []) {
  const danhSach = (Array.isArray(segments) ? segments : [])
    .map((item) => String(item || "").trim())
    .filter(Boolean)
    .map((item) => encodeURIComponent(item));

  if (!danhSach.length) return "";
  return `/${danhSach.join("/")}`;
}

function timApiApacheMacDinh() {
  if (typeof window === "undefined") return "";

  const { origin, pathname } = window.location;
  const segmentsDecoded = String(pathname || "")
    .split("/")
    .map((item) => item.trim())
    .filter(Boolean)
    .map(giaiMaPathAnToan);

  const indexFe = segmentsDecoded.findIndex(
    (segment) => segment.toLowerCase() === "doantotnghiepfe",
  );
  const indexBe = segmentsDecoded.findIndex(
    (segment) => segment.toLowerCase() === "doantotnghiepbe",
  );

  const layBaseTheoIndex = (index) => {
    if (index < 0) return "";
    const tienTo = segmentsDecoded.slice(0, index);
    const duongDan = taoPathDaMaHoaMacDinh([...tienTo, "DoAnTotNghiepBE", "public", "api"]);
    if (!duongDan) return "";
    return `${origin}${duongDan}`;
  };

  return layBaseTheoIndex(indexFe) || layBaseTheoIndex(indexBe);
}

function layApiMacDinh() {
  if (typeof window === "undefined") {
    return "/api";
  }

  const { origin, port } = window.location;

  if (VITE_DEV_PORTS.has(String(port || ""))) {
    return "/api";
  }

  const apiApacheMacDinh = timApiApacheMacDinh();
  if (apiApacheMacDinh) {
    return apiApacheMacDinh;
  }

  return `${origin}/api`;
}

const API_MAC_DINH = layApiMacDinh();

function boDauGachCheoCuoi(url = "") {
  return String(url || "").replace(/\/+$/, "");
}

export const API_BASE = boDauGachCheoCuoi(import.meta.env.VITE_API_BASE_URL || API_MAC_DINH);
export const API_ORIGIN = API_BASE.replace(/\/api$/i, "");
const API_CACHE_TTL_MAC_DINH = Number(import.meta.env.VITE_API_CACHE_TTL_MS || 15000);
const API_CACHE_TOI_DA = Math.max(10, Number(import.meta.env.VITE_API_CACHE_MAX_ENTRIES || 200));
const boNhoDemApi = new Map();
const yeuCauDangCho = new Map();

function laDuongDanTuyetDoi(url = "") {
  return /^https?:\/\//i.test(String(url));
}

function laApiBackend(url = "") {
  const duongDan = String(url || "");
  if (!duongDan) return false;

  if (duongDan.startsWith("/api") || duongDan.startsWith("api/")) {
    return true;
  }

  return (
    duongDan.includes("127.0.0.1:8000/api") ||
    duongDan.includes("localhost:8000/api") ||
    duongDan.startsWith(API_BASE)
  );
}

function laLoiMangAxios(error) {
  if (!error) return false;
  if (error.code === "ERR_NETWORK") return true;
  if (error.code === "ECONNREFUSED") return true;
  return !error.response;
}

function laPhuongThucDoc(method = "GET") {
  const phuongThuc = String(method || "GET").toUpperCase();
  return phuongThuc === "GET" || phuongThuc === "HEAD";
}

function saoChepSau(value) {
  if (value === undefined || value === null) return value;
  if (typeof structuredClone === "function") {
    try {
      return structuredClone(value);
    } catch {
      // fallback JSON clone below
    }
  }

  if (typeof value === "string" || typeof value === "number" || typeof value === "boolean") {
    return value;
  }

  try {
    return JSON.parse(JSON.stringify(value));
  } catch {
    return value;
  }
}

function chuanHoaQueryParams(params) {
  if (!params) return "";
  const boThamSo = new URLSearchParams();

  const themThamSo = (key, value) => {
    if (value === undefined || value === null) return;
    if (Array.isArray(value)) {
      value.forEach((item) => themThamSo(key, item));
      return;
    }
    if (typeof value === "object") {
      boThamSo.append(key, JSON.stringify(value));
      return;
    }
    boThamSo.append(key, String(value));
  };

  Object.keys(params)
    .sort()
    .forEach((key) => {
      themThamSo(key, params[key]);
    });

  return boThamSo.toString();
}

function taoKhoaYeuCau({ method, urlRaw, params, authHeader = "", authType = "" }) {
  return [
    String(method || "GET").toUpperCase(),
    String(urlRaw || ""),
    chuanHoaQueryParams(params),
    String(authHeader || ""),
    String(authType || ""),
  ].join("|");
}

function layBoNhoDemApi(khoa) {
  const banGhi = boNhoDemApi.get(khoa);
  if (!banGhi) return null;
  if (banGhi.hetHan <= Date.now()) {
    boNhoDemApi.delete(khoa);
    return null;
  }
  return banGhi.snapshot;
}

function luuBoNhoDemApi(khoa, snapshot, ttlMs) {
  if (!khoa || !snapshot || ttlMs <= 0) return;

  if (boNhoDemApi.has(khoa)) {
    boNhoDemApi.delete(khoa);
  }

  boNhoDemApi.set(khoa, {
    hetHan: Date.now() + ttlMs,
    snapshot,
  });

  while (boNhoDemApi.size > API_CACHE_TOI_DA) {
    const khoaDauTien = boNhoDemApi.keys().next().value;
    boNhoDemApi.delete(khoaDauTien);
  }
}

export function xoaBoNhoDemApi() {
  boNhoDemApi.clear();
  yeuCauDangCho.clear();
}

function doiHostLocal(baseUrl = "") {
  if (baseUrl.includes("127.0.0.1")) {
    return baseUrl.replace("127.0.0.1", "localhost");
  }
  if (baseUrl.includes("localhost")) {
    return baseUrl.replace("localhost", "127.0.0.1");
  }
  return "";
}

function anToanGiaiMaPath(segment = "") {
  try {
    return decodeURIComponent(segment);
  } catch {
    return segment;
  }
}

function taoPathDaMaHoa(segments = []) {
  const danhSach = (Array.isArray(segments) ? segments : [])
    .map((item) => String(item || "").trim())
    .filter(Boolean)
    .map((item) => encodeURIComponent(item));

  if (!danhSach.length) return "";
  return `/${danhSach.join("/")}`;
}

function taoDanhSachBaseApiApache() {
  if (typeof window === "undefined") return [];

  const ketQua = [];
  const { origin, protocol, hostname, port, pathname } = window.location;
  const them = (url) => {
    if (!url || ketQua.includes(url)) return;
    ketQua.push(boDauGachCheoCuoi(url));
  };

  const nguonGocKhongPort =
    port && port !== "80" && port !== "443" ? `${protocol}//${hostname}` : origin;

  const segmentsRaw = String(pathname || "")
    .split("/")
    .map((s) => s.trim())
    .filter(Boolean);
  const segmentsDecoded = segmentsRaw.map(anToanGiaiMaPath);

  const indexFe = segmentsDecoded.findIndex(
    (segment) => segment.toLowerCase() === "doantotnghiepfe",
  );
  const indexBe = segmentsDecoded.findIndex(
    (segment) => segment.toLowerCase() === "doantotnghiepbe",
  );

  if (indexFe >= 0) {
    const tienTo = segmentsDecoded.slice(0, indexFe);
    const path = taoPathDaMaHoa([...tienTo, "DoAnTotNghiepBE", "public", "api"]);
    if (path) {
      them(`${origin}${path}`);
      them(`${nguonGocKhongPort}${path}`);
    }
  }

  if (indexBe >= 0) {
    const tienTo = segmentsDecoded.slice(0, indexBe);
    const path = taoPathDaMaHoa([...tienTo, "DoAnTotNghiepBE", "public", "api"]);
    if (path) {
      them(`${origin}${path}`);
      them(`${nguonGocKhongPort}${path}`);
    }
  }

  them(`${origin}/DoAnTotNghiepBE/public/api`);
  them(`${nguonGocKhongPort}/DoAnTotNghiepBE/public/api`);
  them(`${origin}/%C4%90%E1%BB%93%20%C3%81n/DoAnTotNghiepBE/public/api`);
  them(`${nguonGocKhongPort}/%C4%90%E1%BB%93%20%C3%81n/DoAnTotNghiepBE/public/api`);

  return ketQua;
}

function taoUrlApiVoiBase(duongDan = "", baseUrl = API_BASE) {
  const base = boDauGachCheoCuoi(baseUrl);
  const origin = base.replace(/\/api$/i, "");
  const giaTri = String(duongDan || "").trim();

  if (!giaTri) return base;
  if (laDuongDanTuyetDoi(giaTri)) return giaTri;
  if (giaTri.startsWith("/api")) return `${origin}${giaTri}`;
  if (giaTri.startsWith("api/")) return `${origin}/${giaTri}`;
  return `${base}/${giaTri.replace(/^\/+/, "")}`;
}

function taoDanhSachUrlDuPhong(duongDan = "") {
  const ketQua = [];
  const them = (url) => {
    if (!url) return;
    if (!ketQua.includes(url)) {
      ketQua.push(url);
    }
  };

  them(taoUrlApiVoiBase(duongDan, API_BASE));

  const baseDoiHost = doiHostLocal(API_BASE);
  if (baseDoiHost) {
    them(taoUrlApiVoiBase(duongDan, baseDoiHost));
  }

  if (typeof window !== "undefined") {
    const { protocol, hostname } = window.location;
    them(taoUrlApiVoiBase(duongDan, `${window.location.origin}/api`));

    if (hostname && hostname !== "127.0.0.1" && hostname !== "localhost") {
      them(taoUrlApiVoiBase(duongDan, `${protocol}//${hostname}:8000/api`));
    }

    them(taoUrlApiVoiBase(duongDan, "http://127.0.0.1:8000/api"));
    them(taoUrlApiVoiBase(duongDan, "http://localhost:8000/api"));

    taoDanhSachBaseApiApache().forEach((baseApi) => {
      them(taoUrlApiVoiBase(duongDan, baseApi));
    });
  }

  return ketQua;
}

function chuanHoaUrlBackend(url = "") {
  let duongDan = String(url || "").trim();
  if (!duongDan) return duongDan;

  // 1. Chuyển đường dẫn tuyệt đối về tương đối
  if (laDuongDanTuyetDoi(duongDan)) {
    duongDan = duongDan.replace(/^https?:\/\/(?:localhost|127\.0\.0\.1)(?::\d+)?/i, "");
  }

  // 2. Chống lặp /api/api/
  if (duongDan.includes("/api/api/")) {
    duongDan = duongDan.replace(/\/api\/api\//g, "/api/");
  }

  // 3. Nếu đường dẫn đã có /api (hoặc api/), hãy xóa nó
  // Vì baseURL của axios instance đã là /api rồi.
  // Ví dụ: /api/dia-diem -> /dia-diem
  // Khi axios xử lý: baseURL(/api) + url(/dia-diem) -> /api/dia-diem (CHÍNH XÁC)
  if (/^\/?api(\/|$)/i.test(duongDan)) {
    return duongDan.replace(/^\/?api($|\/)/i, "/");
  }

  return duongDan;
}

export function taoUrlApi(duongDan = "") {
  const giaTri = String(duongDan || "").trim();
  if (!giaTri) return API_BASE;

  if (laDuongDanTuyetDoi(giaTri)) return giaTri;

  if (giaTri.startsWith("/api")) {
    return `${API_ORIGIN}${giaTri}`;
  }

  if (giaTri.startsWith("api/")) {
    return `${API_ORIGIN}/${giaTri}`;
  }

  return `${API_BASE}/${giaTri.replace(/^\/+/, "")}`;
}

function layHeaderXacThuc() {
  const tieuDe = {
    Accept: "application/json",
  };

  const token = localStorage.getItem("token");
  if (token) {
    tieuDe.Authorization = `Bearer ${token}`;
  }

  const loaiXacThuc = localStorage.getItem("auth_type");
  if (loaiXacThuc) {
    tieuDe["X-Auth-Type"] = loaiXacThuc;
  }

  return tieuDe;
}

function chuanHoaHeaders(headers = {}) {
  if (headers instanceof Headers) {
    return Object.fromEntries(headers.entries());
  }

  if (Array.isArray(headers)) {
    return Object.fromEntries(headers);
  }

  return { ...headers };
}

export const apiClient = axios.create({
  baseURL: API_BASE,
  timeout: 30000,
  validateStatus: () => true,
  headers: {
    Accept: "application/json",
  },
});

apiClient.interceptors.request.use((config) => {
  const headers = {
    ...layHeaderXacThuc(),
    ...(config.headers || {}),
  };

  return {
    ...config,
    url: chuanHoaUrlBackend(config.url),
    headers,
  };
});

function taoHeaderPhanHoi(headers = {}) {
  const bangHeader = new Map(
    Object.entries(headers || {}).map(([ten, giaTri]) => [String(ten).toLowerCase(), String(giaTri)]),
  );

  return {
    get(ten) {
      return bangHeader.get(String(ten || "").toLowerCase()) || null;
    },
    entries() {
      return bangHeader.entries();
    },
  };
}

function taoPhanHoiGiaLap(ketQua, url) {
  const duLieu = ketQua?.data;

  return {
    ok: ketQua.status >= 200 && ketQua.status < 300,
    status: ketQua.status,
    statusText: ketQua.statusText || "",
    url,
    headers: taoHeaderPhanHoi(ketQua.headers || {}),
    data: duLieu,
    async json() {
      if (typeof duLieu === "string") {
        return duLieu ? JSON.parse(duLieu) : null;
      }
      return duLieu;
    },
    async text() {
      if (typeof duLieu === "string") {
        return duLieu;
      }
      if (duLieu === undefined || duLieu === null) {
        return "";
      }
      return JSON.stringify(duLieu);
    },
  };
}

function taoSnapshotPhanHoi(ketQua, url) {
  return {
    status: ketQua.status,
    statusText: ketQua.statusText || "",
    headers: { ...(ketQua.headers || {}) },
    data: saoChepSau(ketQua.data),
    url,
  };
}

export async function goiApi(input, init = {}) {
  const urlRaw = typeof input === "string" ? input : input?.url || "";
  const method = (init.method || "GET").toUpperCase();
  const headersDaChuanHoa = {
    ...chuanHoaHeaders(init.headers || {}),
  };
  const headers = {
    ...layHeaderXacThuc(),
    ...headersDaChuanHoa,
  };
  const laRequestDoc = laPhuongThucDoc(method);
  const choPhepCache = laRequestDoc && init.cache !== false && init.cache !== "no-store";
  const ttlMs = Number.isFinite(Number(init.cacheTtlMs))
    ? Math.max(0, Number(init.cacheTtlMs))
    : Math.max(0, API_CACHE_TTL_MAC_DINH);
  const khoaYeuCau = taoKhoaYeuCau({
    method,
    urlRaw,
    params: init.params,
    authHeader: headers.Authorization,
    authType: headers["X-Auth-Type"],
  });

  if (choPhepCache && ttlMs > 0) {
    const snapshotDaCo = layBoNhoDemApi(khoaYeuCau);
    if (snapshotDaCo) {
      return taoPhanHoiGiaLap(snapshotDaCo, snapshotDaCo.url);
    }

    const requestDangCho = yeuCauDangCho.get(khoaYeuCau);
    if (requestDangCho) {
      const snapshot = await requestDangCho;
      return taoPhanHoiGiaLap(snapshot, snapshot.url);
    }
  }

  const thucThiYeuCau = async () => {
    const danhSachUrl = taoDanhSachUrlDuPhong(urlRaw);
    let loiCuoi = null;

    for (let i = 0; i < danhSachUrl.length; i += 1) {
      const url = danhSachUrl[i];
      try {
        const ketQua = await apiClient.request({
          url,
          method,
          headers: headersDaChuanHoa,
          params: init.params,
          data: init.body,
          signal: init.signal,
          timeout: typeof init.timeout === "number" ? init.timeout : undefined,
          withCredentials: init.credentials === "include",
        });

        const snapshot = taoSnapshotPhanHoi(ketQua, url);
        if (choPhepCache && ttlMs > 0 && ketQua.status >= 200 && ketQua.status < 300) {
          luuBoNhoDemApi(khoaYeuCau, snapshot, ttlMs);
        }

        if (!laRequestDoc && init.invalidateCache !== false && ketQua.status >= 200 && ketQua.status < 300) {
          xoaBoNhoDemApi();
        }

        return snapshot;
      } catch (error) {
        loiCuoi = error;
        const coTheThuLai = laLoiMangAxios(error) && i < danhSachUrl.length - 1;
        if (!coTheThuLai) {
          throw error;
        }
      }
    }

    throw loiCuoi;
  };

  const requestPromise = thucThiYeuCau();
  if (choPhepCache) {
    yeuCauDangCho.set(khoaYeuCau, requestPromise);
  }

  try {
    const snapshot = await requestPromise;
    return taoPhanHoiGiaLap(snapshot, snapshot.url);
  } finally {
    if (choPhepCache) {
      yeuCauDangCho.delete(khoaYeuCau);
    }
  }
}

let fetchGoc = null;

export function kichHoatFetchBangAxios() {
  if (typeof globalThis === "undefined") return;
  if (globalThis.__FETCH_AXIOS_DA_BAT__) return;

  fetchGoc = typeof globalThis.fetch === "function" ? globalThis.fetch.bind(globalThis) : null;

  globalThis.fetch = async (input, init = {}) => {
    const urlRaw = typeof input === "string" ? input : input?.url || "";

    if (!laApiBackend(urlRaw) && fetchGoc) {
      return fetchGoc(input, init);
    }

    return goiApi(input, init);
  };

  globalThis.__FETCH_AXIOS_DA_BAT__ = true;
}

export function cauHinhAxiosToanCuc() {
  axios.defaults.baseURL = API_BASE;
  axios.defaults.timeout = 30000;

  if (!axios.__INTERCEPTOR_XAC_THUC_DA_BAT__) {
    axios.interceptors.request.use((config) => ({
      ...config,
      url: chuanHoaUrlBackend(config.url),
      headers: {
        ...layHeaderXacThuc(),
        ...(config.headers || {}),
      },
    }));

    axios.__INTERCEPTOR_XAC_THUC_DA_BAT__ = true;
  }
}
