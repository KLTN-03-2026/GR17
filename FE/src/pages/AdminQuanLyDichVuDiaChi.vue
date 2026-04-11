<template>
  <div class="bg-slate-100 font-sans text-slate-800 min-h-screen antialiased">
    <!-- SideNavBar -->
    <aside class="fixed left-0 top-0 h-full flex flex-col w-64 border-r border-slate-200 bg-slate-50 z-50">
      <div class="p-6">
        <h1 class="text-xl font-black text-indigo-600 tracking-tighter">Indigo Horizon</h1>
        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mt-1">Admin Console</p>
      </div>

      <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
        <a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:text-indigo-600">
          <span class="material-symbols-outlined">dashboard</span>
          <span class="text-sm">Dashboard</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-3 bg-white text-indigo-600 rounded-lg shadow-sm font-bold border border-indigo-50">
          <span class="material-symbols-outlined">location_on</span>
          <span class="text-sm">Location Management</span>
        </a>
      </nav>

      <div class="p-4 mt-auto border-t border-slate-100">
        <button class="w-full flex items-center justify-center gap-2 bg-indigo-600 text-white py-3 rounded-lg font-bold text-sm">
          <span class="material-symbols-outlined text-sm">add</span>
          New Tour
        </button>
      </div>
    </aside>

    <!-- Main -->
    <main class="ml-64 min-h-screen">
      <!-- Header -->
      <header class="sticky top-0 z-40 flex items-center justify-between px-8 h-16 bg-white border-b">
        <div class="flex items-center gap-2 text-slate-400">
          <span class="material-symbols-outlined">location_on</span>
          <span class="text-xs font-bold uppercase">Vietnam</span>
        </div>

        <div class="flex items-center gap-4">
          <input class="pl-4 pr-4 py-2 bg-slate-100 rounded-full text-xs w-64" placeholder="Search..." />
        </div>
      </header>

      <!-- Content -->
      <div class="p-8">
        <h2 class="text-2xl font-bold mb-6">Ha Long Bay Service Management</h2>

        <!-- Packages -->
        <div class="space-y-6">
          <div v-for="pkg in packages" :key="pkg.id" class="bg-white p-6 rounded-xl shadow">
            <h3 class="text-xl font-bold">{{ pkg.name }}</h3>
            <p class="text-slate-500 text-sm mt-1">{{ pkg.desc }}</p>

            <div class="mt-4 flex justify-between items-center">
              <span class="text-indigo-600 font-bold">${{ pkg.price }}</span>

              <div class="flex gap-2">
                <button class="px-3 py-1 border rounded" @click="edit(pkg)">Edit</button>
                <button class="px-3 py-1 bg-indigo-100 text-indigo-600 rounded">Manage</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const packages = ref([
  {
    id: 1,
    name: 'VIP Luxury Cruise Package',
    desc: '3 Days, 2 Nights with premium service',
    price: 1850
  },
  {
    id: 2,
    name: 'Resort Package',
    desc: 'Spa + Beach access',
    price: 740
  },
  {
    id: 3,
    name: 'Explorer Day Trip',
    desc: '1 day experience',
    price: 120
  }
])

const edit = (pkg) => {
  console.log('Edit:', pkg)
}
</script>

