import assert from "node:assert/strict";

import {
  buildCustomerDashboardSummary,
  buildCustomerOrderActionRoute,
  buildCustomerPaymentRoute,
  filterCustomerOrdersByStatus,
} from "../src/components/KhachHang/customerOrdersShared.js";

const sampleOrders = [
  {
    ma_hoa_don: "HD-PAID-01",
    ma_tour: "TOUR-01",
    ma_thoi_gian_tour: "LICH-01",
    payment_status: "paid",
    created_at: "2026-04-10T10:00:00Z",
  },
  {
    ma_hoa_don: "HD-FAILED-01",
    ma_tour: "TOUR-02",
    ma_thoi_gian_tour: "LICH-02",
    payment_status: "failed",
    created_at: "2026-04-12T08:00:00Z",
  },
  {
    ma_hoa_don: "HD-PENDING-NEW",
    ma_tour: "TOUR-03",
    ma_thoi_gian_tour: "LICH-03",
    payment_status: "pending",
    created_at: "2026-04-13T07:30:00Z",
  },
  {
    ma_hoa_don: "HD-EXPIRED-OLD",
    ma_tour: "TOUR-04",
    ma_thoi_gian_tour: "LICH-04",
    payment_status: "expired",
    created_at: "2026-04-11T09:00:00Z",
  },
];

const summary = buildCustomerDashboardSummary({
  orders: sampleOrders,
  plans: [{ id: "KH01" }, { id: "KH02" }],
  groups: [{ id: "NH01" }],
});

assert.equal(summary.stats.pendingOrders, 1, "phai dem dung hoa don pending");
assert.equal(summary.stats.paidOrders, 1, "phai dem dung hoa don paid");
assert.equal(summary.stats.planCount, 2, "phai dem dung so ke hoach");
assert.equal(summary.stats.groupCount, 1, "phai dem dung so nhom");
assert.equal(
  summary.resumeOrder?.ma_hoa_don,
  "HD-PENDING-NEW",
  "phai uu tien hoa don pending moi nhat cho CTA tiep tuc thanh toan",
);

const filtered = filterCustomerOrdersByStatus(sampleOrders, "failed");
assert.deepEqual(
  filtered.map((item) => item.ma_hoa_don),
  ["HD-FAILED-01"],
  "bo loc trang thai phai tra dung danh sach theo payment_status",
);

assert.deepEqual(
  buildCustomerPaymentRoute(summary.resumeOrder),
  {
    path: "/khach-hang/tour/TOUR-03/thanh-toan",
    query: {
      schedule: "LICH-03",
      invoice: "HD-PENDING-NEW",
    },
  },
  "route tiep tuc thanh toan phai giu du schedule va invoice",
);

assert.deepEqual(
  buildCustomerOrderActionRoute(sampleOrders[0]),
  {
    path: "/khach-hang/hoa-don/HD-PAID-01",
  },
  "don da thanh toan phai mo trang chi tiet hoa don rieng, khong quay lai checkout",
);

console.log("OK: customer dashboard/order helper assertions passed.");
