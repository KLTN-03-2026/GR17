<template>
  <div class="register-page">
    <section class="register-shell">
      <div class="register-brand">
        <div class="brand-mark">
          <i class="fas fa-route"></i>
        </div>
        <h1>Smart Travel</h1>
        <p>Tạo tài khoản để bắt đầu lên kế hoạch cho chuyến đi của bạn</p>
      </div>

      <div class="register-card">
        <div v-if="thongBaoLoi" class="notice notice-error">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ thongBaoLoi }}</span>
        </div>

        <div v-if="thongBaoThanhCong" class="notice notice-success">
          <i class="fas fa-circle-check"></i>
          <span>{{ thongBaoThanhCong }}</span>
        </div>

        <form class="register-form" @submit.prevent="xuLyDangKy">
          <div class="form-grid">
            <div class="field field-full">
              <label for="full-name">Họ và tên</label>
              <div class="input-shell">
                <i class="fas fa-user"></i>
                <input
                  id="full-name"
                  v-model.trim="form.fullName"
                  type="text"
                  placeholder="Nguyễn Văn A"
                  @focus="clearMessages"
                >
              </div>
              <span v-if="errors.fullName" class="field-error">{{ errors.fullName }}</span>
            </div>

            <div class="field">
              <label for="email">Email</label>
              <div class="input-shell">
                <i class="fas fa-envelope"></i>
                <input
                  id="email"
                  v-model.trim="form.email"
                  type="email"
                  placeholder="example@gmail.com"
                  @focus="clearMessages"
                >
              </div>
              <span v-if="errors.email" class="field-error">{{ errors.email }}</span>
            </div>

            <div class="field">
              <label for="phone">Số điện thoại</label>
              <div class="input-shell">
                <i class="fas fa-phone"></i>
                <input
                  id="phone"
                  v-model.trim="form.phone"
                  type="tel"
                  placeholder="0123456789"
                  @focus="clearMessages"
                >
              </div>
              <span v-if="errors.phone" class="field-error">{{ errors.phone }}</span>
            </div>

            <div class="field">
              <label for="birth-date">Ngày sinh</label>
              <div class="input-shell">
                <i class="fas fa-calendar"></i>
                <input
                  id="birth-date"
                  v-model="form.birthDate"
                  type="date"
                  @focus="clearMessages"
                >
              </div>
              <span v-if="errors.birthDate" class="field-error">{{ errors.birthDate }}</span>
            </div>

            <div class="field">
              <label for="gender">Giới tính</label>
              <div class="input-shell">
                <i class="fas fa-venus-mars"></i>
                <select id="gender" v-model.number="form.gender" @focus="clearMessages">
                  <option :value="1">Nam</option>
                  <option :value="0">Nữ</option>
                </select>
              </div>
            </div>

            <div class="field">
              <div class="field-head">
                <label for="password">Mật khẩu</label>
              </div>
              <div class="input-shell">
                <i class="fas fa-lock"></i>
                <input
                  id="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Tối thiểu 6 ký tự"
                  @focus="clearMessages"
                >
                <button
                  type="button"
                  class="toggle-visibility"
                  @click="showPassword = !showPassword"
                >
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
              <span v-if="errors.password" class="field-error">{{ errors.password }}</span>
            </div>

            <div class="field">
              <div class="field-head">
                <label for="confirm-password">Xác nhận mật khẩu</label>
              </div>
              <div class="input-shell">
                <i class="fas fa-shield-halved"></i>
                <input
                  id="confirm-password"
                  v-model="form.confirmPassword"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  placeholder="Nhập lại mật khẩu"
                  @focus="clearMessages"
                >
                <button
                  type="button"
                  class="toggle-visibility"
                  @click="showConfirmPassword = !showConfirmPassword"
                >
                  <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
              <span v-if="errors.confirmPassword" class="field-error">{{ errors.confirmPassword }}</span>
            </div>
          </div>

          <label class="consent-row">
            <input v-model="form.acceptedTerms" type="checkbox">
            <span>Tôi đồng ý với điều khoản sử dụng và chính sách bảo mật</span>
          </label>
          <span v-if="errors.acceptedTerms" class="field-error terms-error">{{ errors.acceptedTerms }}</span>

          <button type="submit" class="submit-button" :disabled="dangTai">
            <span v-if="dangTai" class="spinner"></span>
            <span>{{ dangTai ? "Đang tạo tài khoản..." : "Tạo tài khoản" }}</span>
            <i v-if="!dangTai" class="fas fa-arrow-right"></i>
          </button>
        </form>

        <div class="security-badge">
          <i class="fas fa-shield-alt"></i>
          <span>Thông tin của bạn được gửi qua kết nối bảo mật</span>
        </div>

        <p class="login-redirect">
          Đã có tài khoản?
          <router-link to="/dang-nhap">Đăng nhập ngay</router-link>
        </p>
      </div>
    </section>
  </div>

