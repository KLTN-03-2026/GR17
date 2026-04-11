<template>
  <div class="min-h-screen flex flex-col bg-white">
    <!-- Mobile Header -->
    <header class="md:hidden px-6 py-6 flex items-center justify-between">
      <div class="text-xl font-black text-primary tracking-tighter">
        Indigo Horizon
      </div>
    </header>

    <main class="flex-grow flex flex-col md:flex-row min-h-screen md:pt-0">
      <!-- Left Section - Dark Hero (Hidden on Mobile) -->
      <section class="hidden md:flex md:w-1/2 relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-700 to-slate-800">
        <div class="absolute inset-0 z-0">
          <img
            alt="Mountain landscape"
            class="w-full h-full object-cover opacity-60"
            src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&h=1600&fit=crop"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-800/50 to-transparent"></div>
        </div>

        <!-- Header Brand on Left -->
        <div class="absolute top-8 left-8 z-20">
          <div class="text-2xl font-black text-primary tracking-tighter">
            Indigo Horizon
          </div>
        </div>

        <!-- Hero Text -->
        <div class="relative z-10 flex flex-col justify-center px-12 xl:px-16 w-full h-full">
          <h1 class="text-5xl lg:text-6xl font-black text-white leading-tight tracking-tight mb-8">
            Explore the<br />World<br />Unbound
          </h1>
          <p class="text-white/80 text-base lg:text-lg font-medium max-w-sm border-l-4 border-primary pl-6 py-2">
            Your intelligent journey starts here. Plan, discover, and wander with Indigo Horizon's smart travel ecosystem.
          </p>
        </div>

        <!-- Bottom User Stats -->
        <div class="absolute bottom-8 left-8 z-10 flex items-center space-x-3">
          <div class="flex -space-x-2">
            <img alt="User 1" class="w-9 h-9 rounded-full border-2 border-white object-cover" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop" />
            <img alt="User 2" class="w-9 h-9 rounded-full border-2 border-white object-cover" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop" />
            <img alt="User 3" class="w-9 h-9 rounded-full border-2 border-white object-cover" src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop" />
          </div>
          <span class="text-white text-xs font-bold tracking-wide">Joined by 2k+ voyagers this week</span>
        </div>
      </section>

      <!-- Right Section - Login Form -->
      <section class="flex-grow md:w-1/2 bg-white flex flex-col justify-center items-center px-6 py-16 lg:px-12">
        <div class="w-full max-w-sm">
          <!-- Header -->
          <div class="mb-12 text-center md:text-left">
            <h2 class="text-4xl font-black text-slate-900 tracking-tight mb-3">Welcome Back</h2>
            <p class="text-slate-600 font-medium text-base">Please enter your details to access your dashboard.</p>
          </div>

          <!-- Social Buttons -->
          <div class="grid grid-cols-2 gap-3 mb-8">
            <button
              class="flex items-center justify-center gap-2 px-4 py-3 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 transition-all duration-200 active:scale-95 shadow-sm"
              type="button"
            >
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05" />
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
              </svg>
              <span class="text-sm font-bold text-slate-700">Google</span>
            </button>
            <button
              class="flex items-center justify-center gap-2 px-4 py-3 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 transition-all duration-200 active:scale-95 shadow-sm"
              type="button"
            >
              <svg class="w-5 h-5" fill="#1877F2" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
              </svg>
              <span class="text-sm font-bold text-slate-700">Facebook</span>
            </button>
          </div>

          <!-- Divider -->
          <div class="relative flex items-center justify-center mb-8">
            <div class="flex-grow border-t border-slate-200"></div>
            <span class="flex-shrink mx-4 text-slate-400 text-xs font-bold tracking-widest uppercase">Or continue with email</span>
            <div class="flex-grow border-t border-slate-200"></div>
          </div>

          <!-- Error Alert -->
          <div v-if="errorMessage" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm font-medium">
            {{ errorMessage }}
          </div>

          <!-- Success Alert -->
          <div v-if="successMessage" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm font-medium">
            {{ successMessage }}
          </div>

          <!-- Login Form -->
          <form class="space-y-5" @submit.prevent="handleLogin">
            <!-- Email Field -->
            <div>
              <label class="block text-sm font-bold text-slate-900 mb-2" for="email">Email Address</label>
              <input
                id="email"
                v-model="formData.email"
                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder:text-slate-400 font-medium text-slate-900"
                placeholder="voyager@horizon.com"
                type="email"
              />
            </div>

            <!-- Password Field -->
            <div>
              <div class="flex justify-between items-center mb-2">
                <label class="block text-sm font-bold text-slate-900" for="password">Password</label>
                <a class="text-xs font-bold text-primary hover:text-primary-dark transition-colors" href="#">
                  Forgot password?
                </a>
              </div>
              <div class="relative">
                <input
                  id="password"
                  v-model="formData.password"
                  :type="showPassword ? 'text' : 'password'"
                  class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder:text-slate-400 font-medium text-slate-900"
                  placeholder="••••••••"
                />
                <button
                  class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                  type="button"
                  @click="showPassword = !showPassword"
                >
                  <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
              <input
                id="remember"
                v-model="formData.rememberMe"
                class="w-4 h-4 text-primary bg-white border-slate-300 rounded focus:ring-primary focus:ring-offset-0 cursor-pointer"
                type="checkbox"
              />
              <label class="ml-3 text-sm font-medium text-slate-700 select-none cursor-pointer" for="remember">
                Remember me for 30 days
              </label>
            </div>

            <!-- Submit Button -->
            <button
              :disabled="isLoading"
              class="w-full py-3 bg-primary text-white font-bold rounded-lg shadow-md hover:shadow-lg hover:scale-[1.01] active:scale-95 transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed mt-6"
              type="submit"
            >
              <span v-if="!isLoading" class="flex items-center gap-2">
                Sign In
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
              </span>
              <span v-else class="flex items-center gap-2">
                <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                  <path class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" fill="currentColor" />
                </svg>
                Signing in...
              </span>
            </button>
          </form>

          <!-- Sign Up Link -->
          <p class="mt-8 text-center text-sm font-medium text-slate-600">
            Don't have an account yet? <router-link class="text-primary font-bold hover:underline" to="/dang-ky">Sign Up</router-link>
          </p>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="w-full py-6 px-6 bg-white border-t border-slate-100 md:border-t-0">
      <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex flex-col md:flex-row items-center gap-2 md:gap-6">
          <span class="font-bold text-primary text-sm">Indigo Horizon</span>
          <span class="text-xs text-slate-500">© 2024 Indigo Horizon Travel. All rights reserved.</span>
        </div>
        <nav class="flex items-center gap-6">
          <a class="text-xs text-slate-500 hover:text-slate-700 transition-colors" href="#">Privacy Policy</a>
          <a class="text-xs text-slate-500 hover:text-slate-700 transition-colors" href="#">Terms of Service</a>
          <a class="text-xs text-slate-500 hover:text-slate-700 transition-colors" href="#">Cookie Settings</a>
        </nav>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'LoginPage',
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
    async handleLogin() {
      // Validation
      if (!this.formData.email || !this.formData.password) {
        this.errorMessage = 'Please enter both email and password';
        return;
      }

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(this.formData.email)) {
        this.errorMessage = 'Please enter a valid email address';
        return;
      }

      if (this.formData.password.length < 6) {
        this.errorMessage = 'Password must be at least 6 characters';
        return;
      }

      this.isLoading = true;
      this.errorMessage = '';
      this.successMessage = '';

      try {
        const response = await fetch('http://127.0.0.1:8000/api/khach-hang/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            Email: this.formData.email,
            Mat_khau: this.formData.password,
          }),
        });

        const data = await response.json();

        if (response.ok && data.token) {
          localStorage.setItem('token', data.token);
          localStorage.setItem('user', JSON.stringify(data.user || {}));

          if (this.formData.rememberMe) {
            localStorage.setItem('rememberEmail', this.formData.email);
          } else {
            localStorage.removeItem('rememberEmail');
          }

          this.successMessage = 'Login successful! Redirecting...';
          setTimeout(() => {
            this.$router.push('/');
          }, 1500);
        } else {
          this.errorMessage = data.message || 'Login failed. Please try again.';
        }
      } catch (error) {
        this.errorMessage = 'Network error. Please check your connection.';
        console.error('Login error:', error);
      } finally {
        this.isLoading = false;
      }
    },
  },
  mounted() {
    const rememberEmail = localStorage.getItem('rememberEmail');
    if (rememberEmail) {
      this.formData.email = rememberEmail;
      this.formData.rememberMe = true;
    }
  },
};
</script>


