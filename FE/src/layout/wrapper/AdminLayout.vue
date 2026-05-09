<template>
  <div id="wrapper">
    <!-- Sidebar -->
    <AdminSidebar />

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      
      <!-- Header -->
      <AdminHeader />

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
import AdminSidebar from '../components/AdminSidebar.vue';
import AdminHeader from '../components/AdminHeader.vue';
import AdminFooter from '../components/AdminFooter.vue';

export default {
  name: 'AdminLayout',
  components: {
    AdminSidebar,
    AdminHeader,
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
