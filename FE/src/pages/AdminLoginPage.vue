<template>
  <div class="min-h-screen bg-[linear-gradient(135deg,#031525_0%,#08233b_38%,#f4f7fb_38%,#f4f7fb_100%),radial-gradient(circle_at_top_left,rgba(14,165,233,0.22),transparent_28%),radial-gradient(circle_at_bottom_right,rgba(34,197,94,0.16),transparent_24%)] box-border">
    <div class="min-h-screen grid grid-cols-[1.05fr_0.95fr] md:grid-cols-1">
      <section class="relative overflow-hidden p-9 pb-7.5 text-blue-50 bg-[linear-gradient(160deg,rgba(3,21,37,0.18),rgba(3,21,37,0.6)),url('https://images.unsplash.com/photo-1519608487953-e999c86e7455?auto=format&fit=crop&w=1400&q=80')] bg-center bg-cover flex flex-col justify-between md:min-h-[44vh] md:p-6 md:pb-6">
        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(6,18,34,0.18)_0%,rgba(6,18,34,0.72)_100%),radial-gradient(circle_at_20%_20%,rgba(56,189,248,0.18),transparent_24%)]"></div>
        <div class="relative z-10 flex items-center justify-between gap-4 md:flex-col md:items-start">
          <div class="text-2xl font-extrabold tracking-tight">CoreVue Admin</div>
          <div class="px-4 py-2.5 border border-white/16 rounded-full bg-white/8 text-xs font-bold uppercase tracking-widest">Control Center</div>
        </div>
        <div class="relative z-10 max-w-[620px] pt-20 pb-15 md:pt-12 md:pb-10">
          <p class="mb-4 text-blue-300 text-sm font-bold uppercase tracking-widest">Administration Portal</p>
          <h1 class="m-0 max-w-[560px] text-5xl leading-tight tracking-tight md:text-4xl">Điều hành hệ thống du lịch trong một không gian tập trung.</h1>
          <p class="mt-7 max-w-[520px] text-blue-50/80 text-lg leading-relaxed">
            Theo dõi phiên đăng nhập quản trị, kiểm soát dữ liệu và truy cập nhanh vào bảng điều khiển vận hành.
          </p>
          <div class="mt-9.5 grid grid-cols-3 gap-4 md:grid-cols-1">
            <div class="p-4.5 border border-white/14 rounded-3xl bg-slate-800/36 backdrop-blur-md">
              <span class="block mb-2.5 text-blue-200/82 text-xs uppercase tracking-wider">Realtime</span>
              <strong class="text-lg font-extrabold">24/7</strong>
            </div>
            <div class="p-4.5 border border-white/14 rounded-3xl bg-slate-800/36 backdrop-blur-md">
              <span class="block mb-2.5 text-blue-200/82 text-xs uppercase tracking-wider">Bảo mật</span>
              <strong class="text-lg font-extrabold">Admin Auth</strong>
            </div>
            <div class="p-4.5 border border-white/14 rounded-3xl bg-slate-800/36 backdrop-blur-md">
              <span class="block mb-2.5 text-blue-200/82 text-xs uppercase tracking-wider">Endpoint</span>
              <strong class="text-lg font-extrabold">/api/admin/login</strong>
            </div>
          </div>
        </div>
        <div class="relative z-10 inline-flex items-center gap-3 text-blue-50/84 text-base">
          <span class="w-3 h-3 rounded-full bg-green-500 shadow-[0_0_0_8px_rgba(34,197,94,0.12)]"></span>
          <span>Sẵn sàng cho ca vận hành hôm nay</span>
        </div>
      </section>
      <section class="flex items-center justify-center p-10 px-7 md:p-5 md:px-4.5">
        <div class="w-full max-w-[520px] p-10 border border-slate-400/18 rounded-3xl bg-white/88 shadow-2xl shadow-slate-900/12 backdrop-blur-md md:p-6 md:rounded-2xl">
          <div class="mb-7">
            <p class="m-0 mb-2.5 text-sky-700 text-xs font-extrabold uppercase tracking-widest">Admin Sign In</p>
            <h2 class="m-0 text-slate-900 text-5xl leading-tight tracking-tight md:text-4xl">Đăng nhập quản trị</h2>
            <p class="mt-3.5 text-slate-600 leading-relaxed">
              Nhập email quản trị và mật khẩu để truy cập hệ thống nội bộ.
            </p>
          </div>
          <div v-if="errorMessage" class="mb-4.5 p-3.5 rounded-2xl text-base leading-relaxed text-red-800 bg-red-50 border border-red-200">
            {{ errorMessage }}
          </div>
          <div v-if="successMessage" class="mb-4.5 p-3.5 rounded-2xl text-base leading-relaxed text-green-800 bg-green-50 border border-green-200">
            {{ successMessage }}
          </div>
          <form class="flex flex-col gap-4.5" @submit.prevent="handleLogin">
            <label class="flex flex-col gap-2.5" for="admin-email">
              <span class="text-slate-900 text-base font-bold">Email quản trị</span>
              <input
                id="admin-email"
                v-model.trim="formData.email"
                autocomplete="email"
                placeholder="admin@example.com"
                type="email"
                class="w-full p-3.75 border border-slate-300 rounded-2xl bg-white text-slate-900 outline-none transition-all duration-200 focus:border-sky-500 focus:shadow-[0_0_0_16px_rgba(14,165,233,0.14)]"
              />
            </label>
            <label class="flex flex-col gap-2.5" for="admin-password">
              <span class="text-slate-900 text-base font-bold">Mật khẩu</span>
              <div class="relative">
                <input
                  id="admin-password"
                  v-model="formData.password"
                  :type="showPassword ? 'text' : 'password'"
                  autocomplete="current-password"
                  placeholder="Nhap mat khau"
                  class="w-full p-3.75 pr-20 border border-slate-300 rounded-2xl bg-white text-slate-900 outline-none transition-all duration-200 focus:border-sky-500 focus:shadow-[0_0_0_16px_rgba(14,165,233,0.14)]"
                />
                <button
                  class="absolute top-1/2 right-3.5 -translate-y-1/2 px-2.5 py-1.5 rounded-xl bg-sky-100 text-sky-700 text-sm font-bold border-none"
                  type="button"
                  @click="showPassword = !showPassword"
                >
                  {{ showPassword ? 'Ẩn' : 'Hiện' }}
                </button>
              </div>
            </label>
            <div class="flex items-center justify-between gap-4 mt-1 md:flex-col md:items-start">
              <label class="inline-flex items-center gap-2.5 text-slate-700 text-base" for="remember-admin">
                <input
                  id="remember-admin"
                  v-model="formData.rememberMe"
                  type="checkbox"
                  class="w-4 h-4"
                />
                <span>Ghi nhớ email quản trị</span>
              </label>
              <span class="text-slate-500 text-sm">Chỉ dùng trên máy cá nhân</span>
            </div>
            <button
              :disabled="isLoading"
              class="mt-2 p-4 px-5.5 rounded-2xl bg-gradient-to-br from-sky-600 to-teal-700 text-white text-base font-extrabold tracking-tight transition-all duration-200 hover:-translate-y-0.5 hover:shadow-2xl hover:shadow-teal-500/28 disabled:opacity-70 disabled:cursor-not-allowed shadow-xl shadow-teal-500/22 border-none"
              type="submit"
            >
              <span v-if="isLoading">Dang dang nhap...</span>
              <span v-else>Dang nhap admin</span>
            </button>
          </form>
          <div class="mt-5.5 pt-5 border-t border-slate-200 flex flex-col gap-2">
            <span class="text-slate-500 text-xs font-bold uppercase tracking-wider">API</span>
            <code class="text-slate-900 text-sm break-all">POST http://127.0.0.1:8000/api/admin/login</code>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
