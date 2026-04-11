<template>
  <div class="bg-background text-on-background antialiased">
    <!-- SideNavBar -->
    <aside class="h-screen w-64 border-r fixed left-0 top-0 bg-white dark:bg-slate-950 shadow-sm z-50 flex flex-col py-6">
      <div class="px-6 mb-8">
        <h1 class="text-xl font-black tracking-tighter text-slate-900 dark:text-white">Indigo Horizon</h1>
        <p class="text-xs text-slate-500 font-medium">Travel Admin Portal</p>
      </div>
      <nav class="flex-1 space-y-1">
        <a class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 font-medium hover:bg-slate-50 dark:hover:bg-slate-900 transition-colors duration-200" href="#">
          <span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
          <span>Dashboard</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 font-medium hover:bg-slate-50 dark:hover:bg-slate-900 transition-colors duration-200" href="#">
          <span class="material-symbols-outlined" data-icon="group">group</span>
          <span>User Management</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-[#4913ec] font-bold border-r-4 border-[#4913ec] bg-indigo-50/50 dark:bg-indigo-900/10 transition-colors duration-200" href="#">
          <span class="material-symbols-outlined" data-icon="explore" style="font-variation-settings: 'FILL' 1;">explore</span>
          <span>Tour Management</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 font-medium hover:bg-slate-50 dark:hover:bg-slate-900 transition-colors duration-200" href="#">
          <span class="material-symbols-outlined" data-icon="confirmation_number">confirmation_number</span>
          <span>Bookings</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 font-medium hover:bg-slate-50 dark:hover:bg-slate-900 transition-colors duration-200" href="#">
          <span class="material-symbols-outlined" data-icon="settings">settings</span>
          <span>Settings</span>
        </a>
      </nav>
      <div class="px-4 mt-auto">
        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-xl flex items-center gap-3">
          <img alt="Admin Avatar" class="w-10 h-10 rounded-full border-2 border-white shadow-sm" data-alt="Admin user profile picture circular avatar" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCILmCwofFz5jEp4FZTkAQWGU-2jP4u9S1_fF4vPo0AR8mfKe8bZGu75EqnkhXeJvsfv6dvrYtFhnaUNg8rVQWIzFklbFHv8fLBV8R_AFtLBkNW2Te6r6XPbLv61E2644SKH-EWbBKQu1ZTSOQ0DR06OhWBZlUpkYAS03fV0gYwJMByFWBcKqc_e5bjRFrb5aq3wPSa89z30ZrXckCNAb2WPAmR-RoLwUuaewIXY5sH6xZSK4yjAETeJC2EVhKp5IE07bxG0V9aY6M"/>
          <div>
            <p class="text-sm font-bold text-slate-900 dark:text-white leading-tight">Admin User</p>
            <p class="text-xs text-slate-500">Super Admin</p>
          </div>
        </div>
      </div>
    </aside>
    <!-- TopNavBar -->
    <header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 z-40 bg-white/90 dark:bg-slate-950/90 backdrop-blur-md shadow-sm flex justify-between items-center px-8">
      <div class="flex-1 max-w-xl">
        <div class="relative group">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
          <input v-model="searchQuery" class="w-full bg-surface border-none focus:ring-2 focus:ring-primary/20 rounded-full pl-10 pr-4 py-2 text-sm transition-all" placeholder="Search tours, IDs, or categories..." type="text"/>
        </div>
      </div>
      <div class="flex items-center gap-4">
        <button class="p-2 text-slate-500 hover:text-primary hover:bg-indigo-50 rounded-full transition-all duration-300 transform active:scale-95">
          <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
        </button>
        <button class="p-2 text-slate-500 hover:text-primary hover:bg-indigo-50 rounded-full transition-all duration-300 transform active:scale-95">
          <span class="material-symbols-outlined" data-icon="account_circle">account_circle</span>
        </button>
      </div>
    </header>
    <!-- Main Content -->
    <main class="ml-64 pt-24 pb-12 px-8">
      <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header Section -->
        <div class="flex justify-between items-end">
          <div>
            <h2 class="text-3xl font-black tracking-tight text-slate-900">Quản lý Danh sách Tour</h2>
            <p class="text-slate-500 mt-1 font-medium">Manage your travel catalog, pricing, and availability.</p>
          </div>
          <button @click="addNewTour" class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all duration-300">
            <span class="material-symbols-outlined">add_circle</span>
            Thêm Tour Mới
          </button>
        </div>
        <!-- Stats Bento Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white p-6 rounded-2xl shadow-sm border-none flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-primary">
              <span class="material-symbols-outlined text-3xl" data-icon="map">map</span>
            </div>
            <div>
              <p class="text-slate-500 text-sm font-bold">Total Tours</p>
              <h3 class="text-2xl font-black text-slate-900">{{ stats.totalTours }}</h3>
            </div>
          </div>
          <div class="bg-white p-6 rounded-2xl shadow-sm border-none flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
              <span class="material-symbols-outlined text-3xl" data-icon="check_circle">check_circle</span>
            </div>
            <div>
              <p class="text-slate-500 text-sm font-bold">Active Tours</p>
              <h3 class="text-2xl font-black text-slate-900">{{ stats.activeTours }}</h3>
            </div>
          </div>
          <div class="bg-white p-6 rounded-2xl shadow-sm border-none flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
              <span class="material-symbols-outlined text-3xl" data-icon="pending_actions">pending_actions</span>
            </div>
            <div>
              <p class="text-slate-500 text-sm font-bold">Pending Review</p>
              <h3 class="text-2xl font-black text-slate-900">{{ stats.pendingReview }}</h3>
            </div>
          </div>
          <div class="bg-white p-6 rounded-2xl shadow-sm border-none flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600">
              <span class="material-symbols-outlined text-3xl" data-icon="payments">payments</span>
            </div>
            <div>
              <p class="text-slate-500 text-sm font-bold">Tour Revenue</p>
              <h3 class="text-2xl font-black text-slate-900">${{ stats.revenue }}</h3>
            </div>
          </div>
        </div>
        <!-- Filter Bar -->
        <div class="bg-white p-4 rounded-2xl shadow-sm flex flex-wrap items-center gap-4">
          <div class="flex-1 min-w-[240px]">
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
              <input v-model="filterSearch" class="w-full bg-slate-50 border-none rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20" placeholder="Search by name or ID..." type="text"/>
            </div>
          </div>
          <select v-model="filterCategory" class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 min-w-[140px]">
            <option value="">Category: All</option>
            <option value="Beach">Beach</option>
            <option value="Adventure">Adventure</option>
            <option value="Culture">Culture</option>
          </select>
          <select v-model="filterPrice" class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 min-w-[140px]">
            <option value="">Price: All</option>
            <option value="0-500">$0 - $500</option>
            <option value="500-1500">$500 - $1500</option>
            <option value="1500+">$1500+</option>
          </select>
          <select v-model="filterStatus" class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/20 min-w-[140px]">
            <option value="">Status: All</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <button @click="applyFilters" class="bg-slate-900 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-800 transition-colors">Apply Filters</button>
        </div>
        <!-- Tours Table Card -->
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden border-none">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50/50">
                  <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-500">Tour Name</th>
                  <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-500">ID</th>
                  <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-500">Category</th>
                  <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-500">Price</th>
                  <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-500">Created Date</th>
                  <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-500">Status</th>
                  <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-500 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50">
                <tr v-for="tour in filteredTours" :key="tour.id" class="hover:bg-slate-50/30 transition-colors group">
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-4">
                      <div class="w-14 h-14 rounded-xl overflow-hidden shadow-sm flex-shrink-0">
                        <img :alt="tour.name" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" :src="tour.image"/>
                      </div>
                      <div>
                        <p class="font-bold text-slate-900">{{ tour.name }}</p>
                        <p class="text-xs text-slate-500">{{ tour.location }} • {{ tour.duration }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded">{{ tour.id }}</span>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="getCategoryClass(tour.category)" class="text-xs font-bold px-3 py-1 rounded-full">{{ tour.category }}</span>
                  </td>
                  <td class="px-6 py-4">
                    <p class="font-black text-slate-900">${{ tour.price }}</p>
                  </td>
                  <td class="px-6 py-4 text-sm text-slate-600 font-medium">{{ tour.createdDate }}</td>
                  <td class="px-6 py-4">
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input v-model="tour.active" @change="toggleStatus(tour)" class="sr-only peer" type="checkbox"/>
                      <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                      <button @click="viewTour(tour)" class="p-2 text-slate-400 hover:text-primary transition-colors"><span class="material-symbols-outlined text-xl">visibility</span></button>
                      <button @click="editTour(tour)" class="p-2 text-slate-400 hover:text-amber-600 transition-colors"><span class="material-symbols-outlined text-xl">edit</span></button>
                      <button @click="deleteTour(tour)" class="p-2 text-slate-400 hover:text-rose-600 transition-colors"><span class="material-symbols-outlined text-xl">delete</span></button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Pagination -->
          <div class="px-6 py-6 border-t border-slate-50 flex items-center justify-between bg-slate-50/20">
            <p class="text-sm text-slate-500 font-medium">Showing <span class="text-slate-900 font-bold">{{ (currentPage - 1) * itemsPerPage + 1 }}-{{ Math.min(currentPage * itemsPerPage, filteredTours.length) }}</span> of <span class="text-slate-900 font-bold">{{ filteredTours.length }}</span> tours</p>
            <div class="flex items-center gap-2">
              <button @click="prevPage" :disabled="currentPage === 1" class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:bg-white hover:text-primary transition-all shadow-sm disabled:opacity-50">
                <span class="material-symbols-outlined">chevron_left</span>
              </button>
              <button v-for="page in visiblePages" :key="page" @click="goToPage(page)" :class="page === currentPage ? 'bg-primary text-white' : 'text-slate-600 hover:bg-white'" class="w-10 h-10 flex items-center justify-center rounded-xl font-bold transition-all">{{ page }}</button>
              <button @click="nextPage" :disabled="currentPage === totalPages" class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:bg-white hover:text-primary transition-all shadow-sm disabled:opacity-50">
                <span class="material-symbols-outlined">chevron_right</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// Reactive data
const searchQuery = ref('')
const filterSearch = ref('')
const filterCategory = ref('')
const filterPrice = ref('')
const filterStatus = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)

const stats = ref({
  totalTours: 1248,
  activeTours: 1102,
  pendingReview: 46,
  revenue: '142.5k'
})

const tours = ref([
  {
    id: 'TR-8821',
    name: 'Phu Quoc Emerald Bay Retreat',
    location: 'Vietnam',
    duration: '5 Days',
    category: 'Beach',
    price: '899.00',
    createdDate: 'Oct 12, 2023',
    active: true,
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCuagQsLSD1Owrop_b5PHkyUEWliJuAPALS7M8NF28kdTQq-vCkKjt9TDle1Jlv92-Yp1RlauxoHHN7sef0YfkIF5aCn2dNDcfRF6dKuptByFTxN6D5qApHsYAZppESX0qFWKN3pkn_y3e4DVJebyp6AELNQS7EPGv32maYGf57Kh-ZzPxuWYgZphRYdvvbbRSrCjnLq3luBskGXhEtyVig56qzaFPkIUxCe4M4elAQ3dqEDXGwNkVQAgIFoEy6v07E936tNs5eQLk'
  },
  {
    id: 'TR-9104',
    name: 'Ha Giang Extreme Adventure',
    location: 'Vietnam',
    duration: '4 Days',
    category: 'Adventure',
    price: '549.00',
    createdDate: 'Nov 02, 2023',
    active: true,
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDEtnpkDTdkXTtbwIjaKrW4SU1PsIeeYkL9OLGuDQskQ7-GVX5OE8HRv2KMObUAyr9dReEjNITuUOscAtAL2s10zyxmiy7JKG3cob2iuGXR4brcdxv8ihX5BW8CD1xOyCV_mLHD370NFQS-6cQlYuN6kWnX7w7LUax50tmfScpOgl7d6zFSMLcAemsa6A41bZ3LaeVyAAgNsIdDSi5MsVm_EWk2vXrc9wHCqpgqRSJeX6ZW8PvMrPnyVuxlHdOfiwIMKfsqU_-QbkU'
  },
  {
    id: 'TR-7729',
    name: 'Hue Heritage Exploration',
    location: 'Vietnam',
    duration: '3 Days',
    category: 'Culture',
    price: '320.00',
    createdDate: 'Dec 15, 2023',
    active: false,
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuB6rHFxryizp2F0KYo59wKqG7blF3P2ApxYVC5gdhvtJ1E8gkl07ou8ckJ5Q23qjTycJ2v1W_DrWBWoznF4LOszrxS1p69IkMZAnEguIomIIxfYTcnoCNxyAiGV3xhpjHCNXoSJW_NdMIHIVqR_cXAR7vkHpNUDeE1-CPhqRdQsYCqobIIFb233MJmXKl76WxiTyvk4Jk18ohzy2P45KHGirm5KDxFz1ypRqv32hD_sU1pNyaj9zosHyvx3KG9fjZ7lTGqGB4WWFXY'
  }
])

// Computed properties
const filteredTours = computed(() => {
  let filtered = tours.value

  if (filterSearch.value) {
    filtered = filtered.filter(tour =>
      tour.name.toLowerCase().includes(filterSearch.value.toLowerCase()) ||
      tour.id.toLowerCase().includes(filterSearch.value.toLowerCase())
    )
  }

  if (filterCategory.value) {
    filtered = filtered.filter(tour => tour.category === filterCategory.value)
  }

  if (filterPrice.value) {
    const [min, max] = filterPrice.value.split('-').map(v => parseFloat(v.replace('+', '')))
    filtered = filtered.filter(tour => {
      const price = parseFloat(tour.price)
      if (filterPrice.value.includes('+')) {
        return price >= min
      }
      return price >= min && price <= max
    })
  }

  if (filterStatus.value) {
    const isActive = filterStatus.value === 'active'
    filtered = filtered.filter(tour => tour.active === isActive)
  }

  return filtered
})

const totalPages = computed(() => Math.ceil(filteredTours.value.length / itemsPerPage.value))

const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, currentPage.value - 2)
  const end = Math.min(totalPages.value, currentPage.value + 2)
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

// Methods
const getCategoryClass = (category) => {
  const classes = {
    Beach: 'bg-indigo-50 text-primary',
    Adventure: 'bg-orange-50 text-orange-600',
    Culture: 'bg-violet-50 text-violet-600'
  }
  return classes[category] || 'bg-gray-50 text-gray-600'
}

const toggleStatus = (tour) => {
  // Logic to toggle status, perhaps API call
  console.log('Toggling status for', tour.id)
}

const viewTour = (tour) => {
  console.log('Viewing tour', tour.id)
  // Navigate or show modal
}

const editTour = (tour) => {
  console.log('Editing tour', tour.id)
  // Navigate to edit page
}

const deleteTour = (tour) => {
  if (confirm('Are you sure you want to delete this tour?')) {
    tours.value = tours.value.filter(t => t.id !== tour.id)
  }
}

const addNewTour = () => {
  console.log('Adding new tour')
  // Navigate to add page
}

const applyFilters = () => {
  currentPage.value = 1
  // Filters are reactive, so this will update filteredTours
}

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++
}

const goToPage = (page) => {
  currentPage.value = page
}

onMounted(() => {
  // Fetch data if needed
})
</script>

<style scoped>
.material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>