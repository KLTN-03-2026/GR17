<template>
  <div id="wrapper">
    <!-- Sidebar -->
    <PartnerSidebar />

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      
      <!-- Header -->
      <PartnerHeader />

      <!-- Main Content -->
      <div id="content">
        <slot></slot>
      </div>

      <!-- Footer -->
      <AdminFooter />
    </div>
  </div>
</template>

<script>
import PartnerSidebar from '../components/PartnerSidebar.vue';
import PartnerHeader from '../components/PartnerHeader.vue';
import AdminFooter from '../components/AdminFooter.vue';

export default {
  name: 'PartnerLayout',
  components: {
    PartnerSidebar,
    PartnerHeader,
    AdminFooter,
  },
};
</script>

<style scoped>
#wrapper {
  display: flex;
  width: 100%;
  min-height: 100vh;
  background-color: #f8fafc;
}

#content-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  width: 100%;
  min-height: 100vh;
}

#content {
  flex: 1;
  padding: 0;
  background-color: #f8fafc;
  min-height: 0;
}
</style>