const ADMIN_LOGIN_URL = 'http://127.0.0.1:8000/api/admin/login';

export default {
  name: 'AdminLoginPage',
  data() {
    return {
      formData: {
        email: '',
        password: '',
        rememberMe: false,
      },
      showPassword: false,
      isLoading: false,
      errorMessage: '',
      successMessage: '',
    };
  },
  methods: {
    getResponseToken(data) {
      return data?.token || data?.access_token || data?.data?.token || '';
    },
    getResponseAdmin(data) {
      return data?.admin || data?.user || data?.data?.admin || data?.data?.user || {};
    },
    validateForm() {
      if (!this.formData.email || !this.formData.password) {
        this.errorMessage = 'Vui long nhap day du email va mat khau.';
        return false;
      }

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(this.formData.email)) {
        this.errorMessage = 'Email quan tri khong hop le.';
        return false;
      }

      return true;
    },
    persistRememberEmail() {
      if (this.formData.rememberMe) {
        localStorage.setItem('adminRememberEmail', this.formData.email);
        return;
      }

      localStorage.removeItem('adminRememberEmail');
    },
    async handleLogin() {
      this.errorMessage = '';
      this.successMessage = '';

      if (!this.validateForm()) {
        return;
      }

      this.isLoading = true;

      try {
        const response = await fetch(ADMIN_LOGIN_URL, {
          method: 'POST',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            email: this.formData.email,
            Mat_khau: this.formData.password,
          }),
        });

        const data = await response.json().catch(() => ({}));
        const token = this.getResponseToken(data);
        const admin = this.getResponseAdmin(data);

        if (!response.ok || !token) {
          this.errorMessage =
            data?.message || data?.error || 'Dang nhap that bai. Vui long kiem tra lai thong tin.';
          return;
        }

        localStorage.setItem('adminToken', token);
        localStorage.setItem('adminProfile', JSON.stringify(admin));
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(admin));
        this.persistRememberEmail();

        this.successMessage = 'Dang nhap thanh cong. Dang chuyen huong...';

        setTimeout(() => {
          this.$router.push('/');
        }, 800);
      } catch (error) {
        console.error('Admin login error:', error);
        this.errorMessage = 'Khong the ket noi den may chu. Kiem tra backend va thu lai.';
      } finally {
        this.isLoading = false;
      }
    },
  },
  mounted() {
    const rememberedEmail = localStorage.getItem('adminRememberEmail');

    if (rememberedEmail) {
      this.formData.email = rememberedEmail;
      this.formData.rememberMe = true;
    }
  },
};
</script>


