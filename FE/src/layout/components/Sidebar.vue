<template>
  <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#/">
      <div class="sidebar-brand-icon rotate-n-15">
        <i class="fas fa-laugh-wink"></i>
      </div>
      <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item" :class="{ active: isActive('dashboard') }">
      <router-link class="nav-link" to="/">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span>
      </router-link>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">Interface</div>

    <!-- Nav Item - Components -->
    <li class="nav-item">
      <a 
        class="nav-link collapsed" 
        href="#" 
        @click.prevent="toggleCollapse('components')"
        :aria-expanded="collapseState.components"
      >
        <i class="fas fa-fw fa-cog"></i>
        <span>Components</span>
      </a>
      <div 
        id="collapseTwo" 
        class="collapse"
        :class="{ show: collapseState.components }"
      >
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Custom Components:</h6>
          <router-link class="collapse-item" to="/buttons">Buttons</router-link>
          <router-link class="collapse-item" to="/cards">Cards</router-link>
        </div>
      </div>
    </li>

    <!-- Nav Item - Utilities -->
    <li class="nav-item">
      <a 
        class="nav-link collapsed" 
        href="#" 
        @click.prevent="toggleCollapse('utilities')"
        :aria-expanded="collapseState.utilities"
      >
        <i class="fas fa-fw fa-wrench"></i>
        <span>Utilities</span>
      </a>
      <div 
        id="collapseUtilities" 
        class="collapse"
        :class="{ show: collapseState.utilities }"
      >
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Custom Utilities:</h6>
          <router-link class="collapse-item" to="/utilities-color">Colors</router-link>
          <router-link class="collapse-item" to="/utilities-border">Borders</router-link>
          <router-link class="collapse-item" to="/utilities-animation">Animations</router-link>
          <router-link class="collapse-item" to="/utilities-other">Other</router-link>
        </div>
      </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">Addons</div>

    <!-- Nav Item - Pages -->
    <li class="nav-item">
      <a 
        class="nav-link collapsed" 
        href="#" 
        @click.prevent="toggleCollapse('pages')"
        :aria-expanded="collapseState.pages"
      >
        <i class="fas fa-fw fa-folder"></i>
        <span>Pages</span>
      </a>
      <div 
        id="collapsePages" 
        class="collapse"
        :class="{ show: collapseState.pages }"
      >
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Login Screens:</h6>
          <router-link class="collapse-item" to="/login">Login</router-link>
          <router-link class="collapse-item" to="/register">Register</router-link>
          <router-link class="collapse-item" to="/forgot-password">Forgot Password</router-link>
          <div class="collapse-divider"></div>
          <h6 class="collapse-header">Other Pages:</h6>
          <router-link class="collapse-item" to="/404">404 Page</router-link>
          <router-link class="collapse-item" to="/blank">Blank Page</router-link>
        </div>
      </div>
    </li>

    <!-- Nav Item - Charts -->
    <li class="nav-item" :class="{ active: isActive('charts') }">
      <router-link class="nav-link" to="/charts">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Charts</span>
      </router-link>
    </li>

    <!-- Nav Item - Tables -->
    <li class="nav-item" :class="{ active: isActive('tables') }">
      <router-link class="nav-link" to="/tables">
        <i class="fas fa-fw fa-table"></i>
        <span>Tables</span>
      </router-link>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
      <button class="rounded-circle border-0" id="sidebarToggle" @click="toggleSidebar"></button>
    </div>

    <!-- Sidebar Message -->
    <div class="sidebar-card d-none d-lg-flex">
      <!-- Image removed: file not found -->
      <p class="text-center mb-2"><strong>SB Admin Pro</strong> is packed with premium features, components, and more!</p>
      <a class="btn btn-success btn-sm" href="https://startbootstrap.com/theme/sb-admin-pro" target="_blank">Upgrade to Pro!</a>
    </div>
  </ul>
</template>

<script>
export default {
  name: 'Sidebar',
  data() {
    return {
      collapseState: {
        components: false,
        utilities: false,
        pages: false,
      },
    };
  },
  methods: {
    toggleCollapse(key) {
      this.collapseState[key] = !this.collapseState[key];
    },
    toggleSidebar() {
      document.body.classList.toggle('sidebar-toggled');
      document.getElementById('accordionSidebar').classList.toggle('toggled');
    },
    isActive(route) {
      return this.$route.name === route;
    },
  },
  watch: {
    $route() {
      // Reset collapse states when navigating
      Object.keys(this.collapseState).forEach(key => {
        this.collapseState[key] = false;
      });
    },
  },
};
</script>

<style scoped>
.navbar-nav {
  width: 225px;
  min-height: 100vh;
  background: linear-gradient(180deg, #224abe 10%, #00b4d8 100%);
}

.sidebar-brand {
  padding: 1.5rem;
  font-size: 1.5rem;
  font-weight: 800;
  color: white;
  text-decoration: none;
  transition: all 0.3s ease;
}

.sidebar-brand:hover {
  color: #f8f9fc;
}

.sidebar-brand-icon {
  font-size: 2rem;
  margin-right: 0.5rem;
}

.sidebar-brand-text {
  font-size: 1.3rem;
}

.nav-item {
  padding: 0.5rem 1rem;
}

.nav-link {
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  padding: 0.75rem 1rem;
  border-radius: 0.35rem;
  display: flex;
  align-items: center;
  transition: all 0.3s ease;
}

.nav-link:hover,
.nav-item.active .nav-link {
  color: white;
  background-color: rgba(255, 255, 255, 0.1);
}

.nav-link i {
  margin-right: 0.5rem;
}

.sidebar-heading {
  padding: 0.5rem 1.5rem;
  font-size: 0.75rem;
  font-weight: 800;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.6);
  margin-top: 1rem;
}

.collapse {
  display: none;
}

.collapse.show {
  display: block;
}

.collapse-inner {
  padding: 0.5rem 1rem;
}

.collapse-header {
  margin: 0.5rem 0;
  font-size: 0.75rem;
  font-weight: 800;
  text-transform: uppercase;
  color: #224abe;
}

.collapse-item {
  display: block;
  color: #858796;
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: 0.35rem;
  transition: all 0.3s ease;
}

.collapse-item:hover {
  color: #224abe;
  background-color: #f8f9fc;
}

.collapse-divider {
  height: 0;
  margin: 0.5rem 0;
  overflow: hidden;
  border-top: 1px solid #e3e6f0;
}

.sidebar-card {
  flex-direction: column;
  align-items: center;
  padding: 1rem;
  margin-top: auto;
  border-radius: 0.35rem;
  background-color: rgba(255, 255, 255, 0.1);
  color: white;
}

.sidebar-card-illustration {
  width: 100%;
  max-width: 100px;
}

#sidebarToggle {
  background: rgba(255, 255, 255, 0.2);
  border: none !important;
  color: white;
  cursor: pointer;
  padding: 0.5rem;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  transition: all 0.3s ease;
}

#sidebarToggle:hover {
  background-color: rgba(255, 255, 255, 0.3);
}

hr.sidebar-divider {
  border: 0;
  height: 1px;
  background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0) 100%);
  margin: 1rem 0;
}

.sidebar-dark.toggled {
  width: 0;
  overflow: hidden;
}
</style>
