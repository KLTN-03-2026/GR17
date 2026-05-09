<template>
  <div class="login-page">
    <div class="login-shell">
      <header class="login-topbar">
        <button class="brand-link" type="button" @click="$router.push('/')">
          <span class="brand-link__mark">
            <i class="fas fa-route"></i>
          </span>
          <span class="brand-link__copy">
            <strong>Smart Travel</strong>
            <small>{{ copy.brandTagline }}</small>
          </span>
        </button>

        <div class="language-switch" role="group" :aria-label="copy.languageLabel">
          <button
            v-for="item in languageOptions"
            :key="item.value"
            type="button"
            :class="['language-switch__button', { 'language-switch__button--active': locale === item.value }]"
            @click="setLocale(item.value)"
          >
            {{ item.label }}
          </button>
        </div>
      </header>

      <main class="login-stage">
        <section class="login-panel">
          <p class="login-panel__eyebrow">{{ copy.panelEyebrow }}</p>
          <h2>{{ copy.panelTitle }}</h2>
          <p class="login-panel__summary">{{ copy.panelSummary }}</p>

          <div v-if="thongBaoLoi" class="notice notice--error">
            <i class="fas fa-circle-exclamation"></i>
            <span>{{ thongBaoLoi }}</span>
          </div>

          <div v-if="thongBaoThanhCong" class="notice notice--success">
            <i class="fas fa-circle-check"></i>
            <span>{{ thongBaoThanhCong }}</span>
          </div>

          <div class="customer-welcome">
            <p class="login-panel__hint">{{ copy.customerHint }}</p>
          </div>

          <form class="login-form" @submit.prevent="xuLyDangNhap">
            <label class="field">
              <span>{{ copy.fields.email }}</span>
              <div class="field__shell">
                <i class="fas fa-envelope"></i>
                <input
                  v-model.trim="form.email"
                  type="email"
                  autocomplete="username"
                  :placeholder="copy.placeholders.email"
                  @focus="clearMessages"
                >
              </div>
              <small v-if="errors.email" class="field__error">{{ errors.email }}</small>
            </label>

            <label class="field">
              <div class="field__top">
                <span>{{ copy.fields.password }}</span>
                <button class="field__link" type="button" @click="showForgotMessage">
                  {{ copy.fields.forgot }}
                </button>
              </div>
              <div class="field__shell">
                <i class="fas fa-lock"></i>
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  autocomplete="current-password"
                  :placeholder="copy.placeholders.password"
                  @focus="clearMessages"
                >
                <button class="field__toggle" type="button" @click="showPassword = !showPassword">
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
              <small v-if="errors.password" class="field__error">{{ errors.password }}</small>
            </label>

            <div class="form-meta">
              <label class="remember-box">
                <input v-model="form.rememberMe" type="checkbox">
                <span>{{ copy.fields.remember }}</span>
              </label>
            </div>

            <button class="login-button" type="submit" :disabled="dangTai">
              <i v-if="dangTai" class="fas fa-spinner fa-spin"></i>
              <span>{{ dangTai ? copy.buttons.loading : copy.buttons.submit }}</span>
              <i v-if="!dangTai" class="fas fa-arrow-right"></i>
            </button>
          </form>

          <p class="register-copy">
            {{ copy.registerPrompt }}
            <router-link to="/dang-ky">{{ copy.registerAction }}</router-link>
          </p>
        </section>
      </main>
    </div>

    <!-- FORGOT PASSWORD DIALOG -->
    <div v-if="forgotDialogOpen" class="forgot-backdrop" @click.self="forgotDialogOpen = false">
      <section class="forgot-dialog">
        <button class="forgot-dialog__close" type="button" @click="forgotDialogOpen = false">
          <i class="fas fa-times"></i>
        </button>
        <div class="forgot-dialog__icon">
          <i class="fas fa-key"></i>
        </div>
        <h2>{{ locale === 'vi' ? 'Quên mật khẩu?' : 'Forgot password?' }}</h2>
        <p>{{ locale === 'vi'
          ? 'Hiện tại hệ thống chưa hỗ trợ đặt lại mật khẩu trực tuyến. Vui lòng liên hệ quản trị viên để được hỗ trợ.'
          : 'Online password reset is not yet available. Please contact the administrator for assistance.'
        }}</p>
        <div class="forgot-dialog__contact">
          <div class="forgot-dialog__row">
            <i class="fas fa-envelope"></i>
            <span>admin@smarttravel.vn</span>
          </div>
          <div class="forgot-dialog__row">
            <i class="fas fa-phone"></i>
            <span>0123 456 789</span>
          </div>
        </div>
        <button class="forgot-dialog__btn" type="button" @click="forgotDialogOpen = false">
          {{ locale === 'vi' ? 'Đã hiểu' : 'Got it' }}
        </button>
      </section>
    </div>
  </div>
