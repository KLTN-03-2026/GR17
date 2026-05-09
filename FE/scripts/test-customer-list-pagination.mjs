import assert from "node:assert/strict";

import {
  buildPaginatedListing,
  resetListingPage,
} from "../src/components/KhachHang/listingPagination.js";

const items = Array.from({ length: 28 }, (_, index) => ({
  id: index + 1,
}));

const firstPage = buildPaginatedListing(items, { currentPage: 1, perPage: 8 });
assert.equal(firstPage.totalPages, 4, "28 item phai duoc chia thanh 4 trang");
assert.equal(firstPage.items.length, 8, "trang dau phai hien thi 8 item");
assert.equal(firstPage.items[0].id, 1, "trang dau phai bat dau tu item dau tien");

const lastPage = buildPaginatedListing(items, { currentPage: 4, perPage: 8 });
assert.equal(lastPage.items.length, 4, "trang cuoi phai con lai 4 item");
assert.equal(lastPage.items[0].id, 25, "trang cuoi phai bat dau tu item 25");

const overflowPage = buildPaginatedListing(items.slice(0, 3), { currentPage: 5, perPage: 8 });
assert.equal(overflowPage.currentPage, 1, "khi ket qua loc it hon, trang hien tai phai tu dong quay ve trang hop le");
assert.equal(overflowPage.totalPages, 1, "3 item phai chi con 1 trang");

assert.equal(resetListingPage(), 1, "tim kiem hoac doi filter phai reset ve trang 1");

console.log("OK: customer listing pagination assertions passed.");
