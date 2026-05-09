<template>
  <div class="plan-page">
    <div class="plan-layout">
      <CustomerSidebar />

      <section class="plan-content">
        <div class="customer-profile-page">
          <section class="hero">
            <div>
              <p class="eyebrow">Tài khoản cá nhân</p>
              <h1>Hồ sơ khách hàng</h1>
              <p class="subtitle">
                Cập nhật thông tin của bạn và đồng bộ ngay trên giao diện tài khoản.
              </p>
            </div>
            <button class="btn btn-ghost" type="button" @click="taiHoSo" :disabled="dangTaiHoSo">
              <i class="fas fa-rotate-right" :class="{ 'fa-spin': dangTaiHoSo }"></i>
              <span>{{ dangTaiHoSo ? "Đang tải..." : "Làm mới hồ sơ" }}</span>
            </button>
          </section>

          <section class="profile-layout">
            <aside class="profile-summary card">
              <div class="identity-card">
                <div class="identity-avatar">{{ initials }}</div>
                <div>
                  <h2>{{ profile.Ho_va_ten || "Khách hàng" }}</h2>
                  <p>{{ profile.Email || "Chưa có email" }}</p>
                </div>
              </div>

              <div class="summary-list">
                <div>
                  <span>Mã khách hàng</span>
                  <strong>{{ customerIdLabel }}</strong>
                </div>
                <div>
                  <span>Trạng thái</span>
                  <strong :class="isBlocked ? 'danger-text' : 'success-text'">
                    {{ isBlocked ? "Đang khóa" : "Hoạt động" }}
                  </strong>
                </div>
                <div>
                  <span>Số điện thoại</span>
                  <strong>{{ profile.so_dien_thoai || "Chưa cập nhật" }}</strong>
                </div>
                <div>
                  <span>Ngày cập nhật</span>
                  <strong>{{ updatedDateLabel }}</strong>
                </div>
              </div>
            </aside>

            <div class="profile-main-stack">
              <section class="profile-main card">
                <header class="card-head">
                  <div>
                    <p class="eyebrow">Thông tin của tôi</p>
                    <h2>Cập nhật hồ sơ</h2>
                  </div>
                </header>

                <div class="form-grid form-grid-2">
                  <label class="field">
                    <span>Họ và tên</span>
                    <input v-model.trim="form.Ho_va_ten" type="text" placeholder="Nhập họ và tên">
                  </label>
                  <label class="field">
                    <span>Email</span>
                    <input v-model.trim="form.Email" type="email" placeholder="name@example.com">
                  </label>
                  <label class="field">
                    <span>Số điện thoại</span>
                    <input v-model.trim="form.so_dien_thoai" type="text" inputmode="numeric" maxlength="10"
                      placeholder="0xxxxxxxxx">
                  </label>
                  <label class="field">
                    <span>Ngày sinh</span>
                    <input v-model="form.Ngay_sinh" type="date">
                  </label>
                  <label class="field">
                    <span>Giới tính</span>
                    <select v-model.number="form.Gioi_tinh">
                      <option :value="1">Nam</option>
                      <option :value="0">Nữ</option>
                    </select>
                  </label>
                  <!-- <label class="field">
                    <span>Mã khách hàng</span>
                    <input :value="customerIdLabel" type="text" readonly>
                  </label> -->
                </div>

                <div class="actions-row">
                  <button class="btn btn-primary" type="button" @click="saveProfile"
                    :disabled="isSavingProfile || !canSaveProfile">
                    <i v-if="isSavingProfile" class="fas fa-spinner fa-spin"></i>
                    <span>{{ isSavingProfile ? "Đang lưu..." : "Lưu thông tin" }}</span>
                  </button>
                </div>
              </section>

              <section class="password-card card">
                <header class="card-head">
                  <div>
                    <p class="eyebrow">Bảo mật tài khoản</p>
                    <h2>Đổi mật khẩu</h2>
                  </div>
                </header>

                <div class="form-grid form-grid-3">
                  <label class="field">
                    <span>Mật khẩu hiện tại</span>
                    <input v-model="passwordForm.current_password" type="password" autocomplete="current-password"
                      placeholder="Nhập mật khẩu hiện tại">
                  </label>
                  <label class="field">
                    <span>Mật khẩu mới</span>
                    <input v-model="passwordForm.new_password" type="password" autocomplete="new-password"
                      placeholder="Tối thiểu 8 ký tự">
                  </label>
                  <label class="field">
                    <span>Xác nhận mật khẩu mới</span>
                    <input v-model="passwordForm.confirm_password" type="password" autocomplete="new-password"
                      placeholder="Nhập lại mật khẩu mới">
                  </label>
                </div>

                <p class="password-note">Mật khẩu mới phải có tối thiểu 8 ký tự.</p>

                <div class="actions-row">
                  <button class="btn btn-primary" type="button" @click="savePassword"
                    :disabled="isSavingPassword || !canSavePassword">
                    <i v-if="isSavingPassword" class="fas fa-spinner fa-spin"></i>
                    <span>{{ isSavingPassword ? "Đang cập nhật..." : "Cập nhật mật khẩu" }}</span>
                  </button>
                </div>
              </section>
            </div>
          </section>
        </div>
      </section>
    </div>
  </div>