</template>

<script>
import { goiApi } from '../../services/httpClient.js';
import authStorage, { Role } from '../../services/authStorage.js';
const API_BASE = "/api";
const COPY = {
  vi: {
    brandTagline: "Khám phá Việt Nam cùng Smart Travel",
    languageLabel: "Chọn ngôn ngữ",
    languageOptions: { vi: "Tiếng Việt", en: "Tiếng Anh" },
    panelEyebrow: "Đăng nhập thành viên",
    panelTitle: "Chào mừng trở lại",
    panelSummary: "Nhập email và mật khẩu để tiếp tục hành trình của bạn.",
    customerHint: "Đăng nhập để xem các kế hoạch và hành trình đã lưu của bạn.",
    fields: {
      email: "Email thành viên",
      password: "Mật khẩu",
      remember: "Ghi nhớ đăng nhập",
      forgot: "Quên mật khẩu?",
    },
    placeholders: {
      email: "name@example.com",
      password: "Nhập mật khẩu của bạn",
    },
    buttons: {
      submit: "Đăng nhập",
      loading: "Đang xử lý...",
    },
    registerPrompt: "Chưa là thành viên?",
    registerAction: "Đăng ký ngay",
    messages: {
      loginSuccess: "Đăng nhập thành công. Chúc bạn một ngày tốt lành!",
      loginFailed: "Email hoặc mật khẩu không chính xác.",
      apiUnavailable: "Hệ thống đang bảo trì, vui lòng quay lại sau.",
      khachHangTaiThatBai: "Không thể tải hồ sơ khách hàng.",
      khachHangDangNhapThatBai: "Đăng nhập thất bại.",
      khachHangThieuToken: "Lỗi hệ thống: Token không hợp lệ.",
      khachHangThieuHoSo: "Không tìm thấy thông tin tài khoản.",
    },
    validation: {
      emailRequired: "Vui lòng nhập Email.",
      emailInvalid: "Email không đúng định dạng.",
      passwordRequired: "Vui lòng nhập mật khẩu.",
      passwordShort: "Mật khẩu tối thiểu 6 ký tự.",
    },
  },
  en: {
    brandTagline: "Explore Vietnam with Smart Travel",
    languageLabel: "Select Language",
    languageOptions: { vi: "Tiếng Việt", en: "English" },
    panelEyebrow: "Member Login",
    panelTitle: "Welcome Back",
    panelSummary: "Enter your email and password to continue your journey.",
    customerHint: "Login to view your saved plans and itineraries.",
    fields: {
      email: "Member Email",
      password: "Password",
      remember: "Remember me",
      forgot: "Forgot password?",
    },
    placeholders: {
      email: "name@example.com",
      password: "Enter your password",
    },
    buttons: {
      submit: "Sign In",
      loading: "Processing...",
    },
    registerPrompt: "Not a member yet?",
    registerAction: "Register now",
    messages: {
      loginSuccess: "Login successful. Have a great day!",
      loginFailed: "Incorrect email or password.",
      apiUnavailable: "System maintenance, please try again later.",
      khachHangTaiThatBai: "Failed to load profile.",
      khachHangDangNhapThatBai: "Login failed.",
      khachHangThieuToken: "System error: Invalid token.",
      khachHangThieuHoSo: "Account information not found.",
    },
    validation: {
      emailRequired: "Email is required.",
      emailInvalid: "Invalid email format.",
      passwordRequired: "Password is required.",
      passwordShort: "Minimum 6 characters.",
    },
  },
};
export default {
  name: "DangNhap",
  data() {
    return {
      locale: "vi",
      dangTai: false,
      showPassword: false,
      thongBaoLoi: "",
      thongBaoThanhCong: "",
      form: {
        email: "",
        password: "",
        rememberMe: false,
      },
      errors: {
        email: "",
        password: "",
      },
      forgotDialogOpen: false,
    };
  },
  computed: {
    copy() {
      return COPY[this.locale] || COPY.vi;
    },
    languageOptions() {
      return [
        { value: "vi", label: this.copy.languageOptions.vi },
        { value: "en", label: this.copy.languageOptions.en },
      ];
    },
  },
  watch: {
    locale(value) {
      localStorage.setItem("preferred_locale", value);
      document.documentElement.lang = value;
    },
  },
  methods: {
    setLocale(value) {
      if (value === "vi" || value === "en") {
        this.locale = value;
      }
    },
    saveAuthSession({ token, user, type }) {
      authStorage.saveSession(Role.CUSTOMER, { token, user });
    },
    async dangNhapKhachHang() {
      const phanHoi = await goiApi(`${API_BASE}/khach-hang/login`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify({
          Email: this.form.email,
          Mat_khau: this.form.password,
        }),
      });

      const data = await phanHoi.json().catch(() => ({}));

      if (!phanHoi.ok) {
        return {
          ok: false,
          status: phanHoi.status,
          message: data?.message || this.copy.messages.khachHangDangNhapThatBai,
        };
      }

      const token = String(data?.token || "").trim();
      if (!token) {
        return {
          ok: false,
          status: phanHoi.status,
          message: this.copy.messages.khachHangThieuToken,
        };
      }

      const customer = data?.user || data?.data?.user || data?.data?.khach_hang || null;
      if (!customer) {
        return {
          ok: false,
          status: phanHoi.status,
          message: this.copy.messages.khachHangThieuHoSo,
        };
      }

      return {
        ok: true,
        token,
        user: customer,
      };
    },
    hoanTatDangNhap(duLieuPhanHoi) {
      this.saveAuthSession({
        token: duLieuPhanHoi.token,
        user: duLieuPhanHoi.user,
        type: 'customer',
      });

      if (this.form.rememberMe) {
        localStorage.setItem("rememberEmail", this.form.email);
      } else {
        localStorage.removeItem("rememberEmail");
      }

      this.thongBaoThanhCong = this.copy.messages.loginSuccess;

      const redirectPath = String(this.$route.query.redirect || "/khach-hang/dashboard");

      setTimeout(() => {
        this.$router.push(redirectPath);
      }, 700);
    },
    clearMessages() {
      this.thongBaoLoi = "";
      this.thongBaoThanhCong = "";
    },
    validateEmail(email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },
    validateForm() {
      this.errors = {
        email: "",
        password: "",
      };

      if (!this.form.email) {
        this.errors.email = this.copy.validation.emailRequired;
      } else if (!this.validateEmail(this.form.email)) {
        this.errors.email = this.copy.validation.emailInvalid;
      }

      if (!this.form.password) {
        this.errors.password = this.copy.validation.passwordRequired;
      } else if (this.form.password.length < 6) {
        this.errors.password = this.copy.validation.passwordShort;
      }

      return !this.errors.email && !this.errors.password;
    },
    async xuLyDangNhap() {
      if (!this.validateForm()) return;

      this.dangTai = true;
      this.clearMessages();

      try {
        const loginResult = await this.dangNhapKhachHang();

        if (loginResult.ok) {
          this.hoanTatDangNhap(loginResult);
          return;
        }

        this.thongBaoLoi = loginResult.message || this.copy.messages.loginFailed;
      } catch (error) {
        console.error("Lỗi đăng nhập:", error);
        this.thongBaoLoi = this.copy.messages.apiUnavailable;
      } finally {
        this.dangTai = false;
      }
    },
    showForgotMessage() {
      this.forgotDialogOpen = true;
    },
  },
  mounted() {
    const rememberedLocale = localStorage.getItem("preferred_locale");
    if (rememberedLocale === "vi" || rememberedLocale === "en") {
      this.locale = rememberedLocale;
    }

    document.documentElement.lang = this.locale;

    const rememberEmail = localStorage.getItem("rememberEmail");
    if (rememberEmail) {
      this.form.email = rememberEmail;
      this.form.rememberMe = true;
    }

    // Check if already logged in as customer
    const customerToken = authStorage.getToken(Role.CUSTOMER);
    if (customerToken) {
      const redirectPath = String(this.$route.query.redirect || "/khach-hang/dashboard");
      this.$router.push(redirectPath);
    }
  },
};
</script>


