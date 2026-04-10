Khách hàng - Đánh giá

Màn hình nên tạo:
- BangDanhGia.vue: danh sách đánh giá của địa điểm hoặc kế hoạch liên quan
- TaoDanhGia.vue: gửi đánh giá mới
- ChinhSuaDanhGiaCuaToi.vue: sửa đánh giá của chính mình

API liên quan:
- GET /api/danh-gia-ke-hoach
- GET /api/danh-gia-ke-hoach/dia-diem/{maDiaDiem}
- POST /api/danh-gia-ke-hoach
- PUT /api/danh-gia-ke-hoach/{id}
- DELETE /api/danh-gia-ke-hoach/{id}

Ghi chú:
- Nếu cần tối giản, TaoDanhGia và ChinhSuaDanhGiaCuaToi có thể đưa vào modal trong BangDanhGia.vue.
