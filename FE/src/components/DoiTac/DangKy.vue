<template>
  <div class="partner-register">
    <header class="partner-register__top">
      <button class="brand" type="button" @click="$router.push('/')">
        <span class="brand__icon"><i class="fas fa-handshake"></i></span>
        <span class="brand__text">
          <strong>Smart Travel</strong>
          <small>CỘNG ĐỒNG ĐỐI TÁC</small>
        </span>
      </button>
    </header>

    <main class="partner-register__stage">
      <section class="panel">
        <p class="panel__eyebrow">ĐĂNG KÝ HỢP TÁC</p>
        <h1>Trở thành đối tác</h1>
        <p class="panel__desc">
          Mở rộng phạm vi kinh doanh của bạn bằng cách tham gia nền tảng Smart Travel. Điền các thông tin cơ bản dưới đây để Admin xét duyệt.
        </p>

        <div v-if="messageError" class="notice notice--error">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ messageError }}</span>
        </div>
        <div v-if="messageSuccess" class="notice notice--success">
          <i class="fas fa-circle-check"></i>
          <span>{{ messageSuccess }}</span>
        </div>

        <form class="form" @submit.prevent="handleRegister" v-if="!registerSuccess">
          <label class="field">
            <span>Tên Công ty / Vận hành</span>
            <div class="field__shell">
              <i class="fas fa-building"></i>
              <input
                v-model.trim="form.ten_doi_tac"
                type="text"
                placeholder="Ví dụ: Công ty TNHH Smart Travel"
                @focus="clearMessages"
                required
              >
            </div>
          </label>

          <label class="field">
            <span>Người đại diện</span>
            <div class="field__shell">
              <i class="fas fa-user-tie"></i>
              <input
                v-model.trim="form.ten_nguoi_dai_dien"
                type="text"
                placeholder="Nhập họ và tên"
                @focus="clearMessages"
                required
              >
            </div>
          </label>

          <div class="form-row">
            <label class="field">
              <span>Email liên hệ</span>
              <div class="field__shell">
                <i class="fas fa-envelope"></i>
                <input
                  v-model.trim="form.email"
                  type="email"
                  placeholder="contact@company.com"
                  @focus="clearMessages"
                  required
                >
              </div>
            </label>

            <label class="field">
              <span>Số điện thoại</span>
              <div class="field__shell">
                <i class="fas fa-phone"></i>
                <input
                  v-model.trim="form.so_dien_thoai"
                  type="text"
                  placeholder="09xx xxx xxx"
                  @focus="clearMessages"
                  required
                >
              </div>
            </label>
          </div>

          <label class="field">
            <span>Địa chỉ trụ sở</span>
            <div class="field__shell">
              <i class="fas fa-map-marker-alt"></i>
              <input
                v-model.trim="form.dia_chi"
                type="text"
                placeholder="Nhập đỉa chỉ đăng ký kinh doanh"
                @focus="clearMessages"
                required
              >
            </div>
          </label>

          <label class="field">
            <span>Mật khẩu tài khoản</span>
            <div class="field__shell">
              <i class="fas fa-lock"></i>
              <input
                v-model="form.mat_khau"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Tối thiểu 6 ký tự"
                @focus="clearMessages"
                required
                minlength="6"
              >
              <button class="field__toggle" type="button" @click="showPassword = !showPassword">
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
          </label>

          <button class="submit" type="submit" :disabled="loading">
            <i v-if="loading" class="fas fa-spinner fa-spin"></i>
            <span>{{ loading ? "Đang gửi yêu cầu..." : "Gửi thông tin đăng ký" }}</span>
            <i v-if="!loading" class="fas fa-arrow-right"></i>
          </button>
        </form>

        <div class="success-block" v-if="registerSuccess">
          <div class="success-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <h2>Đăng ký thành công!</h2>
          <p>Yêu cầu làm đối tác của bạn đã được gửi. Quản trị viên sẽ sớm liên hệ và phê duyệt tài khoản.</p>
          <button class="submit" type="button" @click="$router.push('/doi-tac/dang-nhap')" style="width: 100%; margin-top: 20px;">
            Đăng nhập ngay <i class="fas fa-arrow-right"></i>
          </button>
        </div>

        <div class="form-footer" v-if="!registerSuccess">
          Đã có tài khoản?
          <router-link to="/doi-tac/dang-nhap" class="text-link">Đăng nhập tại đây</router-link>
        </div>
      </section>
    </main>
  </div>
</template>

<script>
import { goiApi } from "../../services/httpClient";

