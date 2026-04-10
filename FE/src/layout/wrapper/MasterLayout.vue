<template>
  <div id="wrapper">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      
      <!-- Header -->
      <Header />

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
import Sidebar from '../components/Sidebar.vue';
import Header from '../components/Header.vue';
import AdminFooter from '../components/AdminFooter.vue';

export default {
  name: 'MasterLayout',
  components: {
    Sidebar,
    Header,
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
