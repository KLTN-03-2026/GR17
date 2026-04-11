<template>
  <nav class="navbar navbar-top">
    <div class="navbar-container">
      <!-- Logo -->
      <div class="navbar-brand">
        <router-link to="/" class="brand-link">
          <i class="fas fa-paper-plane"></i>
          <span>Smart Travel</span>
        </router-link>
      </div>

      <!-- Toggle Button for Mobile -->
      <button class="navbar-toggler" @click="toggleNav">
        <i class="fas fa-bars"></i>
      </button>

      <!-- Navigation Menu -->
      <ul class="navbar-menu" :class="{ active: isNavOpen }">
        <li class="nav-item">
          <router-link to="/" class="nav-link">Home</router-link>
        </li>
        <li class="nav-item">
          <router-link to="/destinations" class="nav-link">Destinations</router-link>
        </li>
        <li class="nav-item">
          <router-link to="/tours" class="nav-link">Tours</router-link>
        </li>
        <li class="nav-item">
          <router-link to="/about" class="nav-link">About Us</router-link>
        </li>
        <li class="nav-item">
          <router-link to="/contact" class="nav-link">Contact</router-link>
        </li>
        <li class="nav-item">
          <router-link to="/dang-nhap" class="nav-link nav-link-btn">Sign In</router-link>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'TopNavBar',
  data() {
    return {
      isNavOpen: false,
    };
  },
  methods: {
    toggleNav() {
      this.isNavOpen = !this.isNavOpen;
    },
    closeNav() {
      this.isNavOpen = false;
    },
  },
  watch: {
    $route() {
      this.closeNav();
    },
  },
};
</script>

<style scoped>
.navbar-top {
  background-color: white;
  padding: 1rem 0;
  border-bottom: 1px solid #e5e5e5;
  position: sticky;
  top: 0;
  z-index: 1000;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.navbar-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.navbar-brand {
  flex-shrink: 0;
}

.brand-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.3rem;
  font-weight: 700;
  color: #1f2937;
  text-decoration: none;
  transition: color 0.3s ease;
}

.brand-link:hover {
  color: #6366f1;
}

.brand-link i {
  color: #6366f1;
  font-size: 1.5rem;
}

.navbar-menu {
  display: flex;
  list-style: none;
  gap: 2rem;
  margin: 0;
  padding: 0;
  align-items: center;
}

.nav-item {
  position: relative;
}

.nav-link {
  color: #6b7280;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
  font-size: 0.95rem;
}

.nav-link:hover {
  color: #1f2937;
}

.nav-link.router-link-active {
  color: #6366f1;
}

.nav-link-btn {
  background-color: #6366f1;
  color: white !important;
  padding: 0.5rem 1.5rem;
  border-radius: 0.5rem;
  transition: background-color 0.3s ease;
}

.nav-link-btn:hover {
  background-color: #4f46e5;
  color: white;
}

.navbar-toggler {
  display: none;
  background: none;
  border: none;
  color: #1f2937;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0;
}

@media (max-width: 768px) {
  .navbar-container {
    padding: 0 1rem;
  }

  .navbar-toggler {
    display: block;
  }

  .navbar-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    flex-direction: column;
    gap: 0;
    padding: 1rem;
    background-color: white;
    border-bottom: 1px solid #e5e5e5;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .navbar-menu.active {
    display: flex;
  }

  .nav-item {
    width: 100%;
  }

  .nav-link {
    display: block;
    padding: 0.75rem 0;
  }

  .nav-link-btn {
    display: block;
    text-align: center;
    margin-top: 0.5rem;
  }
}
</style>
