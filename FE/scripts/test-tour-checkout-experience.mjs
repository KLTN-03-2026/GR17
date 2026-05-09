import assert from "node:assert/strict";
import fs from "node:fs";
import path from "node:path";

const repoRoot = process.cwd();
const sharedModulePath = path.join(
  repoRoot,
  "src/components/KhachHang/Tour/ThanhToan/paymentFlowShared.js",
);

assert.ok(
  fs.existsSync(sharedModulePath),
  "Checkout payment flow phai co paymentFlowShared.js de tach logic UX/polling khoi component lon.",
);

const sharedModule = await import(`file://${sharedModulePath.replace(/\\/g, "/")}`);
const {
  buildCheckoutPrimaryActionState,
  buildPaymentSuccessSpotlight,
  getAdaptivePaymentPollingDelay,
} = sharedModule;

assert.equal(
  getAdaptivePaymentPollingDelay(0),
  1500,
  "Lan kiem tra thanh toan dau tien phai xay ra som de giam do tre sau khi khach vua chuyen khoan.",
);

assert.equal(
  getAdaptivePaymentPollingDelay(3),
  3000,
  "Nhung lan kiem tra som tiep theo van phai nhanh hon polling cu 8 giay.",
);

assert.equal(
  getAdaptivePaymentPollingDelay(8),
  8000,
  "Sau giai doan dau moi quay ve nhip polling on dinh hon.",
);

assert.deepEqual(
  buildCheckoutPrimaryActionState({
    processingStage: "creating_invoice",
    paymentStatus: null,
    hasActivePendingInvoice: false,
    showRegenerateButton: false,
    hasSchedule: true,
  }),
  {
    label: "Đang tạo hóa đơn...",
    icon: "fa-spinner fa-spin",
    disabled: true,
    emphasis: "loading",
  },
  "Nut tao hoa don phai co loading state ro rang ngay khi bat dau tao invoice.",
);

assert.deepEqual(
  buildCheckoutPrimaryActionState({
    processingStage: "",
    paymentStatus: "paid",
    hasActivePendingInvoice: false,
    showRegenerateButton: false,
    hasSchedule: true,
  }),
  {
    label: "Đã thanh toán",
    icon: "fa-circle-check",
    disabled: true,
    emphasis: "success",
  },
  "Nut chinh phai khoa lai va doi sang trang thai thanh cong khi hoa don da paid.",
);

const spotlight = buildPaymentSuccessSpotlight({
  ma_hoa_don: "HD123456",
  payment_status: "paid",
  paid_at: "2026-04-16T09:15:00Z",
  so_tien: 1250000,
});

assert.equal(spotlight.visible, true, "Spotlight thanh toan thanh cong phai hien thi voi hoa don paid.");
assert.match(
  spotlight.title,
  /Thanh toán đã hoàn tất/,
  "Spotlight thanh toan thanh cong phai co tieu de noi bat, de nhan ra ngay.",
);
assert.match(
  spotlight.summary,
  /HD123456/,
  "Spotlight phai nhac ro ma hoa don de khach yên tam da doi chieu dung giao dich.",
);

console.log("OK: checkout payment experience guardrails passed.");
