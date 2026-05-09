<template>
  <div class="login-page admin-theme">
    <div class="login-shell">
      <header class="login-topbar">
        <button class="brand-link" type="button" @click="$router.push('/')">
          <span class="brand-link__mark">
            <i class="fas fa-shield-halved"></i>
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

          <form class="login-form" @submit.prevent="xuLyDangNhap">
            <label class="field">
              <span>{{ copy.fields.email }}</span>
              <div class="field__shell">
                <i class="fas fa-user-shield"></i>
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
                <i class="fas fa-key"></i>
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

          <footer class="admin-footer">
            <p>{{ copy.adminNote }}</p>
          </footer>
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
          <i class="fas fa-lock"></i>
        </div>
        <h2>{{ locale === 'vi' ? 'Khôi phục quyền truy cập?' : 'Need Access?' }}</h2>
        <p>{{ locale === 'vi'
          ? 'Vui lòng liên hệ bộ phận Kỹ thuật hệ thống hoặc Quản trị viên cấp cao để yêu cầu cấp lại mật khẩu.'
          : 'Please contact the System Engineering department or a Senior Administrator to request a password reset.'
        }}</p>
        <div class="forgot-dialog__contact">
          <div class="forgot-dialog__row">
            <i class="fas fa-envelope"></i>
            <span>tech@smarttravel.vn</span>
          </div>
          <div class="forgot-dialog__row">
            <i class="fas fa-phone"></i>
            <span>0123 456 789 (Ext: 101)</span>
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
    brandTagline: "Hệ thống Quản trị Nội bộ",
    languageLabel: "Chọn ngôn ngữ",
    languageOptions: { vi: "Tiếng Việt", en: "Tiếng Anh" },
    panelEyebrow: "Admin Portal",
    panelTitle: "Đăng nhập Quản trị",
    panelSummary: "Vui lòng xác thực tài khoản để truy cập dashboard quản lý.",
    fields: {
      email: "Tài khoản (Email)",
      password: "Mật khẩu bảo mật",
      remember: "Duy trì đăng nhập",
      forgot: "Khôi phục mật khẩu?",
    },
    placeholders: {
      email: "admin@smarttravel.vn",
      password: "••••••••",
    },
    buttons: {
      submit: "Vào hệ thống",
      loading: "Đang xác thực...",
    },
    adminNote: "Hệ thống bảo mật cao. Chỉ dành cho nhân viên được ủy quyền.",
    messages: {
      loginSuccess: "Xác thực thành công. Đang chuyển hướng đến Dashboard...",
      loginFailed: "Thông tin xác thực không đúng. Vui lòng kiểm tra lại.",
      apiUnavailable: "Không thể kết nối đến máy chủ xác thực.",
      adminLoginFailed: "Đăng nhập quản trị thất bại.",
      adminTokenMissing: "Lỗi hệ thống: Không nhận được mã truy cập.",
    },
    validation: {
      emailRequired: "Email quản trị là bắt buộc.",
      emailInvalid: "Email không đúng định dạng.",
      passwordRequired: "Mật khẩu là bắt buộc.",
      passwordShort: "Mật khẩu bảo mật tối thiểu 6 ký tự.",
    },
  },
  en: {
    brandTagline: "Internal Administration System",
    languageLabel: "Select Language",
    languageOptions: { vi: "Tiếng Việt", en: "English" },
    panelEyebrow: "Admin Portal",
    panelTitle: "Admin Login",
    panelSummary: "Please authenticate to access the management dashboard.",
    fields: {
      email: "Admin Email",
      password: "Secure Password",
      remember: "Keep me logged in",
      forgot: "Access recovery?",
    },
    placeholders: {
      email: "admin@smarttravel.vn",
      password: "••••••••",
    },
    buttons: {
      submit: "Sign In",
      loading: "Authenticating...",
    },
    adminNote: "High security system. Authorized personnel only.",
    messages: {
      loginSuccess: "Authentication successful. Redirecting to Dashboard...",
      loginFailed: "Invalid credentials. Please try again.",
      apiUnavailable: "Cannot connect to authentication server.",
      adminLoginFailed: "Admin login failed.",
      adminTokenMissing: "System error: Access token missing.",
    },
    validation: {
      emailRequired: "Admin email is required.",
      emailInvalid: "Invalid email format.",
      passwordRequired: "Password is required.",
      passwordShort: "Minimum 6 characters required.",
    },
  },
};