</template>

<script>
import CustomerSidebar from './CustomerSidebar.vue';
import { goiApi } from '../../services/httpClient.js';

import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({ position: "top-right" });
import { API_BASE, buildHeaders, getStoredCustomerId, getStoredUser } from "../Shared/customerSession";

function normalizeRecord(duLieuPhanHoi) {
  return duLieuPhanHoi?.data?.data || duLieuPhanHoi?.data || duLieuPhanHoi || null;
}

function parseDateValue(value) {
  if (!value) return null;
  const raw = String(value).trim();
  if (!raw) return null;

  if (/^\d{2}\/\d{2}\/\d{4}$/.test(raw)) {
    const [day, month, year] = raw.split("/");
    return new Date(`${year}-${month}-${day}`);
  }

  const date = new Date(raw);
  return Number.isNaN(date.getTime()) ? null : date;
}

function formatDateForInput(value) {
  const date = parseDateValue(value);
  if (!date) return "";
  const year = date.getFullYear();
  const month = `${date.getMonth() + 1}`.padStart(2, "0");
  const day = `${date.getDate()}`.padStart(2, "0");
  return `${year}-${month}-${day}`;
}

function formatDateForApi(value) {
  if (!value) return "";
  const [year, month, day] = String(value).split("-");
  if (!year || !month || !day) return "";
  return `${day}/${month}/${year}`;
}