<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap");

.login-page {
  --ink: #f6f8ff;
  --muted: rgba(230, 238, 255, 0.78);
  --panel-ink: #132238;
  --panel-muted: #60748f;
  --accent: #1d9bf0;
  --accent-deep: #0f63c9;
  position: relative;
  min-height: 100vh;
  overflow: hidden;
  color: var(--ink);
  background-color: #081b2d;
  font-family: "Inter", sans-serif;
}

.login-page::before,
.login-page::after {
  content: "";
  position: absolute;
  inset: 0;
}

.login-page::before {
  background:
    linear-gradient(115deg, rgba(5, 19, 33, 0.28) 0%, rgba(6, 27, 49, 0.04) 38%, rgba(8, 23, 39, 0.42) 100%),
    url("https://lh3.googleusercontent.com/aida-public/AB6AXuBbR343yTgUwz1_CIFuP7W-h3y5Eu6dVetjn2OYgDxL3AWlpZjm7x4ZL3ATy1IuLzNhOeTwZmmfWzclpkXnZ1KvpCU96M76eqwc-rV2-BCmJMMyHLOTOigmrZ59y6B-GsrlSyNghJuFMbI6jr_cyAH4Wgu_wY03Ioco2TKSZhomDP6G_Pn5suoLWXC776jEWeCSmmipomVcLf5DQXtFM-NXRM--Ho-MZ8m-1n1gN2Z2EQh7N7yz4lulZjgovOH8JmIYs0IzkmxVyGE")
      center/cover no-repeat;
  transform: scale(1.04);
  animation: backdropDrift 18s ease-in-out infinite alternate;
}

