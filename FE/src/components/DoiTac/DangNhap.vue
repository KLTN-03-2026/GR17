<template>
  <div class="partner-login">
    <header class="partner-login__top">
      <button class="brand" type="button" @click="$router.push('/')">
        <span class="brand__icon"><i class="fas fa-handshake"></i></span>
        <span class="brand__text">
          <strong>Smart Travel</strong>
          <small>CỘNG ĐỒNG ĐỐI TÁC</small>
        </span>
      </button>
    </header>

    <main class="partner-login__stage">
      <section class="panel">
        <p class="panel__eyebrow">CỔNG ĐĂNG NHẬP ĐỐI TÁC</p>
        <h1>Đăng nhập tài khoản đối tác</h1>
        <p class="panel__desc">
          Đăng nhập để quản lý tour, địa điểm và hành trình của doanh nghiệp bạn trên Smart Travel.
        </p>

        <div v-if="messageError" class="notice notice--error">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ messageError }}</span>
        </div>
        <div v-if="messageSuccess" class="notice notice--success">
          <i class="fas fa-circle-check"></i>
          <span>{{ messageSuccess }}</span>
        </div>

        <form class="form" @submit.prevent="handleLogin">
          <label class="field">
            <span>Email</span>
            <div class="field__shell">
              <i class="fas fa-envelope"></i>
              <input
                v-model.trim="form.email"
                type="email"
                autocomplete="username"
                placeholder="partner@example.com"
                @focus="clearMessages"
              >
            </div>
            <small v-if="errors.email" class="field__error">{{ errors.email }}</small>
          </label>

          <label class="field">
            <span>Mật khẩu</span>
            <div class="field__shell">
              <i class="fas fa-lock"></i>
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password"
                placeholder="Nhập mật khẩu"
                @focus="clearMessages"
              >
              <button class="field__toggle" type="button" @click="showPassword = !showPassword">
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
            <small v-if="errors.password" class="field__error">{{ errors.password }}</small>
          </label>

          <button class="submit" type="submit" :disabled="loading">
            <i v-if="loading" class="fas fa-spinner fa-spin"></i>
            <span>{{ loading ? "Đang đăng nhập..." : "Vào trang quản lý" }}</span>
            <i v-if="!loading" class="fas fa-arrow-right"></i>
          </button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center; color: #60748f; font-weight: 500;">
          Chưa có tài khoản?
          <button type="button" @click="$router.push('/doi-tac/dang-ky')" style="color: #2563eb; font-weight: 700; border: none; background: transparent; cursor: pointer; padding: 0; font-size: inherit;">
            Đăng ký trở thành đối tác
          </button>
        </div>
      </section>
    </main>
  </div>
</template>

<script>
import { goiApi } from "../../services/httpClient";

export default {
  name: "PartnerDangNhap",
  data() {
    return {
      loading: false,
      showPassword: false,
      messageError: "",
      messageSuccess: "",
      form: {
        email: "",
        password: "",
      },
      errors: {
        email: "",
        password: "",
      },
    };
  },
  methods: {
    clearMessages() {
      this.messageError = "";
      this.messageSuccess = "";
    },
    validateEmail(email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },
    validateForm() {
      this.errors.email = "";
      this.errors.password = "";

      if (!this.form.email) {
        this.errors.email = "Email là bắt buộc.";
      } else if (!this.validateEmail(this.form.email)) {
        this.errors.email = "Email không đúng định dạng.";
      }

      if (!this.form.password) {
        this.errors.password = "Mật khẩu là bắt buộc.";
      } else if (this.form.password.length < 6) {
        this.errors.password = "Mật khẩu tối thiểu 6 ký tự.";
      }

      return !this.errors.email && !this.errors.password;
    },
    savePartnerSession(token, doiTac) {
      localStorage.setItem("token", token);
      localStorage.setItem("auth_type", "partner");
      localStorage.setItem("user", JSON.stringify(doiTac || {}));
      window.dispatchEvent(new Event("storage"));
    },
    async handleLogin() {
      if (!this.validateForm()) return;

      this.loading = true;
      this.clearMessages();

      try {
        const response = await goiApi("/api/doi-tac/login", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: JSON.stringify({
            email: this.form.email,
            mat_khau: this.form.password,
          }),
        });

        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
          this.messageError = payload?.message || "Đăng nhập thất bại.";
          return;
        }

        const token = String(payload?.data?.token || "").trim();
        if (!token) {
          this.messageError = "Đăng nhập thất bại.";
          return;
        }

        this.savePartnerSession(token, payload?.data?.doi_tac || null);
        this.messageSuccess = "Đăng nhập thành công, đang chuyển hướng...";

        setTimeout(() => {
          this.$router.push("/doi-tac/dashboard");
        }, 500);
      } catch (error) {
        console.error("Partner login error:", error);
        this.messageError = "Không thể kết nối đến API.";
      } finally {
        this.loading = false;
      }
    },
    redirectIfLoggedIn() {
      const token = localStorage.getItem("token");
      const authType = (localStorage.getItem("auth_type") || "").toLowerCase();
      if (token && authType === "partner") {
        this.$router.replace("/doi-tac/dashboard");
      }
    },
  },
  mounted() {
    this.redirectIfLoggedIn();
  },
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap");

