<template>
  <div class="bg-white dark:bg-slate-900 font-sans text-slate-900 dark:text-white antialiased overflow-y-auto min-h-screen">
    <!-- TopNavBar -->
    <nav class="sticky top-0 w-full z-50 bg-white/90 dark:bg-slate-900/90 backdrop-blur-lg shadow-sm">
      <div class="flex items-center justify-between px-6 py-4 max-w-7xl mx-auto">
        <div class="text-2xl font-black tracking-tighter text-slate-900 dark:text-white">Indigo Horizon</div>
        <!-- Desktop Links -->
        <div class="hidden md:flex items-center space-x-8">
          <a class="text-slate-500 dark:text-slate-400 font-medium hover:text-indigo-600 transition-colors" href="#">Explore</a>
          <a class="text-indigo-600 dark:text-indigo-400 font-bold border-b-2 border-indigo-600 pb-1" href="#">Itineraries</a>
          <a class="text-slate-500 dark:text-slate-400 font-medium hover:text-indigo-600 transition-colors" href="#">AI Planner</a>
          <a class="text-slate-500 dark:text-slate-400 font-medium hover:text-indigo-600 transition-colors" href="#">My Bookings</a>
        </div>
        <div class="flex items-center space-x-4">
          <button class="hidden md:block bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-bold hover:scale-105 transition-transform active:scale-95">Plan a Trip</button>
          <span class="material-symbols-outlined text-on-surface cursor-pointer p-2 hover:bg-surface-container rounded-full transition-colors" data-icon="notifications">notifications</span>
          <span class="material-symbols-outlined text-on-surface cursor-pointer p-2 hover:bg-surface-container rounded-full transition-colors" data-icon="account_circle">account_circle</span>
        </div>
      </div>
    </nav>
    <main class="max-w-7xl mx-auto px-6 py-8">
      <!-- Hero Section -->
      <section class="relative h-[600px] rounded-xl overflow-hidden mb-12 shadow-xl group">
        <img alt="Scenic tropical coastline" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" data-alt="Tropical island coastline with turquoise waters" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAYp_KyfttcSyJXzlNNMccIu6ey0I6KlnkukeOOwfH8aFPWMl_id0UBAUWrclckrGLHwl9LgACsLE1h04_1_s_eS7G8eKyU-LCOmjdi7BWvPIoJ49B_r-vbJPzR3QLTGbQ5TCrPaWAH6HtrTYgAMba7Yw9dqiBI2zwbW7Q4cFVe-1zR7IYp7grBYpxr77tAEHgh7njm556hO-JL5q5jB2yp3dX0dQaU0TveOi1ZmI_wNfo2C56M9rpp_OdyeCv-y6TNDedrA73B3rg"/>
        <div class="absolute inset-0 hero-gradient flex flex-col justify-end p-8 md:p-16">
          <div class="flex items-center space-x-2 bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-full w-fit mb-6">
            <span class="material-symbols-outlined text-white text-sm" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
            <span class="text-white text-sm font-bold tracking-wide">TOP RATED EXPERIENCE</span>
          </div>
          <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight mb-4 max-w-3xl">Amalfi Coast: The Azure Dreaming Expedition</h1>
          <div class="flex flex-wrap items-center gap-6 text-white/90">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined" data-icon="schedule">schedule</span>
              <span class="font-medium">8 Days / 7 Nights</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined" data-icon="location_on">location_on</span>
              <span class="font-medium">Positano, Italy</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined" data-icon="group">group</span>
              <span class="font-medium">Small Group (Max 12)</span>
            </div>
          </div>
        </div>
      </section>
      <!-- Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Left Column: Details -->
        <div class="lg:col-span-2 space-y-16">
          <!-- Overview -->
          <article>
            <h2 class="text-3xl font-bold text-slate-900 mb-6">Experience Overview</h2>
            <p class="text-lg text-slate-600 leading-relaxed mb-8">
              Embark on a curated journey through the dramatic cliffs and shimmering azure waters of the Amalfi Coast. This expedition is designed for the lucid voyager who seeks the perfect balance between high-adrenaline discovery and cinematic relaxation. From the vertical streets of Positano to the lemon groves of Sorrento, every moment is crafted to elevate your travel narrative.
            </p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="bg-surface-bright p-4 rounded-xl shadow-sm border border-outline">
                <span class="material-symbols-outlined text-primary mb-2" data-icon="hotel">hotel</span>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Lodging</p>
                <p class="font-bold text-slate-900">5★ Boutique</p>
              </div>
              <div class="bg-surface-bright p-4 rounded-xl shadow-sm border border-outline">
                <span class="material-symbols-outlined text-primary mb-2" data-icon="restaurant">restaurant</span>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Dining</p>
                <p class="font-bold text-slate-900">Gourmet Included</p>
              </div>
              <div class="bg-surface-bright p-4 rounded-xl shadow-sm border border-outline">
                <span class="material-symbols-outlined text-primary mb-2" data-icon="directions_bus">directions_bus</span>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Transport</p>
                <p class="font-bold text-slate-900">Private Chauffeur</p>
              </div>
              <div class="bg-surface-bright p-4 rounded-xl shadow-sm border border-outline">
                <span class="material-symbols-outlined text-primary mb-2" data-icon="hiking">hiking</span>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Activity</p>
                <p class="font-bold text-slate-900">Moderate Pace</p>
              </div>
            </div>
          </article>
          <!-- Itinerary -->
          <section>
            <h2 class="text-3xl font-bold text-slate-900 mb-8">The Itinerary</h2>
            <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:h-full before:w-0.5 before:bg-slate-200">
              <!-- Day 1 -->
              <div class="relative pl-12">
                <div class="absolute left-0 top-1 w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white ring-4 ring-white">
                  <span class="material-symbols-outlined text-sm" data-icon="flight_land">flight_land</span>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">Day 1: Arrival in Naples & Transfer to Sorrento</h3>
                <p class="mt-2 text-slate-600">Meet your private concierge at Naples International Airport for a scenic drive along the coast to your first luxury boutique hotel in Sorrento.</p>
              </div>
              <!-- Day 2 -->
              <div class="relative pl-12">
                <div class="absolute left-0 top-1 w-10 h-10 bg-primary-container rounded-full flex items-center justify-center text-white ring-4 ring-white">
                  <span class="material-symbols-outlined text-sm" data-icon="directions_boat">directions_boat</span>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">Day 2: Capri Island Private Cruise</h3>
                <p class="mt-2 text-slate-600">Spend the day navigating the crystal waters of Capri. Visit the Blue Grotto and enjoy a sunset cocktail on deck before returning to the mainland.</p>
              </div>
              <!-- Day 3 -->
              <div class="relative pl-12">
                <div class="absolute left-0 top-1 w-10 h-10 bg-primary-container rounded-full flex items-center justify-center text-white ring-4 ring-white">
                  <span class="material-symbols-outlined text-sm" data-icon="hiking">hiking</span>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">Day 3: Path of the Gods Hike</h3>
                <p class="mt-2 text-slate-600">Traverse the legendary 'Sentiero degli Dei'. Panoramic vistas of the coastline that defy description. Lunch at a mountain-side shepherd's hut.</p>
              </div>
              <!-- Day 4 -->
              <div class="relative pl-12">
                <div class="absolute left-0 top-1 w-10 h-10 bg-primary-container rounded-full flex items-center justify-center text-white ring-4 ring-white">
                  <span class="material-symbols-outlined text-sm" data-icon="cooking">cooking</span>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">Day 4: Culinary Secrets of Ravello</h3>
                <p class="mt-2 text-slate-600">A private cooking masterclass in a Ravello villa. Learn the art of authentic Pasta al Limone and explore the Villa Rufolo gardens.</p>
              </div>
            </div>
          </section>
          <!-- Inclusions & Exclusions -->
          <section class="grid md:grid-cols-2 gap-8">
            <div class="bg-surface-container p-8 rounded-xl">
              <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary" data-icon="check_circle">check_circle</span>
                What's Included
              </h3>
              <ul class="space-y-4">
                <li class="flex items-start gap-3 text-slate-700">
                  <span class="material-symbols-outlined text-primary text-sm mt-1" data-icon="check">check</span>
                  7 nights in premium boutique hotels
                </li>
                <li class="flex items-start gap-3 text-slate-700">
                  <span class="material-symbols-outlined text-primary text-sm mt-1" data-icon="check">check</span>
                  All breakfasts and 4 gourmet dinners
                </li>
                <li class="flex items-start gap-3 text-slate-700">
                  <span class="material-symbols-outlined text-primary text-sm mt-1" data-icon="check">check</span>
                  Private luxury transportation
                </li>
                <li class="flex items-start gap-3 text-slate-700">
                  <span class="material-symbols-outlined text-primary text-sm mt-1" data-icon="check">check</span>
                  Expert local expedition leads
                </li>
              </ul>
            </div>
            <div class="bg-slate-100 p-8 rounded-xl">
              <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-400" data-icon="cancel">cancel</span>
                Not Included
              </h3>
              <ul class="space-y-4">
                <li class="flex items-start gap-3 text-slate-600">
                  <span class="material-symbols-outlined text-slate-400 text-sm mt-1" data-icon="close">close</span>
                  International airfare to/from Naples
                </li>
                <li class="flex items-start gap-3 text-slate-600">
                  <span class="material-symbols-outlined text-slate-400 text-sm mt-1" data-icon="close">close</span>
                  Personal travel insurance (Required)
                </li>
                <li class="flex items-start gap-3 text-slate-600">
                  <span class="material-symbols-outlined text-slate-400 text-sm mt-1" data-icon="close">close</span>
                  Gratuities for local guides
                </li>
              </ul>
            </div>
          </section>
          <!-- Reviews -->
          <section>
            <div class="flex items-center justify-between mb-8">
              <h2 class="text-3xl font-bold text-slate-900">Traveler Voices</h2>
              <div class="flex items-center gap-2">
                <span class="text-2xl font-black text-slate-900">4.9</span>
                <div class="flex">
                  <span class="material-symbols-outlined text-primary" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                  <span class="material-symbols-outlined text-primary" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                  <span class="material-symbols-outlined text-primary" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                  <span class="material-symbols-outlined text-primary" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                  <span class="material-symbols-outlined text-primary" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                </div>
                <span class="text-slate-500">(128 reviews)</span>
              </div>
            </div>
            <div class="space-y-6">
              <div class="bg-surface-bright p-6 rounded-xl shadow-sm border border-outline">
                <div class="flex justify-between items-start mb-4">
                  <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-200 flex items-center justify-center overflow-hidden">
                      <img alt="Sarah J." class="w-full h-full object-cover" data-alt="Portrait of a female traveler" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCY7sD_zEhy8_zbhHzU5hkdKUbM78pfk-OVpJ6O42eqJbpreyIzrUJBO3K7Vaqipo_gvy9wHzff8sQLnOJkHvEFY_3dgPyUVYqwmLyBfQdvghyZmRWnBCnqp5ATqq-R5IOYJ_7w_8vRZaaASaZw3jSfIegrzaiJRpmnloCyLcRLUZ6jlL-qqQFcmg711WS5q2jttCds4cfz8YUHfazSm5zjEEn1AI5EokSGoZO7VbIKDCqHqhCyoZIotYkdDZ-1-h-aFHQLwAV0dCs"/>
                    </div>
                    <div>
                      <p class="font-bold text-slate-900">Sarah Jenkins</p>
                      <p class="text-sm text-slate-500">September 2023</p>
                    </div>
                  </div>
                  <div class="flex">
                    <span class="material-symbols-outlined text-primary text-sm" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="material-symbols-outlined text-primary text-sm" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="material-symbols-outlined text-primary text-sm" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="material-symbols-outlined text-primary text-sm" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="material-symbols-outlined text-primary text-sm" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                  </div>
                </div>
                <p class="text-slate-600 leading-relaxed italic">"Beyond my expectations. The private boat tour around Capri felt like a dream. Indigo Horizon handles the details so you can actually live the moments."</p>
              </div>
              <div class="bg-surface-bright p-6 rounded-xl shadow-sm border border-outline">
                <div class="flex justify-between items-start mb-4">
                  <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-200 flex items-center justify-center overflow-hidden">
                      <img alt="Marcus T." class="w-full h-full object-cover" data-alt="Portrait of a male traveler" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAFwD13uYsQhbrqdviXDdRxbyRlbFNQS3b_keoMJr39MCly5O4drWR3dGH0XBJu2SH1gamXJRegYROQ3HNscXK5QTYAVSnz6gd249REA0UDtrdFj9QZxgb9KFVyHLX25YTb0lv-pIElUw7hbO_VaNO02uNAoEvDhTrP2zTSDALp5oRLhhHqKBcDqSqvv6ecMs1cAv68Lnm3rHJAeh1y9yvpypAidNpY-SF58qlCXIXnvX5lou2qOi7_l_6_ibQfxKuOMvNps97EbpY"/>
                    </div>
                    <div>
                      <p class="font-bold text-slate-900">Marcus Thorne</p>
                      <p class="text-sm text-slate-500">August 2023</p>
                    </div>
                  </div>
                  <div class="flex">
                    <span class="material-symbols-outlined text-primary text-sm" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="material-symbols-outlined text-primary text-sm" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="material-symbols-outlined text-primary text-sm" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="material-symbols-outlined text-primary text-sm" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="material-symbols-outlined text-primary text-sm" data-icon="star" style="font-variation-settings: 'FILL' 0;">star</span>
                  </div>
                </div>
                <p class="text-slate-600 leading-relaxed italic">"Expertly curated. The Path of the Gods hike was challenging but rewarding. The hotels were top-notch. Highly recommend the small group format."</p>
              </div>
            </div>
          </section>
        </div>
        <!-- Right Column: Booking Card -->
        <aside class="relative">
          <div class="sticky top-24 space-y-6">
            <div class="bg-white rounded-2xl shadow-2xl p-8 border border-outline transition-all">
              <div class="flex justify-between items-end mb-6">
                <div>
                  <p class="text-sm text-slate-500 font-bold uppercase tracking-wider">Starting from</p>
                  <h3 class="text-4xl font-black text-slate-900">$3,499<span class="text-lg font-medium text-slate-400">/person</span></h3>
                </div>
                <div class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">SAVING 15%</div>
              </div>
              <div class="space-y-4 mb-8">
                <div class="p-4 bg-surface rounded-xl border border-outline-variant">
                  <label class="block text-xs font-extrabold text-slate-500 uppercase mb-1">Select Dates</label>
                  <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-900">Oct 12 - Oct 19, 2024</span>
                    <span class="material-symbols-outlined text-slate-400" data-icon="calendar_today">calendar_today</span>
                  </div>
                </div>
                <div class="p-4 bg-surface rounded-xl border border-outline-variant">
                  <label class="block text-xs font-extrabold text-slate-500 uppercase mb-1">Guests</label>
                  <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-900">2 Travelers</span>
                    <span class="material-symbols-outlined text-slate-400" data-icon="expand_more">expand_more</span>
                  </div>
                </div>
              </div>
              <div class="space-y-3 mb-8">
                <div class="flex justify-between text-slate-600">
                  <span>$3,499 x 2 Guests</span>
                  <span>$6,998</span>
                </div>
                <div class="flex justify-between text-slate-600">
                  <span>Booking Fee</span>
                  <span>$0</span>
                </div>
                <div class="border-t border-dashed border-outline-variant pt-3 flex justify-between">
                  <span class="font-bold text-slate-900">Total Price</span>
                  <span class="font-black text-primary text-xl">$6,998</span>
                </div>
              </div>
              <button class="w-full bg-primary text-on-primary py-4 rounded-xl font-black text-lg shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
                Book This Expedition
              </button>
              <p class="text-center text-xs text-slate-400 mt-4">No payment required until reservation is confirmed.</p>
            </div>
            <!-- Trust Badge -->
            <div class="bg-secondary p-6 rounded-2xl flex items-center gap-4 text-white">
              <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-primary-fixed" data-icon="verified_user">verified_user</span>
              </div>
              <div>
                <p class="font-bold text-sm">Lucid Voyager Guarantee</p>
                <p class="text-xs text-white/60">Free cancellation up to 30 days before departure.</p>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </main>
    <!-- Footer -->
    <footer class="bg-slate-50 dark:bg-slate-950 w-full py-12 px-6 mt-16 border-t border-slate-200 dark:border-slate-800">
      <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
        <div class="space-y-4">
          <div class="text-xl font-black text-indigo-600 dark:text-indigo-400">Indigo Horizon</div>
          <p class="font-inter text-sm text-slate-500 dark:text-slate-400 max-w-sm">Curating high-velocity travel experiences for the discerning modern explorer. From sky to sea, we define the horizon.</p>
          <p class="font-inter text-sm text-slate-500 dark:text-slate-400">© 2024 Indigo Horizon. The Lucid Voyager's Choice.</p>
        </div>
        <div class="flex flex-wrap gap-x-8 gap-y-4 md:justify-end">
          <a class="text-slate-500 hover:text-indigo-600 transition-colors text-sm" href="#">Privacy Policy</a>
          <a class="text-slate-500 hover:text-indigo-600 transition-colors text-sm" href="#">Terms of Service</a>
          <a class="text-slate-500 hover:text-indigo-600 transition-colors text-sm" href="#">Help Center</a>
          <a class="text-slate-500 hover:text-indigo-600 transition-colors text-sm" href="#">Destinations</a>
          <a class="text-slate-500 hover:text-indigo-600 transition-colors text-sm" href="#">Partnerships</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
// No reactive data needed for this static component
</script>

<style scoped>
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
.hero-gradient {
  background: linear-gradient(to bottom, rgba(15, 23, 42, 0.2) 0%, rgba(15, 23, 42, 0.7) 100%);
}
</style>