export default {
  name: "DangNhapAdmin",
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
  methods: {
    setLocale(value) {
      this.locale = value;
      localStorage.setItem("preferred_locale", value);
    },
    async dangNhapQuanTri() {
      const phanHoi = await goiApi(`${API_BASE}/admin/login`, {
        method: "POST",
        headers: { "Content-Type": "application/json", "Accept": "application/json" },
        body: JSON.stringify({
          email: this.form.email,
          Mat_khau: this.form.password,
        }),
      });

      const data = await phanHoi.json().catch(() => ({}));
      if (!phanHoi.ok) {
        return { ok: false, message: data?.message || this.copy.messages.adminLoginFailed };
      }

      const token = data?.data?.token || "";
      if (!token) return { ok: false, message: this.copy.messages.adminTokenMissing };

      return { ok: true, token, user: data?.data?.admin || null };
    },
    saveAuthSession({ token, user }) {
      authStorage.saveSession(Role.ADMIN, { token, user });
    },
    async xuLyDangNhap() {
      this.errors = { email: "", password: "" };
      if (!this.form.email) this.errors.email = this.copy.validation.emailRequired;
      if (!this.form.password) this.errors.password = this.copy.validation.passwordRequired;
      if (this.errors.email || this.errors.password) return;

      this.dangTai = true;
      this.thongBaoLoi = "";
      try {
        const result = await this.dangNhapQuanTri();
        if (result.ok) {
          this.saveAuthSession(result);
          this.thongBaoThanhCong = this.copy.messages.loginSuccess;
          const redirectPath = String(this.$route.query.redirect || "/dashboard");
          setTimeout(() => this.$router.push(redirectPath), 800);
        } else {
          this.thongBaoLoi = result.message;
        }
      } catch (e) {
        this.thongBaoLoi = this.copy.messages.apiUnavailable;
      } finally {
        this.dangTai = false;
      }
    },
    clearMessages() { this.thongBaoLoi = ""; this.thongBaoThanhCong = ""; },
    showForgotMessage() { this.forgotDialogOpen = true; }
  },
  mounted() {
    const rememberedLocale = localStorage.getItem("preferred_locale");
    if (rememberedLocale) this.locale = rememberedLocale;
    
    // Check if already logged in as admin
    const adminToken = authStorage.getToken(Role.ADMIN);
    if (adminToken) {
      const redirectPath = String(this.$route.query.redirect || "/dashboard");
      this.$router.push(redirectPath);
    }
  }
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap");

.login-page {
  --ink: #f8fafc;
  --muted: #94a3b8;
  --panel-ink: #0f172a;
  --panel-muted: #64748b;
  --accent: #6366f1;
  --accent-deep: #4338ca;
  position: relative;
  min-height: 100vh;
  overflow: hidden;
  color: var(--ink);
  background-color: #020617;
  font-family: "Inter", sans-serif;
}

.login-page::before {
  content: "";
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 0% 0%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
    radial-gradient(circle at 100% 100%, rgba(79, 70, 229, 0.15) 0%, transparent 50%);
  z-index: 0;
}

.login-shell {
  position: relative;
  z-index: 1;
  width: min(1200px, 100%);
  margin: 0 auto;
  padding: 40px 24px;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.login-topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.brand-link {
  display: flex;
  align-items: center;
  gap: 12px;
  background: transparent;
  border: 0;
  color: inherit;
  cursor: pointer;
  padding: 0;
}

.brand-link__mark {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  border-radius: 12px;
  display: grid;
  place-items: center;
  font-size: 1.25rem;
  box-shadow: 0 8px 16px rgba(79, 70, 229, 0.4);
}

.brand-link__copy strong {
  display: block;
  font-family: "Manrope", sans-serif;
  font-size: 1.5rem;
  font-weight: 800;
  letter-spacing: -0.02em;
}

.brand-link__copy small {
  font-size: 0.8rem;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.language-switch {
  display: flex;
  background: rgba(30, 41, 59, 0.5);
  padding: 4px;
  border-radius: 99px;
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.language-switch__button {
  padding: 6px 16px;
  border: 0;
  border-radius: 99px;
  background: transparent;
  color: var(--muted);
  font-weight: 600;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.2s;
}

.language-switch__button--active {
  background: #ffffff;
  color: #0f172a;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.login-stage {
  flex: 1;
  display: grid;
  place-items: center;
}

.login-panel {
  width: 100%;
  max-width: 420px;
  background: rgba(255, 255, 255, 0.98);
  padding: 48px;
  border-radius: 24px;
  color: var(--panel-ink);
  box-shadow: 0 24px 64px rgba(0, 0, 0, 0.4);
  animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.login-panel__eyebrow {
  color: var(--accent);
  font-weight: 700;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-bottom: 8px;
}

.login-panel h2 {
  font-family: "Manrope", sans-serif;
  font-size: 2rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  margin-bottom: 12px;
}

.login-panel__summary {
  color: var(--panel-muted);
  font-size: 0.95rem;
  line-height: 1.6;
  margin-bottom: 32px;
}

.notice {
  padding: 12px 16px;
  border-radius: 12px;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 0.9rem;
  font-weight: 500;
}

.notice--error {
  background: #fef2f2;
  color: #991b1b;
  border: 1px solid #fee2e2;
}

.notice--success {
  background: #f0fdf4;
  color: #166534;
  border: 1px solid #dcfce7;
}

.login-form {
  display: grid;
  gap: 20px;
}

.field {
  display: grid;
  gap: 8px;
}

.field span {
  font-weight: 600;
  font-size: 0.9rem;
}

.field__shell {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 0 16px;
  border-radius: 12px;
  min-height: 52px;
  transition: all 0.2s;
}

.field__shell:focus-within {
  background: #ffffff;
  border-color: var(--accent);
  box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.field__shell input {
  flex: 1;
  border: 0;
  outline: 0;
  background: transparent;
  font-size: 0.95rem;
  color: inherit;
}

.field__shell i {
  color: #94a3b8;
}

.field__toggle, .field__link {
  background: transparent;
  border: 0;
  color: var(--accent);
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
}

.field__top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.field__error {
  color: #dc2626;
  font-size: 0.8rem;
  font-weight: 500;
}

.remember-box {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
  color: var(--panel-muted);
  cursor: pointer;
}

.remember-box input {
  width: 18px;
  height: 18px;
  accent-color: var(--accent);
}

.login-button {
  background: linear-gradient(135deg, var(--accent), var(--accent-deep));
  color: #ffffff;
  border: 0;
  padding: 16px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-top: 12px;
  box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
  transition: all 0.2s;
}

.login-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(79, 70, 229, 0.3);
}

.login-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.admin-footer {
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1px solid #e2e8f0;
  text-align: center;
}

.admin-footer p {
  font-size: 0.8rem;
  color: var(--panel-muted);
  font-style: italic;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 480px) {
  .login-panel { padding: 32px 24px; }
  .login-shell { padding: 24px 16px; }
}

/* Reuse existing dialog styles for forgot password */
.forgot-backdrop {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: grid;
  place-items: center;
  background: rgba(2, 6, 23, 0.8);
  backdrop-filter: blur(8px);
}

.forgot-dialog {
  position: relative;
  width: min(400px, calc(100% - 32px));
  padding: 40px;
  border-radius: 24px;
  background: #ffffff;
  text-align: center;
}

.forgot-dialog__close {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 0;
  background: #f1f5f9;
  color: #64748b;
  cursor: pointer;
}

.forgot-dialog__icon {
  width: 64px;
  height: 64px;
  background: #fef2f2;
  color: #dc2626;
  border-radius: 50%;
  display: grid;
  place-items: center;
  margin: 0 auto 20px;
  font-size: 1.5rem;
}

.forgot-dialog h2 {
  font-family: "Manrope", sans-serif;
  color: #0f172a;
  margin-bottom: 12px;
}

.forgot-dialog p {
  color: #64748b;
  font-size: 0.95rem;
  line-height: 1.5;
  margin-bottom: 24px;
}

.forgot-dialog__contact {
  display: grid;
  gap: 12px;
  margin-bottom: 32px;
}

.forgot-dialog__row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: #0f172a;
  font-weight: 600;
}

.forgot-dialog__btn {
  width: 100%;
  background: #0f172a;
  color: #ffffff;
  border: 0;
  padding: 14px;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
}
</style>