.login-page::after {
  background:
    radial-gradient(circle at 14% 14%, rgba(65, 156, 255, 0.28), transparent 24%),
    radial-gradient(circle at 86% 10%, rgba(27, 211, 255, 0.18), transparent 18%),
    linear-gradient(180deg, rgba(5, 16, 29, 0.18) 0%, rgba(5, 14, 24, 0.72) 100%);
}

.login-shell {
  position: relative;
  z-index: 1;
  width: min(1360px, 100%);
  min-height: 100vh;
  margin: 0 auto;
  padding: 28px clamp(20px, 4vw, 48px) 36px;
  display: grid;
  grid-template-rows: auto 1fr;
  gap: 28px;
}

.login-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.brand-link {
  display: inline-flex;
  align-items: center;
  gap: 0.95rem;
  padding: 0;
  border: 0;
  background: transparent;
  color: inherit;
  text-align: left;
}

.brand-link__mark {
  width: 54px;
  height: 54px;
  border-radius: 16px;
  display: grid;
  place-items: center;
  color: #ffffff;
  background: linear-gradient(135deg, rgba(29, 155, 240, 0.95), rgba(15, 99, 201, 0.95));
  box-shadow: 0 18px 32px rgba(6, 22, 43, 0.28);
}

.brand-link__copy {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
}

.brand-link__copy strong,
.login-panel h2 {
  font-family: "Manrope", sans-serif;
}