.partner-login {
  color: #eff6ff;
  font-family: "Inter", sans-serif;
}

.partner-login__top {
  width: min(1220px, 100%);
  margin: 0 auto;
  padding: 24px clamp(16px, 4vw, 42px);
}

.brand {
  border: 0;
  padding: 0;
  color: inherit;
  background: transparent;
  display: inline-flex;
  align-items: center;
  gap: 0.9rem;
  text-align: left;
}

.brand__icon {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  display: grid;
  place-items: center;
  color: #fff;
  background: linear-gradient(135deg, #2563eb, #0284c7);
  box-shadow: 0 14px 28px rgba(2, 132, 199, 0.3);
}

.brand__text strong {
  display: block;
  font-family: "Manrope", sans-serif;
  font-size: 1.6rem;
  letter-spacing: -0.03em;
}

.brand__text small {
  color: rgba(214, 226, 255, 0.82);
  font-size: 0.8rem;
  letter-spacing: 0.18em;
}

.partner-login__stage {
  min-height: calc(100vh - 100px);
  display: grid;
  place-items: center;
  padding: 16px;
}

.panel {
  width: min(100%, 440px);
  border-radius: 30px;
  padding: 30px;
  color: #112642;
  background: rgba(248, 251, 255, 0.94);
  border: 1px solid rgba(255, 255, 255, 0.65);
  box-shadow: 0 24px 58px rgba(4, 16, 35, 0.34);
}

.panel__eyebrow {
  margin: 0 0 8px;
  color: #1d4ed8;
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.19em;
}

.panel h1 {
  margin: 0;
  font-family: "Manrope", sans-serif;
  font-size: 2rem;
  line-height: 1.08;
}

.panel__desc {
  margin: 10px 0 0;
  color: #60748f;
  line-height: 1.62;
}

.notice {
  margin-top: 12px;
  border-radius: 14px;
  padding: 10px 12px;
  display: flex;
  align-items: flex-start;
  gap: 0.55rem;
  font-size: 0.92rem;
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

.form {
  margin-top: 16px;
  display: grid;
  gap: 0.95rem;
}

.field {
  display: grid;
  gap: 0.5rem;
}

.field span {
  font-weight: 700;
}

.field__shell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  min-height: 54px;
  padding: 0 12px;
  border-radius: 14px;
  background: #f8fbff;
  border: 1px solid #d6e1ee;
}

.field__shell:focus-within {
  border-color: #2563eb;
  box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.14);
}

.field__shell input {
  flex: 1;
  border: 0;
  outline: 0;
  background: transparent;
  color: #112642;
  font-size: 1rem;
}

.field__shell i,
.field__toggle {
  color: #8ca3bc;
}

.field__toggle {
  border: 0;
  padding: 0;
  background: transparent;
}

.field__error {
  color: #b42318;
  font-size: 0.85rem;
}

.submit {
  margin-top: 4px;
  min-height: 56px;
  border: 0;
  border-radius: 16px;
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  color: #fff;
  font-weight: 800;
  font-size: 1rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.65rem;
  box-shadow: 0 16px 32px rgba(29, 78, 216, 0.32);
}

.submit:disabled {
  opacity: 0.76;
  cursor: not-allowed;
}
</style>
