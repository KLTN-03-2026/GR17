<template>
  <div class="bg-background text-on-surface min-h-screen flex flex-col">
    <!-- Top Navigation (Reduced/Suppressed for Focus) -->
    <nav class="bg-white/90 backdrop-blur-md sticky top-0 w-full z-50 px-8 h-20 flex justify-between items-center shadow-sm">
      <div class="text-2xl font-black text-slate-900 tracking-tighter">Indigo Horizon</div>
      <div class="flex items-center gap-4">
        <span class="material-symbols-outlined text-slate-500" data-icon="lock">lock</span>
        <span class="text-slate-500 font-inter text-sm font-medium">Secure Checkout</span>
      </div>
    </nav>
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 md:px-8 py-12">
      <!-- Progress Indicator -->
      <div class="flex items-center justify-center mb-12 space-x-4 md:space-x-8">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs">01</div>
          <span class="text-slate-500 text-sm font-bold uppercase tracking-tight">Information</span>
        </div>
        <div class="w-12 h-px bg-slate-200"></div>
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-on-primary font-bold text-xs">02</div>
          <span class="text-primary text-sm font-bold uppercase tracking-tight">Payment</span>
        </div>
        <div class="w-12 h-px bg-slate-200"></div>
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs">03</div>
          <span class="text-slate-500 text-sm font-bold uppercase tracking-tight">Confirmation</span>
        </div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Left Column: Checkout Forms -->
        <div class="lg:col-span-7 space-y-12">
          <!-- Contact Information -->
          <section>
            <div class="flex items-center gap-3 mb-6">
              <span class="material-symbols-outlined text-primary" data-icon="person">person</span>
              <h2 class="text-2xl font-bold tracking-tight text-slate-900">Contact Information</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Full Name</label>
                <input class="w-full px-4 py-3 rounded-lg border border-outline bg-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all" placeholder="Johnathan Doe" type="text"/>
              </div>
              <div class="space-y-2">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Email Address</label>
                <input class="w-full px-4 py-3 rounded-lg border border-outline bg-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all" placeholder="john@horizon.com" type="email"/>
              </div>
              <div class="md:col-span-2 space-y-2">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Phone Number</label>
                <input class="w-full px-4 py-3 rounded-lg border border-outline bg-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all" placeholder="+1 (555) 000-0000" type="tel"/>
              </div>
            </div>
          </section>
          <!-- Payment Methods -->
          <section>
            <div class="flex items-center gap-3 mb-6">
              <span class="material-symbols-outlined text-primary" data-icon="payments">payments</span>
              <h2 class="text-2xl font-bold tracking-tight text-slate-900">Payment Method</h2>
            </div>
            <!-- Method Selector -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
              <button class="flex flex-col items-center justify-center p-4 border-2 border-primary bg-primary-fixed/30 rounded-xl transition-all">
                <span class="material-symbols-outlined text-primary mb-2" data-icon="credit_card">credit_card</span>
                <span class="text-xs font-bold text-primary">Card</span>
              </button>
              <button class="flex flex-col items-center justify-center p-4 border border-outline bg-white rounded-xl hover:border-primary/30 transition-all grayscale opacity-70">
                <span class="material-symbols-outlined text-slate-600 mb-2" data-icon="account_balance_wallet">account_balance_wallet</span>
                <span class="text-xs font-bold text-slate-600">MoMo</span>
              </button>
              <button class="flex flex-col items-center justify-center p-4 border border-outline bg-white rounded-xl hover:border-primary/30 transition-all grayscale opacity-70">
                <span class="material-symbols-outlined text-slate-600 mb-2" data-icon="account_balance">account_balance</span>
                <span class="text-xs font-bold text-slate-600">Bank</span>
              </button>
              <button class="flex flex-col items-center justify-center p-4 border border-outline bg-white rounded-xl hover:border-primary/30 transition-all grayscale opacity-70">
                <span class="material-symbols-outlined text-slate-600 mb-2" data-icon="qr_code_2">qr_code_2</span>
                <span class="text-xs font-bold text-slate-600">ZaloPay</span>
              </button>
            </div>
            <!-- Card Details -->
            <div class="bg-surface-container-low p-6 rounded-xl border border-outline-variant/30 space-y-6">
              <div class="space-y-2">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Card Number</label>
                <div class="relative">
                  <input class="w-full px-4 py-3 rounded-lg border border-outline bg-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all pl-12" placeholder="0000 0000 0000 0000" type="text"/>
                  <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" data-icon="credit_card">credit_card</span>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Expiry Date</label>
                  <input class="w-full px-4 py-3 rounded-lg border border-outline bg-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all text-center" placeholder="MM/YY" type="text"/>
                </div>
                <div class="space-y-2">
                  <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">CVV</label>
                  <input class="w-full px-4 py-3 rounded-lg border border-outline bg-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all text-center" placeholder="***" type="password"/>
                </div>
              </div>
            </div>
          </section>
          <!-- Actions & Security -->
          <div class="space-y-6">
            <button class="w-full bg-primary text-on-primary font-bold py-5 rounded-xl text-lg hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-primary/20 flex items-center justify-center gap-3">
              Complete Payment
              <span class="material-symbols-outlined" data-icon="arrow_forward">arrow_forward</span>
            </button>
            <div class="flex flex-wrap items-center justify-center gap-8 pt-4">
              <div class="flex items-center gap-2 opacity-60">
                <span class="material-symbols-outlined text-green-600" data-icon="verified_user">verified_user</span>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-widest">SSL Secure</span>
              </div>
              <div class="flex items-center gap-2 opacity-60">
                <span class="material-symbols-outlined text-indigo-600" data-icon="shield">shield</span>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-widest">Secure Pay</span>
              </div>
              <div class="flex items-center gap-2 opacity-60">
                <span class="material-symbols-outlined text-blue-600" data-icon="gpp_good">gpp_good</span>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-widest">PCI Compliant</span>
              </div>
            </div>
          </div>
        </div>
        <!-- Right Column: Order Summary -->
        <div class="lg:col-span-5">
          <div class="sticky top-28 space-y-6">
            <div class="bg-white rounded-2xl shadow-2xl shadow-slate-200/50 overflow-hidden border border-slate-100">
              <!-- Tour Header -->
              <div class="relative h-48">
                <img alt="Beach destination" class="w-full h-full object-cover" data-alt="Stunning tropical beach with turquoise water and palms" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBblgYSEDdk7ASbuVoBVMcB0ZRFutKq2sGrFiPTOWQBfJbxb71nIfbfPmVXd2DqtVkW4NNyY0vkMdDeyfmwrkfYNClwjz5FBhbfcj67hUYolR_9aWM1MKALOT0xG-lgB0MuTrLIud83nlcnGEVpjKPLYSnNIgdRVMqG9uo41CU8hnDPP1zHrB1e8IXRwKjMvSJ-dzEACuwcfS9Z2UmakKT8vgurLzpS2LUydzgf8-vY3QD_7fikvhN7fCx8toag5L7rb2ZjcTduim4"/>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                <div class="absolute bottom-4 left-6 right-6">
                  <div class="bg-primary-fixed/90 backdrop-blur-sm px-2 py-1 rounded text-[10px] font-black text-primary uppercase tracking-widest inline-block mb-2">Selected Tour</div>
                  <h3 class="text-white text-xl font-bold leading-tight">Azure Coast: Private Island Expedition</h3>
                </div>
              </div>
              <!-- Summary Content -->
              <div class="p-8 space-y-6">
                <div class="flex flex-col gap-4">
                  <div class="flex items-center gap-3 text-slate-600">
                    <span class="material-symbols-outlined text-primary text-xl" data-icon="calendar_month">calendar_month</span>
                    <span class="text-sm font-medium">October 24, 2024 - October 28, 2024</span>
                  </div>
                  <div class="flex items-center gap-3 text-slate-600">
                    <span class="material-symbols-outlined text-primary text-xl" data-icon="group">group</span>
                    <span class="text-sm font-medium">2 Adults, 1 Child</span>
                  </div>
                </div>
                <hr class="border-slate-100"/>
                <!-- Pricing -->
                <div class="space-y-4">
                  <div class="flex justify-between items-center text-sm">
                    <span class="text-slate-500 font-medium">Base Trip Price</span>
                    <span class="text-slate-900 font-bold">$2,450.00</span>
                  </div>
                  <div class="flex justify-between items-center text-sm">
                    <span class="text-slate-500 font-medium">Taxes &amp; Service Fees</span>
                    <span class="text-slate-900 font-bold">$185.50</span>
                  </div>
                  <div class="flex justify-between items-center text-sm">
                    <span class="text-indigo-600 font-bold italic">Early Bird Discount</span>
                    <span class="text-indigo-600 font-bold">-$245.00</span>
                  </div>
                  <div class="pt-4 mt-4 border-t border-slate-100 flex justify-between items-end">
                    <div>
                      <span class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Total Amount</span>
                      <span class="text-3xl font-black text-slate-900 tracking-tighter">$2,390.50</span>
                    </div>
                    <div class="text-right">
                      <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Currency</span>
                      <span class="text-sm font-bold text-slate-700">USD</span>
                    </div>
                  </div>
                </div>
                <!-- Promo Code -->
                <div class="pt-6">
                  <div class="relative">
                    <input class="w-full px-4 py-3 rounded-lg border border-outline bg-slate-50 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all text-xs font-bold tracking-widest uppercase" placeholder="PROMO CODE" type="text"/>
                    <button class="absolute right-2 top-1/2 -translate-y-1/2 bg-slate-900 text-white text-[10px] font-bold px-4 py-2 rounded-md hover:bg-primary transition-colors">APPLY</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Help Card -->
            <div class="bg-indigo-50/50 p-6 rounded-2xl border border-indigo-100/50 flex items-start gap-4">
              <span class="material-symbols-outlined text-primary" data-icon="help_center">help_center</span>
              <div>
                <h4 class="text-sm font-bold text-indigo-900 mb-1">Need assistance?</h4>
                <p class="text-xs text-indigo-700 leading-relaxed font-medium">Our travel experts are available 24/7 to help with your booking and payment process.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
    <!-- Footer -->
    <footer class="bg-slate-50 w-full py-12 px-8 mt-auto border-t border-slate-200">
      <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:flex lg:justify-between items-center gap-6">
        <div class="text-lg font-bold text-slate-900">Indigo Horizon</div>
        <div class="flex flex-wrap gap-x-8 gap-y-2">
          <a class="text-slate-500 font-inter text-sm font-medium hover:text-indigo-500 transition-colors" href="#">Privacy Policy</a>
          <a class="text-slate-500 font-inter text-sm font-medium hover:text-indigo-500 transition-colors" href="#">Terms of Service</a>
          <a class="text-slate-500 font-inter text-sm font-medium hover:text-indigo-500 transition-colors" href="#">Support Center</a>
          <a class="text-slate-500 font-inter text-sm font-medium hover:text-indigo-500 transition-colors" href="#">Trust &amp; Safety</a>
        </div>
        <div class="text-slate-500 font-inter text-sm font-medium">© 2024 Indigo Horizon Travel Group. All rights reserved.</div>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'UserTrangCheckOut'
}
</script>

<style>
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
body {
  font-family: 'Inter', sans-serif;
}
</style>