export default {
  name: "PartnerDangKy",
  data() {
    return {
      loading: false,
      showPassword: false,
      registerSuccess: false,
      messageError: "",
      messageSuccess: "",
      form: {
        ten_doi_tac: "",
        ten_nguoi_dai_dien: "",
        email: "",
        so_dien_thoai: "",
        dia_chi: "",
        mat_khau: "",
      },
    };
  },
  methods: {
    clearMessages() {
      this.messageError = "";
      this.messageSuccess = "";
    },
    async handleRegister() {
      this.loading = true;
      this.clearMessages();

      try {
        const response = await goiApi("/api/doi-tac/register", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: JSON.stringify(this.form),
        });

        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
          let errorText = payload?.message || "Đăng ký thất bại. Vui lòng thử lại.";
          // Handle Laravel validation errors specifically
          if (payload?.errors) {
            const firstErrorKey = Object.keys(payload.errors)[0];
            if (firstErrorKey && payload.errors[firstErrorKey].length > 0) {
              errorText = payload.errors[firstErrorKey][0];
            }
          }
          this.messageError = errorText;
          return;
        }

        this.registerSuccess = true;
        
        // Remove setTimeout to let user read the message and click the button manually
      } catch (error) {
        console.error("Partner register error:", error);
        this.messageError = "Không thể kết nối đến máy chủ. Vui lòng thử lại.";
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap");

.partner-register {
  color: #eff6ff;
  font-family: "Inter", sans-serif;
  display: flex;
  flex-direction: column;
}

.partner-register__top {
  width: min(1220px, 100%);
  margin: 0 auto;
  padding: 24px clamp(16px, 4vw, 42px);
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
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
  cursor: pointer;
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
  font-size: 1.3rem;
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

.partner-register__stage {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 100px 16px 40px;
}

.panel {
  width: min(100%, 640px);
  border-radius: 20px;
  padding: 40px 48px;
  color: #112642;
  background: rgba(255, 255, 255, 0.98);
  border: 1px solid rgba(255, 255, 255, 0.5);
  box-shadow: 0 24px 64px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(12px);
}

.panel__eyebrow {
  margin: 0 0 8px;
  color: #1d4ed8;
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.19em;
  text-transform: uppercase;
}

.panel h1 {
  margin: 0;
  font-family: "Manrope", sans-serif;
  font-size: 2.2rem;
  line-height: 1.1;
  letter-spacing: -0.02em;
  color: #0f172a;
}

.panel__desc {
  margin: 12px 0 0;
  color: #64748b;
  line-height: 1.6;
  font-size: 0.95rem;
}

.notice {
  margin-top: 20px;
  border-radius: 10px;
  padding: 12px 14px;
  display: flex;
  align-items: flex-start;
  gap: 0.7rem;
  font-size: 0.9rem;
  font-weight: 600;
}

.notice--error {
  color: #b42318;
  background: #fef2f2;
}

.notice--success {
  color: #027a48;
  background: #f0fdf4;
}

.form {
  margin-top: 30px;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-row {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
  gap: 1.25rem;
}
@media(max-width: 600px) {
  .panel { padding: 30px 24px; }
  .form-row { grid-template-columns: minmax(0, 1fr); }
}

.field {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.field span {
  font-weight: 700;
  font-size: 0.85rem;
  color: #334155;
}

.field__shell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  height: 48px;
  padding: 0 14px;
  border-radius: 10px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  transition: all 0.2s ease;
  overflow: hidden;
}

.field__shell:focus-within {
  background: #fff;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.field__shell input {
  flex: 1;
  min-width: 0;
  border: 0;
  outline: 0;
  background: transparent;
  color: #0f172a;
  font-size: 0.95rem;
  padding: 0;
}

.field__shell input::placeholder {
  color: #94a3b8;
}

/* Fix CSS auto-fill background overlap in Chrome/Edge */
.field__shell input:-webkit-autofill,
.field__shell input:-webkit-autofill:hover, 
.field__shell input:-webkit-autofill:focus, 
.field__shell input:-webkit-autofill:active {
  -webkit-box-shadow: 0 0 0 30px #f8fafc inset !important;
  -webkit-text-fill-color: #0f172a !important;
}

.field__shell:focus-within input:-webkit-autofill {
  -webkit-box-shadow: 0 0 0 30px #ffffff inset !important;
}

.field__shell i,
.field__toggle {
  color: #94a3b8;
  font-size: 1.1rem;
}

.field__toggle {
  border: 0;
  padding: 0;
  background: transparent;
  cursor: pointer;
}

.submit {
  margin-top: 15px;
  height: 52px;
  border: 0;
  border-radius: 10px;
  background: #2563eb;
  color: #fff;
  font-weight: 700;
  font-size: 1rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.65rem;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
  cursor: pointer;
  transition: all 0.2s ease;
}

.submit:hover:not(:disabled) {
  background: #1d4ed8;
  box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
}

.submit:disabled {
  background: #94a3b8;
  box-shadow: none;
  cursor: not-allowed;
}

.form-footer {
  margin-top: 1.5rem;
  text-align: center;
  color: #64748b;
  font-weight: 500;
  font-size: 0.9rem;
}

.text-link {
  color: #2563eb;
  font-weight: 700;
  text-decoration: none;
  margin-left: 0.25rem;
  transition: color 0.2s;
}
.text-link:hover {
  text-decoration: underline;
  color: #1d4ed8;
}

.success-block {
  text-align: center;
  padding: 40px 20px;
}

.success-icon {
  font-size: 4rem;
  color: #10b981;
  margin-bottom: 20px;
  animation: scale-up 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
}

.success-block h2 {
  font-family: "Manrope", sans-serif;
  color: #0f172a;
  font-size: 1.8rem;
  margin-bottom: 10px;
}

.success-block p {
  color: #64748b;
  line-height: 1.6;
  font-size: 1rem;
}

@keyframes scale-up {
  0% {
    transform: scale(0.5);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}
</style>