.brand-link__copy strong {
  font-size: 1.9rem;
  font-weight: 800;
  letter-spacing: -0.05em;
}

.brand-link__copy small {
  color: var(--muted);
  font-size: 0.95rem;
}

.language-switch {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.35rem;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.14);
  border: 1px solid rgba(255, 255, 255, 0.22);
  backdrop-filter: blur(16px);
}

.language-switch__button {
  min-height: 42px;
  padding: 0 1rem;
  border: 0;
  border-radius: 999px;
  background: transparent;
  color: rgba(244, 248, 255, 0.82);
  font-weight: 700;
  transition: background-color 0.22s ease, color 0.22s ease, transform 0.22s ease;
}

.language-switch__button--active {
  background: #ffffff;
  color: #114685;
  box-shadow: 0 10px 26px rgba(7, 17, 30, 0.14);
}

.login-stage {
  min-height: calc(100vh - 128px);
  display: flex;
  align-items: center;
  justify-content: center;
}

.login-panel {
  width: min(100%, 440px);
  margin: 0 auto;
  padding: 2rem;
  border-radius: 30px;
  background: rgba(247, 250, 255, 0.9);
  border: 1px solid rgba(255, 255, 255, 0.58);
  backdrop-filter: blur(20px);
  box-shadow: 0 26px 60px rgba(4, 15, 28, 0.28);
  color: var(--panel-ink);
  animation: fadeUp 0.8s ease 0.08s both;
}

.login-panel__eyebrow {
  margin: 0 0 1rem;
  font-size: 0.8rem;
  font-weight: 800;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: #0f63c9;
}

.login-panel h2 {
  margin: 0;
  font-size: 2.35rem;
  line-height: 1.02;
  letter-spacing: -0.05em;
  font-weight: 800;
}

.login-panel__summary {
  margin: 0.9rem 0 0;
  color: var(--panel-muted);
  line-height: 1.7;
}

.notice {
  display: flex;
  align-items: flex-start;
  gap: 0.8rem;
  margin-top: 1.15rem;
  padding: 0.95rem 1rem;
  border-radius: 18px;
  line-height: 1.55;
}

.notice--error {
  color: #b42318;
  background: #fff1f2;
  border: 1px solid #fecdd3;
}

.notice--success {
  color: #027a48;
  background: #ecfdf3;
  border: 1px solid #abefc6;
}

.role-switch {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.45rem;
  padding: 0.45rem;
  margin-top: 1.5rem;
  border-radius: 999px;
  background: #e8f0fe;
}

.role-switch__button {
  min-height: 50px;
  border: 0;
  border-radius: 999px;
  background: transparent;
  color: #4b617b;
  font-weight: 800;
  transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
}

.role-switch__button--active {
  background: #ffffff;
  color: #0f63c9;
  box-shadow: 0 10px 22px rgba(15, 99, 201, 0.14);
}

.login-panel__hint {
  margin: 0.85rem 0 0;
  color: var(--panel-muted);
  font-size: 0.92rem;
  line-height: 1.6;
}

.login-form {
  display: grid;
  gap: 1.05rem;
  margin-top: 1.4rem;
}

.field {
  display: grid;
  gap: 0.5rem;
}

.field span {
  font-weight: 700;
}

.field__top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
}

.field__link {
  padding: 0;
  border: 0;
  background: transparent;
  color: #2563eb;
  font-weight: 700;
}

.field__shell {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  min-height: 58px;
  padding: 0 1rem;
  border-radius: 18px;
  border: 1px solid #d6e0ed;
  background: rgba(255, 255, 255, 0.84);
  transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
}

.field__shell:focus-within {
  border-color: #1d9bf0;
  box-shadow: 0 0 0 5px rgba(29, 155, 240, 0.14);
  transform: translateY(-1px);
}

.field__shell i,
.field__toggle {
  color: #90a3ba;
}

.field__shell input {
  flex: 1;
  border: 0;
  outline: 0;
  background: transparent;
  color: var(--panel-ink);
  font-size: 1rem;
}

