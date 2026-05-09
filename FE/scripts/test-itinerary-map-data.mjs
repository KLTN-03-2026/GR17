import assert from "node:assert/strict";
import fs from "node:fs";
import path from "node:path";
import vm from "node:vm";

const projectRoot = path.resolve(import.meta.dirname, "..");
const sourcePath = path.join(projectRoot, "src/components/KhachHang/KeHoach/planShared.js");
const miniMapPath = path.join(projectRoot, "src/components/Shared/ItineraryMiniMap.vue");
const aiPlannerPath = path.join(projectRoot, "src/components/KhachHang/KeHoachAI/index.vue");
const source = fs
  .readFileSync(sourcePath, "utf8")
  .replace(/import\s*\{[\s\S]*?\}\s*from\s*["'][^"']+["'];\s*/m, "")
  .replace(/export\s*\{[\s\S]*?\};\s*/m, "")
  .replaceAll("export function", "function")
  .replaceAll("export const", "const");

const sandbox = {
  console,
  API_BASE: "/api",
  buildHeaders: () => ({ Accept: "application/json" }),
  chuanHoaDanhSach: (payload) => (Array.isArray(payload) ? payload : []),
  getStoredUser: () => null,
  getStoredCustomerId: () => "",
  formatDateDisplay(value) {
    if (!value) return "--";
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return "--";
    return date.toLocaleDateString("vi-VN", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });
  },
};

vm.createContext(sandbox);
vm.runInContext(
  `${source}\nthis.mapPlan = mapPlan;\nthis.buildPlanTimeline = buildPlanTimeline;\nthis.buildPlanMapActivities = buildPlanMapActivities;`,
  sandbox,
  { filename: sourcePath },
);

const { mapPlan, buildPlanTimeline, buildPlanMapActivities } = sandbox;

const mapped = mapPlan({
  ma_ke_hoach: "KHPLAN001",
  ten_ke_hoach: "Hanh trinh map",
  trang_thai: 1,
  hoat_dong_chi_tiets: [
    {
      ma_hoat_dong_chi_tiet: "HD001",
      ngay_cu_the: "2026-05-01",
      gio_bat_dau: "08:00:00",
      gio_ket_thuc: "10:00:00",
      ma_dia_diem: "DD001",
      dia_diem: {
        ten_dia_diem: "Nha tho Duc Ba",
        dia_chi: "Quan 1, TP HCM",
        kinh_do: "106.6990000",
        vi_do: "10.7798000",
      },
    },
    {
      ma_hoat_dong_chi_tiet: "HD002",
      ma_dia_diem: "DD002",
      dia_diem: {
        ten_dia_diem: "Dia diem thieu toa do",
        kinh_do: "",
        vi_do: null,
      },
    },
  ],
});

assert.equal(mapped.activities[0].kinh_do, "106.6990000");
assert.equal(mapped.activities[0].vi_do, "10.7798000");
assert.equal(mapped.activities[0].coordinates.lat, 10.7798);
assert.equal(mapped.activities[0].coordinates.lng, 106.699);
assert.equal(mapped.activities[1].coordinates, null);

const emptyManualPlan = mapPlan({
  ma_ke_hoach: "KHPLANEMPTY",
  ten_ke_hoach: "Chuyen tham quan mien Bac",
  mo_ta: "",
  nguon_tao: "manual",
  ngay_bat_dau: "2026-05-01",
  ngay_ket_thuc: "2026-05-03",
  trang_thai: 1,
  hoat_dong_chi_tiets: [],
});

const emptyTimeline = buildPlanTimeline(emptyManualPlan);
assert.equal(emptyTimeline.length, 3);
assert.equal(JSON.stringify(emptyTimeline.map((day) => day.title)), JSON.stringify(["Ngày 1", "Ngày 2", "Ngày 3"]));
assert.equal(JSON.stringify(emptyTimeline.map((day) => day.activities.length)), JSON.stringify([0, 0, 0]));
assert.equal(buildPlanMapActivities(emptyTimeline).length, 0);

const sortedTimeline = buildPlanTimeline(mapped);
assert.equal(sortedTimeline.length, 2);
assert.equal(sortedTimeline[0].dateRaw, "2026-05-01");
assert.equal(sortedTimeline[0].activities[0].name, "Nha tho Duc Ba");
assert.equal(sortedTimeline[0].activities[0].coordinates.lat, 10.7798);

const aiOnlyPlan = mapPlan({
  ma_ke_hoach: "KHPLANAIFALLBACK",
  ten_ke_hoach: "AI fallback",
  nguon_tao: "ai",
  ngay_bat_dau: "2026-06-01",
  ngay_ket_thuc: "2026-06-02",
  du_lieu_ai: {
    lichTrinh: [
      {
        tieuDe: "Ngay AI 1",
        danhSachHoatDong: [
          {
            buoi: "SANG",
            thoiGian: "08:30",
            tieuDe: "Ho Guom",
            moTa: "Tham quan trung tam Ha Noi",
            kinh_do: "105.8542",
            vi_do: "21.0287",
          },
        ],
      },
    ],
  },
  hoat_dong_chi_tiets: [],
});

const aiTimeline = buildPlanTimeline(aiOnlyPlan);
assert.equal(aiTimeline.length, 1);
assert.equal(aiTimeline[0].activities[0].name, "Ho Guom");
assert.equal(
  JSON.stringify(buildPlanMapActivities(aiTimeline)[0].coordinates),
  JSON.stringify({ lat: 21.0287, lng: 105.8542 }),
);

const miniMapSource = fs.readFileSync(miniMapPath, "utf8");
assert.match(miniMapSource, /min-height:\s*260px/);
assert.match(miniMapSource, /TRAVEL_MAP_IMAGE_URL/);
assert.match(miniMapSource, /photo-1596347958988-cb942eb22eb7/);
assert.match(miniMapSource, /imageLoadFailed/);
assert.match(miniMapSource, /itinerary-mini-map__route-layer/);
assert.match(miniMapSource, /routePolylinePoints/);
assert.match(miniMapSource, /normalizedPoints/);
assert.match(miniMapSource, /viewBox="0 0 900 520"/);
assert.match(miniMapSource, /Chưa có tọa độ để vẽ bản đồ/);
assert.doesNotMatch(miniMapSource, /TRAVEL_MAP_BACKGROUND/);
assert.doesNotMatch(miniMapSource, /from "leaflet"/);
assert.doesNotMatch(miniMapSource, /L\.map|L\.tileLayer|ResizeObserver|invalidateSize\(true\)|tileload|loadingTiles/);
assert.doesNotMatch(miniMapSource, /basemaps\.cartocdn\.com|OpenStreetMap contributors|CARTO/);
assert.doesNotMatch(miniMapSource, /tile\.openstreetmap\.org/);

const aiPlannerSource = fs.readFileSync(aiPlannerPath, "utf8");
assert.match(aiPlannerSource, /ItineraryMiniMap/);
assert.doesNotMatch(aiPlannerSource, /map-placeholder[\s\S]*<img/);
assert.doesNotMatch(aiPlannerSource, /photo-1524661135-423995f22d0b/);

console.log("OK: itinerary map data mapping passed.");