</template>

<script>
import { goiApi } from '../../services/httpClient.js';
export default {
  name: "RegisterPage",
  data() {
    return {
      form: {
        fullName: "",
        email: "",
        phone: "",
        birthDate: "",
        gender: 1,
        password: "",
        confirmPassword: "",
        acceptedTerms: false,
      },
      errors: {
        fullName: "",
        email: "",
        phone: "",
        birthDate: "",
        password: "",
        confirmPassword: "",
        acceptedTerms: "",
      },
      thongBaoLoi: "",
      thongBaoThanhCong: "",
      showPassword: false,
      showConfirmPassword: false,
      dangTai: false,
    };
  },
  methods: {
    clearMessages() {
      this.thongBaoLoi = "";
      this.thongBaoThanhCong = "";
    },
    validateEmail(email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },
    validatePhone(phone) {
      return /^[0-9]{9,11}$/.test(phone);
    },
    formatBirthDate(date) {
      const [year, month, day] = date.split("-");
      return `${day}/${month}/${year}`;
    },
    generateCustomerCode() {
      const timestamp = Date.now().toString().slice(-8);
      return `KH${timestamp}`;
    },
    validateForm() {
      this.errors = {
        fullName: "",
        email: "",
        phone: "",
        birthDate: "",
        password: "",
        confirmPassword: "",
        acceptedTerms: "",
      };

      if (!this.form.fullName) {
        this.errors.fullName = "Vui lòng nhập họ và tên.";
      }

      if (!this.form.email) {
        this.errors.email = "Vui lòng nhập email.";
      } else if (!this.validateEmail(this.form.email)) {
        this.errors.email = "Email không đúng định dạng.";
      }

      if (!this.form.phone) {
        this.errors.phone = "Vui lòng nhập số điện thoại.";
      } else if (!this.validatePhone(this.form.phone)) {
        this.errors.phone = "Số điện thoại phải từ 9 đến 11 chữ số.";
      }

      if (!this.form.birthDate) {
        this.errors.birthDate = "Vui lòng chọn ngày sinh.";
      }

      if (!this.form.password) {
        this.errors.password = "Vui lòng nhập mật khẩu.";
      } else if (this.form.password.length < 6) {
        this.errors.password = "Mật khẩu cần ít nhất 6 ký tự.";
      }

      if (!this.form.confirmPassword) {
        this.errors.confirmPassword = "Vui lòng xác nhận mật khẩu.";
      } else if (this.form.confirmPassword !== this.form.password) {
        this.errors.confirmPassword = "Mật khẩu xác nhận không khớp.";
      }

      if (!this.form.acceptedTerms) {
        this.errors.acceptedTerms = "Bạn cần đồng ý điều khoản để tiếp tục.";
      }

      return Object.values(this.errors).every((error) => error === "");
    },
    async xuLyDangKy() {
      if (!this.validateForm()) {
        return;
      }

      this.dangTai = true;
      this.clearMessages();

      try {
        const phanHoi = await goiApi("/api/khach-hang/register", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            Ma_khach_hang: this.generateCustomerCode(),
            Ho_va_ten: this.form.fullName,
            Mat_khau: this.form.password,
            Email: this.form.email,
            Ngay_sinh: this.formatBirthDate(this.form.birthDate),
            Gioi_tinh: this.form.gender,
            so_dien_thoai: this.form.phone,
          }),
        });

        const data = await phanHoi.json();

        if (!phanHoi.ok) {
          if (data.errors && typeof data.errors === "object") {
            Object.entries(data.errors).forEach(([key, value]) => {
              const message = Array.isArray(value) ? value[0] : value;

              if (key === "Ho_va_ten") this.errors.fullName = message;
              if (key === "Email") this.errors.email = message;
              if (key === "Mat_khau") this.errors.password = message;
              if (key === "Ngay_sinh") this.errors.birthDate = message;
              if (key === "Gioi_tinh") this.errors.gender = message;
              if (key === "so_dien_thoai") this.errors.phone = message;
            });
          }

          this.thongBaoLoi = data.message || "Đăng ký thất bại. Vui lòng kiểm tra lại thông tin.";
          return;
        }

        this.thongBaoThanhCong = "Tạo tài khoản thành công. Đang chuyển sang trang đăng nhập...";

        this.form = {
          fullName: "",
          email: "",
          phone: "",
          birthDate: "",
          gender: 1,
          password: "",
          confirmPassword: "",
          acceptedTerms: false,
        };

        setTimeout(() => {
          this.$router.push("/dang-nhap");
        }, 1400);
      } catch (error) {
        console.error("Lỗi đăng ký:", error);
        this.thongBaoLoi = "Không thể kết nối tới API đăng ký. Vui lòng kiểm tra backend.";
      } finally {
        this.dangTai = false;
      }
    },
  },
};
</script>