.field__shell input::placeholder {
  color: #9aabc0;
}

.field__toggle {
  padding: 0;
  border: 0;
  background: transparent;
}

.field__error {
  font-size: 0.85rem;
  color: #b42318;
}

.form-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.remember-box {
  display: inline-flex;
  align-items: center;
  gap: 0.7rem;
  color: var(--panel-muted);
}

.remember-box input {
  width: 18px;
  height: 18px;
  accent-color: #1d9bf0;
}

.login-button {
  min-height: 60px;
  margin-top: 0.35rem;
  border: 0;
  border-radius: 18px;
  background: linear-gradient(135deg, var(--accent), var(--accent-deep));
  color: #ffffff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  font-size: 1rem;
  font-weight: 800;
  box-shadow: 0 20px 36px rgba(15, 99, 201, 0.24);
  transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
}

.login-button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 24px 40px rgba(15, 99, 201, 0.28);
}

.login-button:disabled {
  opacity: 0.75;
  cursor: not-allowed;
}

.register-copy {
  margin: 1.35rem 0 0;
  color: var(--panel-muted);
  text-align: center;
}

.register-copy a {
  color: #0f63c9;
  font-weight: 800;
  text-decoration: none;
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(18px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes backdropDrift {
  from {
    transform: scale(1.04) translate3d(0, 0, 0);
  }
  to {
    transform: scale(1.08) translate3d(-1.2%, -0.8%, 0);
  }
}

@media (max-width: 980px) {
  .login-stage {
    min-height: calc(100vh - 140px);
  }
}

@media (max-width: 640px) {
  .login-shell {
    padding: 18px 16px 26px;
  }

  .login-topbar {
    flex-direction: column;
    align-items: stretch;
  }

  .brand-link,
  .language-switch {
    width: 100%;
  }

  .language-switch {
    justify-content: space-between;
  }

  .language-switch__button {
    flex: 1;
  }

  .login-panel {
    padding: 1.4rem;
    border-radius: 24px;
  }

  .role-switch {
    border-radius: 20px;
  }

  .role-switch__button {
    border-radius: 16px;
  }
}

/* ── Forgot Password Dialog ── */
.forgot-backdrop {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: grid;
  place-items: center;
  background: rgba(10, 15, 30, 0.6);
  backdrop-filter: blur(8px);
}

.forgot-dialog {
  position: relative;
  width: min(420px, calc(100% - 32px));
  padding: 36px 32px 28px;
  border-radius: 28px;
  background: rgba(255, 255, 255, 0.97);
  box-shadow: 0 24px 48px rgba(0, 0, 0, 0.18);
  text-align: center;
}

.forgot-dialog__close {
  position: absolute;
  top: 14px;
  right: 14px;
  width: 36px;
  height: 36px;
  border: 0;
  border-radius: 50%;
  background: #f1f5f9;
  color: #64748b;
  cursor: pointer;
  display: grid;
  place-items: center;
}

.forgot-dialog__icon {
  width: 64px;
  height: 64px;
  margin: 0 auto 18px;
  border-radius: 50%;
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
  display: grid;
  place-items: center;
  color: #ffffff;
  font-size: 1.5rem;
}

.forgot-dialog h2 {
  margin: 0 0 12px;
  color: #111827;
  font-size: 1.35rem;
  font-weight: 800;
}

.forgot-dialog p {
  margin: 0 0 20px;
  color: #64748b;
  line-height: 1.6;
  font-size: 0.95rem;
}

.forgot-dialog__contact {
  display: grid;
  gap: 10px;
  margin-bottom: 22px;
}

.forgot-dialog__row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  color: #1e40af;
  font-weight: 600;
}

.forgot-dialog__row i {
  width: 18px;
}

.forgot-dialog__btn {
  min-height: 48px;
  width: 100%;
  border: 0;
  border-radius: 16px;
  background: linear-gradient(135deg, #1d4ed8 0%, #4338ca 100%);
  color: #ffffff;
  font-weight: 800;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 12px 28px rgba(37, 99, 235, 0.22);
}
</style>






