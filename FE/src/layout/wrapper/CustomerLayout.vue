<template>
  <div class="customer-app-layout">
    <CustomerHeader />

    <main class="customer-app-layout__main">
      <slot></slot>
    </main>
    <BugReportWidget />
  </div>
</template>

<script>
import CustomerHeader from "../components/CustomerHeader.vue";
import BugReportWidget from "../../components/KhachHang/PhanHoi/BugReportWidget.vue";

export default {
  name: "CustomerLayout",
  components: {
    CustomerHeader,
    BugReportWidget,
  },
};
</script>

<style scoped>
.customer-app-layout {
  min-height: 100vh;
  background:
    radial-gradient(circle at top right, rgba(56, 189, 248, 0.14), transparent 18%),
    radial-gradient(circle at left center, rgba(15, 118, 110, 0.08), transparent 24%),
    linear-gradient(180deg, #f2f7ff 0%, #f8fbff 100%);
  color: #0f172a;
}

.customer-app-layout__main {
  min-height: calc(100vh - 72px);
}
</style>