<style scoped>
.register-page {
  width: 100%;
  display: flex;
  justify-content: center;
}

.register-shell {
  width: min(100%, 780px);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2rem;
}

.register-brand {
  text-align: center;
  color: #0f172a;
}

.brand-mark {
  width: 80px;
  height: 80px;
  margin: 0 auto 1.25rem;
  display: grid;
  place-items: center;
  border-radius: 22px;
  background: linear-gradient(135deg, #4f46e5 0%, #5b21b6 100%);
  color: #fff;
  font-size: 2rem;
  box-shadow: 0 18px 35px rgba(79, 70, 229, 0.28);
}

.register-brand h1 {
  margin: 0;
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 800;
}

.register-brand p {
  margin: 0.6rem 0 0;
  font-size: 1.05rem;
  color: #475569;
}

.register-card {
  width: 100%;
  padding: 2rem;
  border-radius: 28px;
  background: rgba(255, 255, 255, 0.96);
  box-shadow: 0 28px 65px rgba(15, 23, 42, 0.12);
  border: 1px solid rgba(226, 232, 240, 0.9);
}

.notice {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.95rem 1rem;
  border-radius: 16px;
  margin-bottom: 1rem;
  font-size: 0.95rem;
}

.notice-error {
  color: #b91c1c;
  background: #fef2f2;
  border: 1px solid #fecaca;
}

.notice-success {
  color: #047857;
  background: #ecfdf5;
  border: 1px solid #a7f3d0;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.2rem;
}

.field {
  display: flex;
  flex-direction: column;
}

.field-full {
  grid-column: 1 / -1;
}

.field label {
  margin-bottom: 0.55rem;
  font-weight: 700;
  color: #111827;
}

.field-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.input-shell {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  min-height: 58px;
  padding: 0 1rem;
  border-radius: 16px;
  border: 1px solid #dbe4f0;
  background: #f8fafc;
  transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}

.input-shell:focus-within {
  border-color: #4f46e5;
  background: #fff;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
}

.input-shell i {
  color: #94a3b8;
  font-size: 1rem;
}

.input-shell input,
.input-shell select {
  width: 100%;
  border: 0;
  outline: 0;
  background: transparent;
  color: #0f172a;
  font-size: 1rem;
}

.toggle-visibility {
  border: 0;
  background: transparent;
  color: #94a3b8;
  cursor: pointer;
}

.field-error {
  margin-top: 0.45rem;
  font-size: 0.85rem;
  color: #dc2626;
}

.consent-row {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  margin: 1.4rem 0 0;
  color: #334155;
  font-size: 0.95rem;
}

.consent-row input {
  margin-top: 0.15rem;
  width: 18px;
  height: 18px;
  accent-color: #4f46e5;
}

.terms-error {
  display: block;
  margin-top: 0.35rem;
}

.submit-button {
  width: 100%;
  margin-top: 1.5rem;
  min-height: 60px;
  border: 0;
  border-radius: 18px;
  background: linear-gradient(90deg, #4f46e5 0%, #5b21b6 100%);
  color: #fff;
  font-size: 1rem;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.7rem;
  cursor: pointer;
  box-shadow: 0 16px 30px rgba(79, 70, 229, 0.28);
}

.submit-button:disabled {
  opacity: 0.75;
  cursor: not-allowed;
}

.spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255, 255, 255, 0.35);
  border-top-color: #fff;
  border-radius: 999px;
  animation: spin 0.8s linear infinite;
}

.security-badge {
  margin-top: 1.5rem;
  padding: 0.9rem 1rem;
  border-radius: 999px;
  background: #ecfdf5;
  border: 1px solid #bbf7d0;
  color: #047857;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  font-weight: 700;
}

.login-redirect {
  margin: 1.4rem 0 0;
  text-align: center;
  color: #475569;
}

.login-redirect a {
  color: #4338ca;
  font-weight: 700;
  text-decoration: none;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 768px) {
  .register-card {
    padding: 1.4rem;
    border-radius: 22px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .register-shell {
    gap: 1.5rem;
  }
}
</style>






