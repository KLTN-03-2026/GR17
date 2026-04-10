Khách hàng - Kế hoạch

Màn hình nên tạo:
- DanhSachKeHoach.vue: danh sách kế hoạch theo ma_khach_hang
- ChiTietKeHoach.vue: chi tiết 1 kế hoạch
- TaoKeHoach.vue: tạo kế hoạch mới
- ChinhSuaKeHoach.vue: sửa thông tin kế hoạch

API liên quan:
- GET /api/ke-hoach?ma_khach_hang={id}
- GET /api/ke-hoach/{maKeHoach}
- GET /api/ke-hoach/group/{maNhom}
- POST /api/ke-hoach
- PUT /api/ke-hoach/{maKeHoach}
- PATCH /api/ke-hoach/{maKeHoach}/status
- DELETE /api/ke-hoach/{maKeHoach}

Ghi chú:
- DanhSachKeHoach là màn quan trọng nhất trong cụm việc của Sơn.
