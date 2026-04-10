Khách hàng - Nhóm hành trình

Màn hình nên tạo:
- DanhSachNhom.vue: danh sách nhóm hành trình
- ChiTietNhom.vue: thông tin 1 nhóm và danh sách thành viên
- TaoNhom.vue: tạo nhóm mới
- ChinhSuaNhom.vue: sửa tên nhóm
- QuanLyThanhVienNhom.vue: thêm, sửa vai trò, xóa thành viên

API liên quan:
- GET /api/nhom
- GET /api/nhom/{id}
- POST /api/nhom
- PUT /api/nhom/{id}
- DELETE /api/nhom/{id}
- GET /api/thanh-vien-nhom/nhom/{maNhom}
- POST /api/thanh-vien-nhom
- PUT /api/thanh-vien-nhom/{id}
- DELETE /api/thanh-vien-nhom/{id}

Ghi chú:
- QuanLyThanhVienNhom.vue có thể là tab trong ChiTietNhom.vue.