function formatDateDisplay(value) {
  const date = parseDateValue(value);
  if (!date) return "--";
  return date.toLocaleDateString("vi-VN", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
}
export default {
  name: "HoSoCaNhan",
  components: {
    CustomerSidebar,
  },
  data() {
    return {
      profile: {},
      form: {
        Ho_va_ten: "",
        Email: "",
        Ngay_sinh: "",
        Gioi_tinh: 1,
        so_dien_thoai: "",
      },
      passwordForm: {
        current_password: "",
        new_password: "",
        confirm_password: "",
      },
      dangTaiHoSo: false,
      isSavingProfile: false,
      isSavingPassword: false,
    };
  },
  computed: {
    maKhachHang() {
      return String(getStoredCustomerId() || "");
    },
    hoSoKhachHang() {
      return getStoredUser() || {};
    },
    customerIdLabel() {
      return this.maKhachHang ? `KH ${this.maKhachHang}` : "Chưa xác định";
    },
    initials() {
      const name = String(this.profile.Ho_va_ten || "KH").trim();
      return (
        name
          .split(/\s+/)
          .filter(Boolean)
          .slice(0, 2)
          .map((part) => part.charAt(0).toUpperCase())
          .join("") || "KH"
      );
    },
    isBlocked() {
      return Number(this.profile?.is_block ?? 0) === 1;
    },
    updatedDateLabel() {
      return formatDateDisplay(this.profile?.updated_at);
    },
    canSaveProfile() {
      return Boolean(this.form.Ho_va_ten && this.form.Email);
    },
    canSavePassword() {
      return Boolean(
        this.passwordForm.current_password &&
        this.passwordForm.new_password &&
        this.passwordForm.confirm_password &&
        this.passwordForm.new_password.length >= 8 &&
        this.passwordForm.new_password === this.passwordForm.confirm_password,
      );
    },
  },
  methods: {
    syncFormFromProfile() {
      this.form = {
        Ho_va_ten: this.profile?.Ho_va_ten || "",
        Email: this.profile?.Email || "",
        Ngay_sinh: formatDateForInput(this.profile?.Ngay_sinh),
        Gioi_tinh: Number(this.profile?.Gioi_tinh ?? 1),
        so_dien_thoai: this.profile?.so_dien_thoai || "",
      };
    },
    buildPayload() {
      const duLieuPhanHoi = {
        Ho_va_ten: this.form.Ho_va_ten,
        Email: this.form.Email,
        Gioi_tinh: Number(this.form.Gioi_tinh) === 1,
      };

      if (this.form.so_dien_thoai) {
        duLieuPhanHoi.so_dien_thoai = this.form.so_dien_thoai;
      }

      if (this.form.Ngay_sinh) {
        duLieuPhanHoi.Ngay_sinh = formatDateForApi(this.form.Ngay_sinh);
      }

      return duLieuPhanHoi;
    },
    syncUserStorage() {
      const currentUser = getStoredUser() || {};
      const nextUser = {
        ...currentUser,
        ...this.profile,
        Ho_va_ten: this.form.Ho_va_ten,
        Email: this.form.Email,
        so_dien_thoai: this.form.so_dien_thoai || this.profile.so_dien_thoai || "",
        Ngay_sinh: this.profile.Ngay_sinh || this.form.Ngay_sinh || "",
        Gioi_tinh: Number(this.form.Gioi_tinh) === 1,
      };

      localStorage.setItem("user", JSON.stringify(nextUser));
      window.dispatchEvent(new Event("storage"));
    },
    resetPasswordForm() {
      this.passwordForm = {
        current_password: "",
        new_password: "",
        confirm_password: "",
      };
    },
    async taiHoSo() {
      if (!this.maKhachHang) {
        toaster.error("Không xác định được mã khách hàng trong phiên đăng nhập hiện tại.");
        return;
      }

      this.dangTaiHoSo = true;
      try {
        const phanHoi = await goiApi(`${API_BASE}/khach-hang/profile/${encodeURIComponent(this.maKhachHang)}`, {
          headers: buildHeaders(),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể tải hồ sơ cá nhân.");
        }

        this.profile = normalizeRecord(duLieuPhanHoi) || {};
        this.syncFormFromProfile();
        this.syncUserStorage();
      } catch (error) {
        toaster.error(error?.message || "Không thể tải dữ liệu tài khoản.");
      } finally {
        this.dangTaiHoSo = false;
      }
    },
    async saveProfile() {
      if (!this.maKhachHang) return;
      this.isSavingProfile = true;

      try {
        const phanHoi = await goiApi(`${API_BASE}/khach-hang/profile/${encodeURIComponent(this.maKhachHang)}`, {
          method: "PUT",
          headers: buildHeaders(true),
          body: JSON.stringify(this.buildPayload()),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể cập nhật hồ sơ.");
        }

        this.profile = normalizeRecord(duLieuPhanHoi) || this.profile;
        this.syncFormFromProfile();
        this.syncUserStorage();
        toaster.success(duLieuPhanHoi?.message || "Thông tin cá nhân đã được lưu.");
      } catch (error) {
        toaster.error(error?.message || "Không thể lưu thông tin.");
      } finally {
        this.isSavingProfile = false;
      }
    },
    async savePassword() {
      if (!this.maKhachHang) return;

      if (this.passwordForm.new_password !== this.passwordForm.confirm_password) {
        toaster.error("Vui lòng nhập lại đúng mật khẩu mới để tiếp tục.");
        return;
      }

      if (this.passwordForm.new_password.length < 8) {
        toaster.error("Mật khẩu mới phải có tối thiểu 8 ký tự.");
        return;
      }

      this.isSavingPassword = true;
      try {
        const phanHoi = await goiApi(`${API_BASE}/khach-hang/change-password/${encodeURIComponent(this.maKhachHang)}`, {
          method: "PUT",
          headers: buildHeaders(true),
          body: JSON.stringify({
            current_password: this.passwordForm.current_password,
            new_password: this.passwordForm.new_password,
          }),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể đổi mật khẩu.");
        }

        this.resetPasswordForm();
        toaster.success(duLieuPhanHoi?.message || "Mật khẩu của bạn đã được cập nhật.");
      } catch (error) {
        toaster.error(error?.message || "Không thể cập nhật mật khẩu.");
      } finally {
        this.isSavingPassword = false;
      }
    },
  },
  mounted() {
    this.taiHoSo();
  },
};
</script>

<style scoped>
.plan-page {
  min-height: 100vh;
  width: 100%;
  background:
    radial-gradient(circle at top right, rgba(12, 104, 150, 0.08), transparent 26%),
    linear-gradient(180deg, #eef5ff 0%, #f5f8ff 100%);
}

.plan-layout {
  width: min(1440px, calc(100% - 24px));
  margin: 0 auto;
  padding: 0 0 40px;
  display: grid;
  grid-template-columns: 264px minmax(0, 1fr);
  gap: 18px;
  min-height: calc(100vh - 68px);
}

.plan-content {
  padding: 28px 6px 0 0;
  min-width: 0;
}

.customer-profile-page {
  padding: 0;
}

.hero,
.card-head,
.actions-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.hero {
  margin-bottom: 1.5rem;
  align-items: flex-start;
}

.eyebrow {
  margin: 0 0 0.45rem;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.76rem;
  font-weight: 900;
}

.hero h1,
.card-head h2 {
  margin: 0;
  color: #111827;
  font-weight: 900;
  letter-spacing: -0.04em;
}

.hero h1 {
  font-size: clamp(2.2rem, 4vw, 3rem);
  line-height: 1;
}

.subtitle {
  margin: 0.7rem 0 0;
  color: #60728f;
  font-size: 1rem;
  max-width: 760px;
}

.profile-layout {
  display: grid;
  grid-template-columns: minmax(280px, 340px) minmax(0, 1fr);
  gap: 1.25rem;
}

.profile-main-stack {
  display: grid;
  gap: 1.25rem;
}

.card {
  background: rgba(255, 255, 255, 0.94);
  border: 1px solid rgba(219, 228, 240, 0.9);
  border-radius: 1.25rem;
  box-shadow: 0 24px 50px rgba(15, 23, 42, 0.08);
  padding: 1.2rem;
}

.identity-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.25rem;
}

.identity-avatar {
  width: 72px;
  height: 72px;
  border-radius: 24px;
  display: grid;
  place-items: center;
  background: linear-gradient(135deg, #f97316, #ea580c);
  color: #fff;
  font-size: 1.45rem;
  font-weight: 900;
  box-shadow: 0 18px 32px rgba(249, 115, 22, 0.28);
}

.identity-card h2 {
  margin: 0;
  color: #0f172a;
  font-size: 1.35rem;
}

.identity-card p {
  margin: 0.35rem 0 0;
  color: #64748b;
}

.summary-list {
  display: grid;
  gap: 0.95rem;
}

.summary-list span {
  display: block;
  color: #64748b;
  font-size: 0.85rem;
  margin-bottom: 0.25rem;
}

.summary-list strong {
  color: #0f172a;
  font-size: 0.98rem;
}

.success-text {
  color: #15803d;
}

.danger-text {
  color: #b91c1c;
}

.form-grid {
  display: grid;
  gap: 1rem;
  margin-top: 1rem;
}

.form-grid-2 {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.form-grid-3 {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.field {
  display: grid;
  gap: 0.5rem;
}

.field span {
  color: #334155;
  font-weight: 700;
}

.field input,
.field select {
  width: 100%;
  min-height: 3rem;
  padding: 0.85rem 1rem;
  border-radius: 0.95rem;
  border: 1px solid #dbe4f0;
  background: #fff;
  color: #0f172a;
  outline: none;
}

.password-note {
  margin: 0.85rem 0 0;
  color: #64748b;
  font-size: 0.9rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.65rem;
  min-height: 3rem;
  padding: 0 1.1rem;
  border-radius: 0.95rem;
  font-weight: 800;
  border: none;
  cursor: pointer;
  transition: 0.2s ease;
}

.btn-primary {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  color: #fff;
  box-shadow: 0 12px 24px rgba(37, 99, 235, 0.22);
}

.btn-ghost {
  border: 1px solid #dbe4f0;
  background: rgba(255, 255, 255, 0.88);
  color: #334155;
}

.btn:disabled {
  opacity: 0.65;
  cursor: not-allowed;
  box-shadow: none;
}

@media (max-width: 1280px) {
  .form-grid-3 {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 1200px) {
  .plan-layout {
    grid-template-columns: 1fr;
  }

  .profile-panel {
    border-right: 0;
    border-bottom: 1px solid #e4ecf8;
    padding-right: 0;
  }

  .profile-panel__menu {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .plan-content {
    padding-left: 0;
    padding-top: 28px;
  }

  .profile-layout {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 860px) {
  .plan-layout {
    width: calc(100% - 20px);
  }

  .profile-panel__menu {
    grid-template-columns: 1fr;
  }

  .hero,
  .card-head,
  .actions-row {
    flex-direction: column;
    align-items: stretch;
  }

  .form-grid-2 {
    grid-template-columns: 1fr;
  }
}
</style>
