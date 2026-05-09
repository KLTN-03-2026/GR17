import assert from "node:assert/strict";
import fs from "node:fs";
import path from "node:path";

const repoRoot = process.cwd();

function read(relativePath) {
  return fs.readFileSync(path.join(repoRoot, relativePath), "utf8");
}

const orderHistoryPage = read("src/components/KhachHang/LichSuDonHang/index.vue");
const customerOrderShared = read("src/components/KhachHang/customerOrdersShared.js");

assert.match(
  customerOrderShared,
  /label:\s*"Tất cả"/,
  "Bo loc hoa don phai dung nhan tieng Viet dung cho trang thai 'Tat ca'.",
);

assert.match(
  customerOrderShared,
  /label:\s*"Đã thanh toán"/,
  "Nhan trang thai da thanh toan phai duoc chuan hoa Unicode.",
);

assert(
  orderHistoryPage.includes('class="segmented-filter"') &&
    orderHistoryPage.includes('class="segmented-filter__option"'),
  "Trang lich su don hang phai dung segmented filter gon gon thay vi filter chip day.",
);

assert(
  orderHistoryPage.includes('class="orders-shell card"') &&
    orderHistoryPage.includes('class="history-table history-table--desktop"'),
  "Desktop phai giu bang toi gian trong mot shell chinh de de scan.",
);

assert(
  orderHistoryPage.includes('class="history-card-list"') &&
    orderHistoryPage.includes('class="history-card"'),
  "Mobile/tablet phai co danh sach card stacked thay cho viec phu thuoc vao table cuon ngang.",
);

assert(
  orderHistoryPage.includes('class="status-dot"') &&
    orderHistoryPage.includes('class="action-btn action-btn--soft"'),
  "Trang thai va CTA phai duoc lam nhe va de scan hon trong giao dien moi.",
);

assert(
  orderHistoryPage.includes('class="promo-strip"'),
  "Promo banner cuoi trang phai duoc rut gon thanh promo strip dong bo voi header moi.",
);

console.log("OK: order history UI guardrails passed.");
