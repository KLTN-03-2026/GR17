<template>
  <FeaturePlaceholder
    title="Tạo nhóm"
    description="Trang khách hàng để tạo nhóm hành trình mới."
    :endpoints="['POST /api/nhom']"
    :notes="['Hiện tại backend yêu cầu ten_nhom.', 'Sau khi tạo xong nên điều hướng về chi tiết nhóm hoặc danh sách nhóm.']"
  />
</template>

<script>
import FeaturePlaceholder from "../../Shared/FeaturePlaceholder.vue";

export default {
  name: "TaoNhom",
  components: { FeaturePlaceholder },
};
</script>
