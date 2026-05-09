<template>
  <FeaturePlaceholder
    title="Tạo đánh giá"
    description="Trang khách hàng để gửi đánh giá mới."
    :endpoints="['POST /api/danh-gia-ke-hoach']"
    :notes="['Backend cần Ma_khach_hang, ma_dia_diem, so_sao và noi_dung.', 'Nên dùng chọn sao và ô nội dung.']"
  />
</template>

<script>
import FeaturePlaceholder from "../../Shared/FeaturePlaceholder.vue";

export default {
  name: "TaoDanhGia",
  components: { FeaturePlaceholder },
};
</script>
