<template>
  <FeaturePlaceholder
    title="Chi tiết nhóm"
    description="Trang khách hàng để xem thông tin một nhóm và các thành viên trong nhóm."
    :endpoints="['GET /api/nhom/{id}', 'GET /api/thanh-vien-nhom/nhom/{maNhom}']"
    :notes="['Nên tách phần thông tin nhóm và danh sách thành viên.', 'Đặt lối sang quản lý thành viên nhóm nếu cần thao tác nhiều.']"
  />
</template>

<script>
import FeaturePlaceholder from "../../Shared/FeaturePlaceholder.vue";

export default {
  name: "ChiTietNhom",
  components: { FeaturePlaceholder },
};
</script>
