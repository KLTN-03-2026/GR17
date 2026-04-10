<template>
  <FeaturePlaceholder
    title="Chỉnh sửa nhóm"
    description="Trang khách hàng để sửa tên nhóm hành trình."
    :endpoints="['GET /api/nhom/{id}', 'PUT /api/nhom/{id}', 'DELETE /api/nhom/{id}']"
    :notes="['Nếu cần gọn, sửa tên nhóm có thể đặt trong modal.', 'Cần cảnh báo khi xóa nhóm.']"
  />
</template>

<script>
import FeaturePlaceholder from "../../Shared/FeaturePlaceholder.vue";

export default {
  name: "ChinhSuaNhom",
  components: { FeaturePlaceholder },
};
</script>
