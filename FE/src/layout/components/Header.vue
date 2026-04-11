<template>
  <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    
    <!-- Sidebar Toggle (Navbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3" @click="toggleSidebar">
      <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search -->
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
      <div class="input-group">
        <input 
          class="form-control bg-light border-0 small" 
          type="text" 
          placeholder="Search for..."
          v-model="searchQuery"
          @keyup.enter="handleSearch"
        >
        <div class="input-group-append">
          <button class="btn btn-primary" type="button" @click="handleSearch">
            <i class="fas fa-search fa-sm"></i>
          </button>
        </div>
      </div>
    </form>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

      <!-- Nav Item - Search Dropdown (Visible Only XS) -->
      <li class="nav-item dropdown no-arrow d-sm-none">
        <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" @click.prevent="toggleDropdown('search')"
          :aria-expanded="dropdownState.search">
          <i class="fas fa-search fa-fw"></i>
        </a>
        <div 
          class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" 
          :class="{ show: dropdownState.search }"
          aria-labelledby="searchDropdown"
        >
          <form class="form-inline mr-auto w-100 navbar-search">
            <div class="input-group w-100">
              <input 
                class="form-control bg-light border-0 small" 
                type="text" 
                placeholder="Search for..."
                v-model="searchQuery"
              >
              <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                  <i class="fas fa-search fa-sm"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>

      <!-- Nav Item - Alerts -->
      <li class="nav-item dropdown no-arrow mx-1">
        <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" @click.prevent="toggleDropdown('alerts')"
          :aria-expanded="dropdownState.alerts">
          <i class="fas fa-bell fa-fw"></i>
          <span class="badge badge-danger badge-counter">3+</span>
        </a>
        <div 
          class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" 
          :class="{ show: dropdownState.alerts }"
          aria-labelledby="alertsDropdown"
        >
          <h6 class="dropdown-header bg-primary text-white">
            Alerts Center
          </h6>
          <a class="dropdown-item d-flex align-items-center">
            <div class="mr-3">
              <div class="icon-circle bg-primary">
                <i class="fas fa-file-alt text-white"></i>
              </div>
            </div>
            <div>
              <div class="small text-gray-500">December 12, 2019</div>
              <span class="font-weight-bold">A new monthly report is ready to download!</span>
            </div>
          </a>
          <a class="dropdown-item d-flex align-items-center">
            <div class="mr-3">
              <div class="icon-circle bg-success">
                <i class="fas fa-donate text-white"></i>
              </div>
            </div>
            <div>
              <div class="small text-gray-500">December 7, 2019</div>
              <span class="font-weight-bold">$290.29 has been deposited into your account!</span>
            </div>
          </a>
          <a class="dropdown-item d-flex align-items-center">
            <div class="mr-3">
              <div class="icon-circle bg-warning">
                <i class="fas fa-exclamation-triangle text-white"></i>
              </div>
            </div>
            <div>
              <div class="small text-gray-500">December 2, 2019</div>
              <span class="font-weight-bold">Spending Alert: We've noticed unusually high spending on your account.</span>
            </div>
          </a>
          <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
        </div>
      </li>

      <!-- Nav Item - Messages -->
      <li class="nav-item dropdown no-arrow mx-1">
        <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" @click.prevent="toggleDropdown('messages')"
          :aria-expanded="dropdownState.messages">
          <i class="fas fa-envelope fa-fw"></i>
          <span class="badge badge-danger badge-counter">7</span>
        </a>
        <div 
          class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" 
          :class="{ show: dropdownState.messages }"
          aria-labelledby="messagesDropdown"
        >
          <h6 class="dropdown-header bg-primary text-white">
            Message Center
          </h6>
          <a class="dropdown-item d-flex align-items-center">
            <div class="dropdown-list-image mr-3">
              <img class="rounded-circle" src="https://source.unsplash.com/fn_BT9fwEvg/60x60" alt="...">
              <div class="status-indicator bg-success"></div>
            </div>
            <div class="font-weight-bold">
              <div class="text-truncate">Hi there! I am wondering if you can help me with a
                problem I've been having.</div>
              <div class="small text-gray-500">Emily Fowler · 58m</div>
            </div>
          </a>
          <a class="dropdown-item d-flex align-items-center">
            <div class="dropdown-list-image mr-3">
              <img class="rounded-circle" src="https://source.unsplash.com/AU4_-dptTl0/60x60" alt="...">
              <div class="status-indicator"></div>
            </div>
            <div class="font-weight-bold">
              <div class="text-truncate">I have the photos that you ordered last month, how
                should we proceed?</div>
              <div class="small text-gray-500">Jae Chun · 1d</div>
            </div>
          </a>
          <a class="dropdown-item d-flex align-items-center">
            <div class="dropdown-list-image mr-3">
              <img class="rounded-circle" src="https://source.unsplash.com/CS2uCrpNzJY/60x60" alt="...">
              <div class="status-indicator bg-warning"></div>
            </div>
            <div class="font-weight-bold">
              <div class="text-truncate">Last month's report looks great, I am ready to
                submit check it out!</div>
              <div class="small text-gray-500">Morgan Alvarez · 2d</div>
            </div>
          </a>
          <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
        </div>
      </li>

      <div class="topbar-divider d-none d-sm-block"></div>

      <!-- Nav Item - User Information -->
      <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" @click.prevent="toggleDropdown('user')"
          :aria-expanded="dropdownState.user">
          <span class="mr-2 d-none d-lg-inline text-gray-600 small">Valerie Luna</span>
          <img class="img-profile rounded-circle" src="https://source.unsplash.com/QAB-WJcbgJk/60x60" alt="user">
        </a>
        <div 
          class="dropdown-menu dropdown-menu-right shadow animated--grow-in" 
          :class="{ show: dropdownState.user }"
          aria-labelledby="userDropdown"
        >
          <a class="dropdown-item" href="#">
            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
            Profile
          </a>
          <a class="dropdown-item" href="#">
            <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
            Settings
          </a>
          <a class="dropdown-item" href="#">
            <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
            Activity Log
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#" @click.prevent="logout">
            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
            Logout
          </a>
        </div>
      </li>

    </ul>

  </nav>
</template>

<script>
export default {
  name: 'Header',
  data() {
    return {
      searchQuery: '',
      dropdownState: {
        search: false,
        alerts: false,
        messages: false,
        user: false,
      },
    };
  },
  methods: {
    toggleDropdown(key) {
      // Close other dropdowns
      Object.keys(this.dropdownState).forEach(k => {
        if (k !== key) {
          this.dropdownState[k] = false;
        }
      });
      this.dropdownState[key] = !this.dropdownState[key];
    },
    handleSearch() {
      if (this.searchQuery.trim()) {
        console.log('Searching for:', this.searchQuery);
        // Implement search logic
      }
    },
    toggleSidebar() {
      document.body.classList.toggle('sidebar-toggled');
      document.getElementById('accordionSidebar').classList.toggle('toggled');
    },
    logout() {
      console.log('User logged out');
      // Implement logout logic
      this.$router.push('/login');
    },
  },
  mounted() {
    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.navbar-nav')) {
        Object.keys(this.dropdownState).forEach(key => {
          this.dropdownState[key] = false;
        });
      }
    });
  },
};
</script>

