import assert from "node:assert/strict";
import fs from "node:fs";
import path from "node:path";

const projectRoot = path.resolve(import.meta.dirname, "..");

const trackedFiles = [
  "src/router/index.js",
  "src/services/authSession.js",
  "src/services/appDialog.js",
  "src/components/KhachHang/Tour/ThanhToan/index.vue",
  "src/layout/components/TopNavBar.vue",
  "src/components/KhachHang/KeHoach/DanhSachKeHoach.vue",
  "src/components/KhachHang/KeHoach/TaoKeHoach.vue",
  "src/components/KhachHang/KeHoach/ChiTietKeHoach.vue",
  "src/components/KhachHang/KeHoach/ChinhSuaKeHoach.vue",
  "src/components/KhachHang/KeHoach/planShared.js",
  "src/components/KhachHang/KeHoachAI/index.vue",
  "src/components/Shared/BudgetDetailModal.vue",
  "src/components/Shared/ItineraryMiniMap.vue",
];

const mojibakeTokens = [
  "KhÃ",
  "Ä",
  "Ä‘",
  "LÃ",
  "Thá»",
  "NgÃ",
  "Báº",
  "Há»",
  "Tiáº",
  "Cáº",
  "VNÄ",
  "Ã¡",
  "Ã¢",
  "Ãª",
  "Ã´",
  "Ãº",
  "Ã ",
  "Ã",
  "Ä",
  "Æ",
  "Ð",
  "Táº",
  "ChÆ",
  "VNÄ",
  "â€¢",
  "Ãƒ",
  "Ã„",
  "Ã†",
  "Ã",
  "TÃ¡Âº",
  "ChÃ†",
  "VNÃ„",
  "Ã¢â‚¬Â¢",
];

const planOnlyMojibakeTokens = ["th?", "l?", "k?"];

const requiredSnippets = [
  { file: "src/router/index.js", value: 'title: "Tổng quan khách hàng"' },
  { file: "src/router/index.js", value: 'title: "Đơn hàng và thanh toán"' },
  { file: "src/components/KhachHang/Tour/ThanhToan/index.vue", value: "Thanh toán tour bằng QR ngân hàng" },
  { file: "src/components/KhachHang/Tour/ThanhToan/index.vue", value: "Hủy thanh toán QR" },
  { file: "src/services/appDialog.js", value: 'title = "Xác nhận thao tác"' },
  { file: "src/services/authSession.js", value: "export function readAuthSession" },
  { file: "src/components/KhachHang/KeHoach/DanhSachKeHoach.vue", value: "Kế hoạch của bạn" },
  { file: "src/components/KhachHang/KeHoach/DanhSachKeHoach.vue", value: "Tạo bằng AI" },
  { file: "src/components/KhachHang/KeHoach/TaoKeHoach.vue", value: "Tạo kế hoạch mới" },
  { file: "src/components/KhachHang/KeHoach/ChiTietKeHoach.vue", value: "Chi tiết hành trình" },
  { file: "src/components/KhachHang/KeHoach/ChiTietKeHoach.vue", value: "Tạo lịch trình AI" },
  { file: "src/components/KhachHang/KeHoach/ChinhSuaKeHoach.vue", value: "Chỉnh sửa kế hoạch" },
  { file: "src/components/KhachHang/KeHoach/ChinhSuaKeHoach.vue", value: "Lịch trình chi tiết" },
  { file: "src/components/KhachHang/KeHoach/planShared.js", value: "Kế hoạch chưa đặt tên" },
  { file: "src/components/Shared/BudgetDetailModal.vue", value: "Chi tiết ngân sách" },
  { file: "src/components/Shared/BudgetDetailModal.vue", value: "Chưa có chi phí chi tiết cho hoạt động này" },
  { file: "src/components/Shared/ItineraryMiniMap.vue", value: "Bản đồ lộ trình thu nhỏ" },
  { file: "src/components/Shared/ItineraryMiniMap.vue", value: "Chưa có tọa độ để vẽ bản đồ" },
];

const forbiddenSnippets = [
  {
    file: "src/components/KhachHang/Tour/ThanhToan/index.vue",
    value: "window.confirm(",
  },
  {
    file: "src/components/KhachHang/Tour/ThanhToan/index.vue",
    value: "http://127.0.0.1:8000/",
  },
  {
    file: "src/components/KhachHang/KeHoach/ChinhSuaKeHoach.vue",
    value: "ADD TO PLAN",
  },
  {
    file: "src/components/KhachHang/KeHoach/ChiTietKeHoach.vue",
    value: '<button class="btn btn--white-full">Xem',
  },
  {
    file: "src/components/KhachHang/KeHoach/ChinhSuaKeHoach.vue",
    value: '<button class="sidebar-action-btn">Xem',
  },
  {
    file: "src/components/KhachHang/KeHoachAI/index.vue",
    value: '<button class="btn-sidebar-detail">Xem',
  },
  {
    file: "src/components/KhachHang/KeHoachAI/index.vue",
    value: "map-placeholder",
  },
  {
    file: "src/components/KhachHang/KeHoachAI/index.vue",
    value: "photo-1524661135-423995f22d0b",
  },
];

const failures = [];

for (const relativePath of trackedFiles) {
  const absolutePath = path.join(projectRoot, relativePath);
  const content = fs.readFileSync(absolutePath, "utf8");

  for (const token of mojibakeTokens) {
    if (content.includes(token)) {
      failures.push(`${relativePath}: còn chuỗi mojibake "${token}"`);
    }
  }

  if (relativePath.includes("/KeHoach/") || relativePath.includes("/KeHoachAI/")) {
    for (const token of planOnlyMojibakeTokens) {
      if (content.includes(token)) {
        failures.push(`${relativePath}: còn chuỗi mojibake "${token}"`);
      }
    }
  }
}

for (const { file, value } of requiredSnippets) {
  const content = fs.readFileSync(path.join(projectRoot, file), "utf8");
  if (!content.includes(value)) {
    failures.push(`${file}: thiếu chuỗi bắt buộc "${value}"`);
  }
}

for (const { file, value } of forbiddenSnippets) {
  const content = fs.readFileSync(path.join(projectRoot, file), "utf8");
  if (content.includes(value)) {
    failures.push(`${file}: còn dùng snippet bị cấm "${value}"`);
  }
}

assert.equal(
  failures.length,
  0,
  `Localization guardrails failed:\n- ${failures.join("\n- ")}`,
);

console.log("OK: Vietnamese localization guardrails passed.");
