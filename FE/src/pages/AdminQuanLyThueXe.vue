  <template>
    <div class="text-on-background">
      <!-- SideNavBar Shell -->
      <aside class="fixed left-0 top-0 h-full w-64 bg-white border-r border-slate-200 flex flex-col p-4 z-50">
        <div class="mb-8 px-2">
          <h1 class="text-2xl font-black text-[#4913ec] tracking-tighter">Indigo Horizon</h1>
          <p class="text-xs font-medium text-slate-400 tracking-wider">TRAVEL MANAGEMENT</p>
        </div>
        <nav class="flex-1 space-y-1">
          <a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 rounded-lg transition-all hover:scale-[1.02] hover:text-[#4913ec]" href="#">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-bold text-sm">Dashboard</span>
          </a>
          <a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 rounded-lg transition-all hover:scale-[1.02] hover:text-[#4913ec]" href="#">
            <span class="material-symbols-outlined">explore</span>
            <span class="font-bold text-sm">Tour</span>
          </a>
          <a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 rounded-lg transition-all hover:scale-[1.02] hover:text-[#4913ec]" href="#">
            <span class="material-symbols-outlined">location_on</span>
            <span class="font-bold text-sm">Địa điểm</span>
          </a>
          <a class="flex items-center gap-3 px-4 py-3 bg-indigo-50 text-[#4913ec] rounded-lg transition-all scale-95 duration-200" href="#">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">directions_car</span>
            <span class="font-bold text-sm">Thuê Xe</span>
          </a>
          <a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 rounded-lg transition-all hover:scale-[1.02] hover:text-[#4913ec]" href="#">
            <span class="material-symbols-outlined">group</span>
            <span class="font-bold text-sm">Khách hàng</span>
          </a>
          <a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 rounded-lg transition-all hover:scale-[1.02] hover:text-[#4913ec]" href="#">
            <span class="material-symbols-outlined">settings</span>
            <span class="font-bold text-sm">Cài đặt</span>
          </a>
        </nav>
        <div class="mt-auto p-4 bg-slate-50 rounded-xl flex items-center gap-3">
          <img class="w-10 h-10 rounded-full object-cover" data-alt="Admin Avatar professional portrait" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA2Jk_NrkLzuKx81gRAiBqW4jkRRTkU0HZ2faqlDavihjr2qxyzy1mxUQK-go3BP_VkTxgFpXFIG9uz7401vt359psYYbQTuhi_VU9yNgm5FscqoqePHCsqvYsqDhiBKBnlXfwygvZpTitGiAuEbPr8TggKGbr_j7hrlWyGPo1ulzTd0WGuMYZ69dROu0g7dXULccOvIx7GEbu72fwq9m2k_nsr30jExyvZ9FS4wzWI3xrlnyVLXdkcTMOz5oj-JYn-W4RcrXRAYHk" />
          <div class="overflow-hidden">
            <p class="text-sm font-bold truncate">Admin Console</p>
            <p class="text-xs text-slate-500 truncate">v1.2.4 Premium</p>
          </div>
        </div>
      </aside>

      <!-- Main Content Canvas -->
      <main class="ml-64 min-h-screen">
        <!-- TopNavBar Shell -->
        <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md h-16 w-full border-b border-slate-100 flex justify-between items-center px-8">
          <div class="flex items-center gap-4 flex-1">
            <div class="relative w-full max-w-md">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
              <input class="w-full pl-10 pr-4 py-2 bg-slate-50 border-none rounded-full text-sm focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Tìm kiếm biển số, tên xe hoặc tài xế..." type="text" />
            </div>
          </div>
          <div class="flex items-center gap-6">
            <button class="hover:bg-slate-50 rounded-full p-2 text-slate-500 transition-colors relative">
              <span class="material-symbols-outlined">notifications</span>
              <span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full border-2 border-white"></span>
            </button>
            <button class="hover:bg-slate-50 rounded-full p-2 text-slate-500 transition-colors">
              <span class="material-symbols-outlined">help</span>
            </button>
            <button class="flex items-center gap-2 hover:bg-slate-50 px-3 py-1.5 rounded-full transition-colors">
              <div class="w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs">JD</div>
              <span class="text-sm font-medium">John Doe</span>
            </button>
          </div>
        </header>

        <div class="p-8 max-w-7xl mx-auto space-y-8">
          <!-- Header Section -->
          <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
              <h2 class="text-4xl font-black tracking-tight text-slate-900">Quản lý Đội Xe</h2>
              <p class="text-slate-500 mt-1">Theo dõi, điều phối và bảo trì hệ thống vận tải của bạn.</p>
            </div>
            <button class="bg-primary text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all">
              <span class="material-symbols-outlined">add</span>
              Thêm Xe Mới
            </button>
          </div>

          <!-- Stats Bento Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border-none flex items-center gap-4">
              <div class="w-12 h-12 bg-indigo-50 text-primary rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">directions_bus</span>
              </div>
              <div>
                <p class="text-slate-500 text-sm font-medium">Tổng số xe</p>
                <p class="text-2xl font-black">48</p>
              </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-none flex items-center gap-4">
              <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
              </div>
              <div>
                <p class="text-slate-500 text-sm font-medium">Đang hoạt động</p>
                <p class="text-2xl font-black">32</p>
              </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-none flex items-center gap-4">
              <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">build</span>
              </div>
              <div>
                <p class="text-slate-500 text-sm font-medium">Đang bảo trì</p>
                <p class="text-2xl font-black">04</p>
              </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-none flex items-center gap-4">
              <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">key</span>
              </div>
              <div>
                <p class="text-slate-500 text-sm font-medium">Đang được thuê</p>
                <p class="text-2xl font-black">12</p>
              </div>
            </div>
          </div>

          <!-- Filters -->
          <div class="bg-white p-4 rounded-2xl shadow-sm flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[240px]">
              <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">filter_list</span>
                <input class="w-full pl-10 pr-4 py-2 border-slate-200 rounded-xl text-sm focus:border-primary focus:ring-primary" placeholder="Lọc nhanh..." type="text" />
              </div>
            </div>
            <select class="border-slate-200 rounded-xl text-sm font-medium px-4 py-2 min-w-[140px] focus:border-primary focus:ring-primary">
              <option>Loại xe: Tất cả</option>
              <option>Xe Bus (45 chỗ)</option>
              <option>SUV (7 chỗ)</option>
              <option>Sedan (4 chỗ)</option>
            </select>
            <select class="border-slate-200 rounded-xl text-sm font-medium px-4 py-2 min-w-[140px] focus:border-primary focus:ring-primary">
              <option>Trạng thái: Tất cả</option>
              <option>Sẵn sàng</option>
              <option>Đang thuê</option>
              <option>Đang bảo trì</option>
            </select>
            <div class="flex border border-slate-200 rounded-xl overflow-hidden">
              <button class="px-3 py-2 bg-slate-100 text-slate-900"><span class="material-symbols-outlined">grid_view</span></button>
              <button class="px-3 py-2 bg-white text-slate-400 hover:text-slate-600"><span class="material-symbols-outlined">view_list</span></button>
            </div>
          </div>

          <!-- Main Content Layout (Split) -->
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8 space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm group hover:shadow-xl transition-all duration-300">
                  <div class="relative h-48 overflow-hidden">
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" data-alt="Modern white tourist bus luxury edition" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAnYkXvdQrnVcKwJidgjrm48VCOfFgLhmTdwFOZOTXMgZ9Cdac_ffgjTN17oFhN4Ejwhbz74V9V_UxxjkHjegFC9p_zIGuJXQPqdUnVbY5NaDyGHd4iP1Z6HTzMQuUfFtltRm7mqjOn_hOvIYtr6zWjuMl663Ht3H-WVnstbFUqzD1VukQQve-tqSGKjoQ4jDIkwS7vhT1c5zGUSETcMzh-_ibdl9Nw4VW-oGaW_wG1tglvCmp2ShAaVzkoai-2bsYswCyOsCQ9MO4" />
                    <div class="absolute top-4 right-4 backdrop-blur-md bg-white/90 px-3 py-1 rounded-full text-[10px] font-black tracking-widest text-primary shadow-sm uppercase">BEST SELLER</div>
                    <div class="absolute bottom-4 left-4 flex gap-2">
                      <span class="bg-emerald-500 text-white px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-1 shadow-lg shadow-emerald-500/20">
                        <span class="w-1.5 h-1.5 bg-white rounded-full"></span> Sẵn sàng
                      </span>
                    </div>
                  </div>
                  <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                      <div>
                        <h3 class="font-bold text-lg text-slate-900">Horizon Express #104</h3>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-tighter">Bus Cao Cấp • 45 Chỗ</p>
                      </div>
                      <span class="text-primary font-black text-lg">2.4tr<span class="text-[10px] text-slate-400 font-medium">/ngày</span></span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-500 text-sm mb-6">
                      <span class="material-symbols-outlined text-sm">badge</span>
                      <span class="font-mono">29B-124.55</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl mb-6">
                      <div class="flex items-center gap-2">
                        <img class="w-8 h-8 rounded-full border-2 border-white object-cover" data-alt="Driver profile photo male" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAQxWigGpcNBhko9AhItSk--R9RuhGoXps4KpfBjrvqk3GC50O10BAQxsYpSwJS-DyqOs_IY40LOXw0T3E0t6GmqFbzdq0HNYJN8QgBl5BNcijOrQUJKGIwReQbUZT6dkke9tRMdw7ywGPJg9ZIPEjqXtEHocU20McDKhYYpWWn3GJ5L9b2uQpam-j1FPJ1qCkLZhLWFjdua5fP-kRmlAHmijxty54ARk-MdwRDjEFUPJeQ1bl9x6ClamYhpBOGDtbbBqIBVK5Ovbg" />
                        <div>
                          <p class="text-[10px] text-slate-400 font-bold uppercase">Tài xế</p>
                          <p class="text-xs font-bold text-slate-700">Nguyễn Văn An</p>
                        </div>
                      </div>
                      <span class="material-symbols-outlined text-slate-400 text-lg">arrow_forward_ios</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                      <button class="bg-slate-50 text-slate-600 py-2 rounded-lg text-xs font-bold hover:bg-slate-100 transition-colors">Sửa</button>
                      <button class="bg-slate-50 text-slate-600 py-2 rounded-lg text-xs font-bold hover:bg-slate-100 transition-colors">Lịch sử</button>
                      <button class="bg-red-50 text-red-600 py-2 rounded-lg text-xs font-bold hover:bg-red-100 transition-colors">Xóa</button>
                    </div>
                  </div>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden shadow-sm group hover:shadow-xl transition-all duration-300">
                  <div class="relative h-48 overflow-hidden">
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" data-alt="Black luxury SUV offroad capable" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCwu_zzP4fDAOebYoOw6J0zhZvgweZWCNTsTlkTTRVxdDKb4vN2uL0EkW2PJruScvb8r6955ARtzEiPaY-_SyE_EpR6TQonBiJzngQIGw5DYoGOWWhkcICaDrA97g3Y2LV8uVs_pxqxBStLNVKnUAFmz777bdAgr4ZEangyQLe6MraB3jD428MHfwUsmx6FIbkPEH6GiQgQi6khlqpVVOSOalT4CRF9HKqMgIgHM6N7x6bsSgEPg-ljbZ5h0oUI_W9yMnKWe08eJyk" />
                    <div class="absolute bottom-4 left-4 flex gap-2">
                      <span class="bg-blue-500 text-white px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-1 shadow-lg shadow-blue-500/20">
                        <span class="w-1.5 h-1.5 bg-white rounded-full"></span> Đang thuê
                      </span>
                    </div>
                  </div>
                  <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                      <div>
                        <h3 class="font-bold text-lg text-slate-900">Black Panther SUV</h3>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-tighter">SUV Đô Thị • 7 Chỗ</p>
                      </div>
                      <span class="text-primary font-black text-lg">1.2tr<span class="text-[10px] text-slate-400 font-medium">/ngày</span></span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-500 text-sm mb-6">
                      <span class="material-symbols-outlined text-sm">badge</span>
                      <span class="font-mono">30G-888.66</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-indigo-50/50 border border-indigo-100 rounded-xl mb-6">
                      <div class="flex items-center gap-2">
                        <img class="w-8 h-8 rounded-full border-2 border-white object-cover" data-alt="Female driver profile photo" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBrw-pYioFHtLFPUHlbDCovGju-ggjuNSnSFmArS4UggFVnHjnjea4vFb4o-Sks5B9WOnSPNOiAJk8A8VV4tBA2FDYY1wIobfSgugHHLSxhLlFag5SxyfZJjq_PxHN5CYR9D6OjTeQYnq0IJsVMewC0AkiRACdXAW4iTXd1MUSVxhNGd_K_4dLnBQH7K3LX6PYUrgYc2mSO-RZ7giWqrj_C2FoW5IQI05SKMVRXNUS6Dvy8pq7thEUmXNcURnwMiV6IPQrnonk_JtE" />
                        <div>
                          <p class="text-[10px] text-primary font-bold uppercase">Tài xế</p>
                          <p class="text-xs font-bold text-slate-700">Trần Thị Bích</p>
                        </div>
                      </div>
                      <div class="text-right">
                        <p class="text-[10px] text-slate-400 font-bold uppercase">Lộ trình</p>
                        <p class="text-[10px] font-bold text-primary">Hà Nội - Sapa</p>
                      </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                      <button class="bg-slate-50 text-slate-600 py-2 rounded-lg text-xs font-bold hover:bg-slate-100 transition-colors">Sửa</button>
                      <button class="bg-slate-50 text-slate-600 py-2 rounded-lg text-xs font-bold hover:bg-slate-100 transition-colors">Lịch sử</button>
                      <button class="bg-red-50 text-red-600 py-2 rounded-lg text-xs font-bold hover:bg-red-100 transition-colors">Xóa</button>
                    </div>
                  </div>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden shadow-sm group opacity-90 grayscale-[0.3] hover:grayscale-0 transition-all">
                  <div class="relative h-48 overflow-hidden">
                    <img class="w-full h-full object-cover" data-alt="Blue luxury sedan car" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBHs__AUkn4ivKyvpLVP_i0yCSwMvV8jf38oXK3MS6ysEhItBVodNpb7BdVB2j1WiWl40udX6XnmEaVP2erJWFYZUQUsE83O7wWKZ_-xmUzX7BxzW8BEySE_72XI63OEgOMJdWHQuGuXA1w4n8KLb9hVPylme8R3eykmZTChWmV004eR8SJUd8k2H6aiTKgCdY3C0baoSXJ93GTwAT8JnBKazxlhK2Ww2IsAm8eCf0BXudLdrttsPj9pb2YyC0JGwWJKCSQao4O2wQ" />
                    <div class="absolute inset-0 bg-slate-900/40 flex items-center justify-center backdrop-blur-[2px]">
                      <div class="bg-amber-500 text-white px-4 py-2 rounded-xl text-sm font-black flex items-center gap-2 shadow-xl shadow-amber-500/20">
                        <span class="material-symbols-outlined text-lg">engineering</span> Đang bảo trì
                      </div>
                    </div>
                  </div>
                  <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                      <div>
                        <h3 class="font-bold text-lg text-slate-900">Elite Cruiser Sedan</h3>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-tighter">Sedan Hạng C • 4 Chỗ</p>
                      </div>
                      <span class="text-slate-400 font-black text-lg line-through">900k</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-500 text-sm mb-6">
                      <span class="material-symbols-outlined text-sm">badge</span>
                      <span class="font-mono">51A-992.31</span>
                    </div>
                    <div class="p-3 bg-amber-50 border border-amber-100 rounded-xl mb-6">
                      <p class="text-[10px] text-amber-600 font-bold uppercase">Trạng thái kỹ thuật</p>
                      <p class="text-xs font-medium text-amber-800">Thay dầu & kiểm tra phanh định kỳ. Dự kiến hoàn thành: 18:00 hôm nay.</p>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                      <button class="bg-slate-50 text-slate-600 py-2 rounded-lg text-xs font-bold hover:bg-slate-100 transition-colors">Sửa</button>
                      <button class="bg-primary text-white py-2 rounded-lg text-xs font-bold shadow-md shadow-primary/10 transition-colors">Báo xong</button>
                      <button class="bg-red-50 text-red-600 py-2 rounded-lg text-xs font-bold hover:bg-red-100 transition-colors">Xóa</button>
                    </div>
                  </div>
                </div>

                <div class="border-2 border-dashed border-slate-200 rounded-2xl flex flex-col items-center justify-center p-8 hover:border-primary hover:bg-indigo-50/20 transition-all cursor-pointer group">
                  <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-primary group-hover:text-white transition-all mb-4">
                    <span class="material-symbols-outlined text-3xl">add</span>
                  </div>
                  <p class="font-bold text-slate-400 group-hover:text-primary">Thêm Xe Mới Vào Đội</p>
                  <p class="text-xs text-slate-300 mt-1">Cập nhật nhanh hệ thống quản lý</p>
                </div>
              </div>
            </div>

            <div class="lg:col-span-4 space-y-6">
              <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                  <h3 class="font-black text-slate-900 tracking-tight">Phân công Tài xế</h3>
                  <button class="text-primary text-xs font-bold hover:underline">Xem tất cả</button>
                </div>
                <div class="space-y-4">
                  <div class="p-4 bg-slate-50 rounded-xl border-l-4 border-primary">
                    <div class="flex items-center justify-between mb-3">
                      <span class="text-[10px] font-black px-2 py-0.5 bg-primary text-white rounded-full uppercase">ĐANG CHẠY</span>
                      <span class="text-[10px] font-bold text-slate-400">08:30 AM</span>
                    </div>
                    <div class="flex items-center gap-3 mb-4">
                      <img class="w-10 h-10 rounded-full object-cover shadow-sm" data-alt="Experienced male driver portrait" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBkqje7imEzZcbqQZ7joI7nHJNQFj4gOEAtZAhEp5_kU2lbF9W8aW0FdxGTnrlh6BaSwbc-miFjajSbXKmyPvx2mQ84mbVh2rBvOdoJMb_EDQ8th8NMa0guf-oQNa6ubsZ89YZccJMzEUjU4xChCxeGMPnDmr9X28CuvttVCV0mga-xlIhEjff_vZlVsi_RxhaTsgKwIa9zpSwKJwvsabJ96mvQglu5uBTq2UAjDlAjdhxo6Q9hU3uH5wk1OzHzp-r2qi_EajZASKc" />
                      <div>
                        <p class="text-sm font-bold text-slate-900">Lê Minh Đức</p>
                        <p class="text-[10px] text-slate-500 font-medium">Xe: <span class="text-slate-700">Horizon #104</span></p>
                      </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-medium text-slate-600 bg-white p-2 rounded-lg">
                      <span class="material-symbols-outlined text-sm text-primary">route</span>
                      <span>Hà Nội → Hạ Long</span>
                    </div>
                  </div>
                  <div class="p-4 bg-slate-50 rounded-xl border-l-4 border-slate-300">
                    <div class="flex items-center justify-between mb-3">
                      <span class="text-[10px] font-black px-2 py-0.5 bg-slate-300 text-slate-600 rounded-full uppercase">CHỜ LỆNH</span>
                      <span class="text-[10px] font-bold text-slate-400">14:00 PM</span>
                    </div>
                    <div class="flex items-center gap-3 mb-4">
                      <img class="w-10 h-10 rounded-full object-cover shadow-sm" data-alt="Senior professional driver profile" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB1ni7r2ADw42GGKzxUAWZbuEuYEEhx3u87QHqUiOXRRz7jGMNMlw2G3mZ24Cj3qBwhxoli2eHRe5mK-uOvwE-a8cOoiS46fAkvn9k8tz8_fcr_e1f7c9z5BCUiSQxSTzVn_XLZgrPV8eNLVxHBjBnw3VtM9hSPj-uTXprqi_Ihajzur9sNevUE8B67iwQt9eomxLirnOWDRR1AYReNCt0t4s8RSC6juF1a0wTSm8d_6IMgtDXtbmkC5Nl37h1-mOI71A-_-gO3C9M" />
                      <div>
                        <p class="text-sm font-bold text-slate-900">Hoàng Quốc Cường</p>
                        <p class="text-[10px] text-slate-500 font-medium">Xe: <span class="text-slate-700">Chưa phân công</span></p>
                      </div>
                    </div>
                    <button class="w-full py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-primary hover:bg-indigo-50 transition-colors">Điều phối xe ngay</button>
                  </div>
                  <div class="p-4 bg-indigo-50/30 rounded-xl border-l-4 border-emerald-500">
                    <div class="flex items-center justify-between mb-3">
                      <span class="text-[10px] font-black px-2 py-0.5 bg-emerald-500 text-white rounded-full uppercase">KẾT THÚC</span>
                      <span class="text-[10px] font-bold text-slate-400">Vừa xong</span>
                    </div>
                    <div class="flex items-center gap-3 mb-2">
                      <img class="w-10 h-10 rounded-full object-cover shadow-sm" data-alt="Female operations manager" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDAmAxedpIqGjf7tDSXi1g-Ulcq8WprFyT1gInPB4RdT7LcD9BMCOhJBs_rCdtlzuF_x9ouxB1sR5RyhL1aAxFeodpwoD9Ua6PWtha7rkKK5ErDHfgln2ghlsgVZIc3UrcU3tDmEjRUDQNC7H9k6U7mqtesUL0p9NWD-wL-kYyKW7gb1I14NyMFJZrESWZUd3yGggxYwhyYZ5Kjy6Ey8e5LO0QkfZqeoZUkpSQo8wq1d1erl-ZiQ2jqx-HCWVf7lEZoA1tqJlesBOY" />
                      <div>
                        <p class="text-sm font-bold text-slate-900">Phạm Thúy Hằng</p>
                        <p class="text-[10px] text-slate-500 font-medium">Xe: <span class="text-slate-700">SUV #008</span></p>
                      </div>
                    </div>
                    <p class="text-[10px] text-emerald-600 font-bold text-right italic">Đã trả xe & kiểm tra xong</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-indigo-900 text-white rounded-2xl p-6 shadow-xl shadow-indigo-900/20">
              <div class="flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-amber-400">warning</span>
                <h4 class="font-bold text-sm uppercase tracking-wider">Thông báo khẩn</h4>
              </div>
              <p class="text-xs text-indigo-100 mb-4 leading-relaxed">Có 2 xe SUV sẽ đến hạn đăng kiểm vào tuần tới. Vui lòng sắp xếp lịch bảo trì sớm.</p>
              <button class="w-full py-2 bg-white/10 hover:bg-white/20 rounded-lg text-xs font-bold transition-all border border-white/20">Xử lý ngay</button>
            </div>
          </div>
        </div>
      </main>

      <div class="fixed bottom-8 right-8 z-50">
        <button class="w-16 h-16 bg-white rounded-full shadow-2xl flex items-center justify-center text-primary hover:scale-110 transition-transform active:scale-95 group overflow-hidden">
          <span class="material-symbols-outlined text-3xl group-hover:hidden">map</span>
          <span class="text-[10px] font-black hidden group-hover:block uppercase">Bản đồ</span>
        </button>
      </div>
    </div>
  </template>

  <script setup>
  // KHÔNG ĐỤNG LOGIC
  </script>