<style scoped>
.navbar {
  background-color: #f8f9fc;
  border-bottom: 1px solid #e3e6f0;
  padding: 0.5rem 1.5rem;
}

.navbar-search {
  width: 250px;
}

.form-inline .input-group {
  width: 100%;
}

.input-group .form-control {
  background-color: #eaecf4 !important;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 0.35rem;
  transition: all 0.3s ease;
}

.input-group .form-control:focus {
  background-color: #fff !important;
  border-color: #224abe;
}

.btn-primary {
  background-color: #224abe;
  border-color: #224abe;
}

.btn-primary:hover {
  background-color: #1a3a7e;
  border-color: #1a3a7e;
}

.nav-link {
  color: #858796;
  text-decoration: none;
  padding: 0.5rem 0.75rem;
  transition: all 0.3s ease;
  cursor: pointer;
}

.nav-link:hover {
  color: #224abe;
}

.badge {
  font-size: 0.7rem;
  padding: 0.25rem 0.5rem;
}

.badge-danger {
  background-color: #e74c3c;
}

.badge-counter {
  position: relative;
  top: -2px;
}

.dropdown-menu {
  min-width: 300px;
  border: none;
  border-radius: 0.35rem;
  margin-top: 0.5rem;
}

.dropdown-header {
  padding: 0.5rem 1.5rem;
  font-size: 0.85rem;
  font-weight: 800;
  text-transform: uppercase;
  border-bottom: 1px solid #e3e6f0;
}

.dropdown-item {
  padding: 0.75rem 1.5rem;
  color: #858796;
  border: none;
  display: flex;
  align-items: center;
  transition: all 0.3s ease;
  text-decoration: none;
}

.dropdown-item:hover {
  color: #224abe;
  background-color: #f8f9fc;
}

.icon-circle {
  height: 40px;
  width: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
}

.icon-circle.bg-primary {
  background-color: #224abe;
}

.icon-circle.bg-success {
  background-color: #1cc88a;
}

.icon-circle.bg-warning {
  background-color: #f6c23e;
}

.dropdown-list-image {
  position: relative;
}

.dropdown-list-image img {
  height: 40px;
  width: 40px;
}

.status-indicator {
  height: 10px;
  width: 10px;
  background-color: #ddd;
  border-radius: 50%;
  position: absolute;
  bottom: 0;
  right: 0;
  border: 2px solid white;
}

.status-indicator.bg-success {
  background-color: #1cc88a;
}

.status-indicator.bg-warning {
  background-color: #f6c23e;
}

.topbar-divider {
  width: 1px;
  height: 1.5rem;
  background-color: #e3e6f0;
  margin: 0 1rem;
}

.img-profile {
  height: 40px;
  width: 40px;
  border: 1px solid #e3e6f0;
  cursor: pointer;
}

#sidebarToggleTop {
  background: transparent;
  color: #224abe;
  border: none;
  padding: 0;
  font-size: 1.5rem;
}

#sidebarToggleTop:hover {
  color: #1a3a7e;
}

.show {
  display: block !important;
}
</style>
