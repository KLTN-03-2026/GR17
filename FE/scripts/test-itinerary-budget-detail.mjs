import assert from "node:assert/strict";
import fs from "node:fs";
import path from "node:path";
import vm from "node:vm";

const projectRoot = path.resolve(import.meta.dirname, "..");
const sourcePath = path.join(projectRoot, "src/components/KhachHang/KeHoach/planShared.js");
const modalPath = path.join(projectRoot, "src/components/Shared/BudgetDetailModal.vue");

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
  `${source}\nthis.buildBudgetDetails = buildBudgetDetails;\nthis.extractActivityCost = extractActivityCost;\nthis.formatCurrencyDisplay = formatCurrencyDisplay;`,
  sandbox,
  { filename: sourcePath },
);

const { buildBudgetDetails, extractActivityCost, formatCurrencyDisplay } = sandbox;

assert.equal(formatCurrencyDisplay(1000000), "1.000.000 VNĐ");

const pricedActivity = {
  name: "Bảo tàng",
  dateFormatted: "01/05/2026",
  time: "08:00 - 10:00",
  locationCost: "100000",
  services: [
    { name: "Vé tham quan", price: "30000" },
    { ten_dich_vu: "Hướng dẫn", gia: "20000", trang_thai: true },
    { ten_dich_vu: "Dịch vụ tắt", gia: "90000", trang_thai: false },
  ],
};

const priced = extractActivityCost(pricedActivity);
assert.equal(priced.baseCost, 100000);
assert.equal(priced.serviceCost, 50000);
assert.equal(priced.totalCost, 150000);
assert.equal(priced.totalLabel, "150.000 VNĐ");
assert.equal(priced.services.length, 2);

const details = buildBudgetDetails(
  {
    budget: 1000000,
    budgetLabel: "1.000.000 VNĐ",
    soNguoi: 2,
    aiData: { tongChiPhi: "900000" },
  },
  [
    {
      title: "Ngày 1",
      dateFormatted: "01/05/2026",
      activities: [
        pricedActivity,
        { name: "Điểm chưa có giá", dateFormatted: "01/05/2026", time: "12:00 - 14:00" },
      ],
    },
  ],
);

assert.equal(details.plannedBudget, 1000000);
assert.equal(details.aiEstimatedTotal, 900000);
assert.equal(details.estimatedActivityTotal, 150000);
assert.equal(details.remaining, 850000);
assert.equal(details.isOverBudget, false);
assert.equal(details.perPersonEstimate, 75000);
assert.equal(details.missingCostCount, 1);
assert.equal(details.activityRows[1].note, "Chưa có chi phí chi tiết cho hoạt động này");
assert.equal(details.rows.some((row) => row.label === "Còn lại so với ngân sách"), true);

assert.equal(fs.existsSync(modalPath), true, "BudgetDetailModal.vue must exist");
const modalSource = fs.readFileSync(modalPath, "utf8");
assert.match(modalSource, /Chi tiết ngân sách/);
assert.match(modalSource, /Chưa có chi phí chi tiết cho hoạt động này/);
assert.match(modalSource, /update:show/);

console.log("OK: itinerary budget detail helpers passed.");
