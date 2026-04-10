Admin Khách hàng

Màn hình nên tạo:
- DanhSachKhachHang.vue: danh sách, tìm kiếm cơ bản, xem trạng thái is_block
- ChiTietKhachHang.vue: xem chi tiết 1 khách hàng
- ChinhSuaKhachHang.vue: cập nhật thông tin khách hàng

API liên quan:
- GET /api/khach-hang
- GET /api/khach-hang/profile/{maKhachHang}
- PUT /api/khach-hang/profile/{maKhachHang}

Ghi chú:
- Hiện tại backend không có route xóa khách hàng hay tạo khách hàng từ admin.
- Nếu muốn đơn giản hơn, ChiTietKhachHang và ChinhSuaKhachHang có thể làm dạng modal trong trang danh sách.
