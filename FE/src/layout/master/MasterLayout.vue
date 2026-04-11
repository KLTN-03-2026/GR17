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
      <Footer />
    </div>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue';
import Header from '../components/Header.vue';
import Footer from '../components/Footer.vue';

export default {
  name: 'MasterLayout',
  components: {
    Sidebar,
    Header,
    Footer,
  },
};
</script>

<style scoped>
/* Main wrapper */
#wrapper {
  display: flex;
  width: 100%;
  height: 100vh;
  overflow: hidden;
  background-color: #f8f9fc;
}

#content-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  width: 100%;
}

#content {
  flex: 1;
  overflow-y: auto;
  padding: 0;
  background-color: #f8f9fc;
}
</style>
