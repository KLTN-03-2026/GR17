<template>
  <div id="app-container">
    <!-- Top Navigation Bar -->
    <TopNavBar />

    <!-- Main Content -->
    <main id="main-content">
      <slot></slot>
    </main>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script>
import TopNavBar from '../components/TopNavBar.vue';
import Footer from '../components/Footer.vue';

export default {
  name: 'LandingLayout',
  components: {
    TopNavBar,
    Footer,
  },
};
</script>

<style scoped>
#app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  width: 100%;
  background-color: #ffffff;
  overflow-x: hidden;
}

#main-content {
  flex: 1;
  width: 100%;
  padding-top: 86px;
}
</style>
