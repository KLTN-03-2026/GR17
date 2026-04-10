<template>
  <FeaturePlaceholder
    title="Chỉnh sửa đánh giá của tôi"
    description="Trang khách hàng để sửa hoặc xóa đánh giá đã gửi."
    :endpoints="['PUT /api/danh-gia-ke-hoach/{id}', 'DELETE /api/danh-gia-ke-hoach/{id}']"
    :notes="['Có thể gọi dữ liệu từ BangDanhGia và chuyển qua route sửa.', 'Nên khóa nút lưu khi dữ liệu không hợp lệ.']"
  />
</template>

<script>
import FeaturePlaceholder from "../../Shared/FeaturePlaceholder.vue";

export default {
  name: "ChinhSuaDanhGiaCuaToi",
  components: { FeaturePlaceholder },
};
</script>
