<template>
  <PartnerShell
    title="Thông tin tài khoản"
    subtitle="Quản lý thông tin hồ sơ và bảo mật của bạn."
    :partner="partner"
    :loading="loading"
  >
    <template #headerActions>
      <button class="toolbar-btn toolbar-btn--ghost" type="button" @click="loadProfile" :disabled="loading">
        <i class="fas fa-rotate-right" :class="{ 'fa-spin': loading }"></i>
        Làm mới
      </button>
    </template>

    <div class="profile-layout">
      <!-- Profile Summary aside -->
      <aside class="profile-summary card">
        <div class="identity-card">
          <div class="identity-avatar">{{ initials }}</div>
          <div>
            <h2>{{ displayName }}</h2>
            <p>{{ partner?.email || "Chưa có email" }}</p>
          </div>
        </div>

        <div class="summary-list">
          <div>
            <span>Mã đối tác</span>
            <strong>{{ partner?.ma_doi_tac || "Chưa xác định" }}</strong>
          </div>
          <div>
            <span>Trạng thái</span>
            <strong :class="isBlocked ? 'danger-text' : 'success-text'">
              {{ isBlocked ? "Đang khóa" : "Hoạt động" }}
            </strong>
          </div>
          <div>
            <span>Trạng thái duyệt</span>
            <strong :class="approvalStatusClass">
              {{ mapStatusLabel(partner?.trang_thai_duyet) }}
            </strong>
          </div>
          <div>
            <span>Người đại diện</span>
            <strong>{{ partner?.ten_nguoi_dai_dien || "Chưa cập nhật" }}</strong>
          </div>
          <div>
            <span>Ngày tạo tài khoản</span>
            <strong>{{ createdAtLabel }}</strong>
          </div>
        </div>
      </aside>

      <div class="profile-main-stack">
        <!-- Update Profile Card -->
        <section class="profile-main card">
          <header class="card-head">
            <div>
              <p class="eyebrow">Thông tin của tôi</p>
              <h2>Cập nhật hồ sơ</h2>
            </div>
          </header>

          <div class="form-grid form-grid-2">
            <label class="field">
              <span>Tên đối tác</span>
              <input v-model.trim="form.ten_doi_tac" type="text" placeholder="Nhập tên đối tác">
            </label>
            <label class="field">
              <span>Người đại diện</span>
              <input v-model.trim="form.ten_nguoi_dai_dien" type="text" placeholder="Nhập người đại diện">
            </label>
            <label class="field">
              <span>Số điện thoại</span>
              <input v-model.trim="form.so_dien_thoai" type="text" inputmode="numeric" maxlength="10" placeholder="0xxxxxxxxx">
            </label>
            <label class="field">
              <span>Mã số thuế</span>
              <input v-model.trim="form.ma_so_thue" type="text" placeholder="Nhập mã số thuế">
            </label>
            <label class="field" style="grid-column: 1 / -1;">
              <span>Email (Không thể thay đổi)</span>
              <input :value="partner?.email" type="email" disabled>
            </label>
            <label class="field" style="grid-column: 1 / -1;">
              <span>Địa chỉ</span>
              <input v-model.trim="form.dia_chi" type="text" placeholder="Nhập địa chỉ đầy đủ">
            </label>
          </div>

          <div class="actions-row">
            <button class="btn btn-primary" type="button" @click="saveProfile" :disabled="isSavingProfile || !canSaveProfile">
              <i v-if="isSavingProfile" class="fas fa-spinner fa-spin"></i>
              <span>{{ isSavingProfile ? "Đang lưu..." : "Lưu thông tin" }}</span>
            </button>
          </div>
        </section>

        <!-- Change Password Card -->
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
              <input v-model="passwordForm.current_password" type="password" autocomplete="current-password" placeholder="Nhập mật khẩu hiện tại">
            </label>
            <label class="field">
              <span>Mật khẩu mới</span>
              <input v-model="passwordForm.new_password" type="password" autocomplete="new-password" placeholder="Tối thiểu 8 ký tự">
            </label>
            <label class="field">
              <span>Xác nhận mật khẩu mới</span>
              <input v-model="passwordForm.confirm_password" type="password" autocomplete="new-password" placeholder="Nhập lại mật khẩu mới">
            </label>
          </div>

          <p class="password-note">Mật khẩu mới phải có tối thiểu 8 ký tự.</p>

          <div class="actions-row">
            <button class="btn btn-primary" type="button" @click="savePassword" :disabled="isSavingPassword || !canSavePassword">
              <i v-if="isSavingPassword" class="fas fa-spinner fa-spin"></i>
              <span>{{ isSavingPassword ? "Đang cập nhật..." : "Cập nhật mật khẩu" }}</span>
            </button>
          </div>
        </section>
      </div>
    </div>
  </PartnerShell>
</template>

<script>
import PartnerShell from "./layout/PartnerShell.vue";
import { fetchPartnerSession, updatePartnerProfile, changePartnerPassword, formatDateTime, mapStatusLabel } from "./shared/partnerApi";

import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({ position: 'top-right' });

function formatDateDisplay(value) {
  if (!value) return "--";
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return "--";
  return date.toLocaleDateString("vi-VN", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
}

export default {
  name: "PartnerThongTinTaiKhoan",
  components: {
    PartnerShell,
  },
  data() {
    return {
      loading: false,
      isSavingProfile: false,
      isSavingPassword: false,
      partner: null,
      form: {
        ten_doi_tac: "",
        ten_nguoi_dai_dien: "",
        so_dien_thoai: "",
        dia_chi: "",
        ma_so_thue: "",
      },
      passwordForm: {
        current_password: "",
        new_password: "",
        confirm_password: "",
      },
    };
  },
  computed: {
    displayName() {
      return (
        this.partner?.ten_doi_tac
        || this.partner?.ten_nguoi_dai_dien
        || "Tài khoản đối tác"
      );
    },
    initials() {
      const name = String(this.displayName).trim();
      return (
        name
          .split(/\s+/)
          .filter(Boolean)
          .slice(0, 2)
          .map((part) => part.charAt(0).toUpperCase())
          .join("") || "DT"
      );
    },
    isBlocked() {
      return Number(this.partner?.is_block ?? 0) === 1;
    },
    createdAtLabel() {
      return formatDateDisplay(this.partner?.created_at);
    },
    approvalStatusClass() {
      const status = String(this.partner?.trang_thai_duyet || "").toLowerCase();
      if (status === "approved") return "success-text";
      if (status === "rejected") return "danger-text";
      return "warning-text";
    },
    canSaveProfile() {
      return Boolean(this.form.ten_doi_tac && this.form.ten_nguoi_dai_dien);
    },
    canSavePassword() {
      return Boolean(
        this.passwordForm.current_password &&
        this.passwordForm.new_password &&
        this.passwordForm.confirm_password &&
        this.passwordForm.new_password.length >= 8 &&
        this.passwordForm.new_password === this.passwordForm.confirm_password
      );
    },
  },
  methods: {
    mapStatusLabel,
    syncFormFromProfile() {
      this.form = {
        ten_doi_tac: this.partner?.ten_doi_tac || "",
        ten_nguoi_dai_dien: this.partner?.ten_nguoi_dai_dien || "",
        so_dien_thoai: this.partner?.so_dien_thoai || this.partner?.sdt || "",
        dia_chi: this.partner?.dia_chi || "",
        ma_so_thue: this.partner?.ma_so_thue || "",
      };
    },
    syncUserStorage() {
      const currentUserStr = localStorage.getItem("doi_tac_user");
      if (!currentUserStr) return;
      try {
        const currentUser = JSON.parse(currentUserStr);
        const nextUser = {
          ...currentUser,
          ...this.partner,
        };
        localStorage.setItem("doi_tac_user", JSON.stringify(nextUser));
        window.dispatchEvent(new Event("storage"));
      } catch (e) {
        // ignore
      }
    },
    resetPasswordForm() {
      this.passwordForm = {
        current_password: "",
        new_password: "",
        confirm_password: "",
      };
    },
    async loadProfile() {
      this.loading = true;
      try {
        this.partner = await fetchPartnerSession();
        this.syncFormFromProfile();
        this.syncUserStorage();
      } catch (error) {
        toaster.error(error?.message || "Không thể tải thông tin tài khoản đối tác.");
      } finally {
        this.loading = false;
      }
    },
    async saveProfile() {
      if (!this.canSaveProfile) return;
      this.isSavingProfile = true;

      try {
        const payload = await updatePartnerProfile(this.form);
        this.partner = payload || this.partner;
        this.syncFormFromProfile();
        this.syncUserStorage();
        toaster.success("Thông tin cá nhân đã được lưu.");
      } catch (error) {
        toaster.error(error?.message || "Không thể lưu thông tin.");
      } finally {
        this.isSavingProfile = false;
      }
    },
    async savePassword() {
      if (!this.canSavePassword) return;

      if (this.passwordForm.new_password !== this.passwordForm.confirm_password) {
        toaster.error("Vui lòng nhập lại đúng mật khẩu mới để tiếp tục.");
        return;
      }

      this.isSavingPassword = true;
      try {
        await changePartnerPassword({
          current_password: this.passwordForm.current_password,
          new_password: this.passwordForm.new_password,
        });
        this.resetPasswordForm();
        toaster.success("Mật khẩu của bạn đã được cập nhật.");
      } catch (error) {
        toaster.error(error?.message || "Không thể cập nhật mật khẩu.");
      } finally {
        this.isSavingPassword = false;
      }
    },
  },
  mounted() {
    this.loadProfile();
  },
};
</script>

<style scoped>
.toolbar-btn {
  min-height: 2.75rem;
  border-radius: 0.75rem;
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
  font-weight: 700;
  padding: 0 1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
}

.toolbar-btn:hover {
  background: #dbeafe;
  transform: translateY(-1px);
}
.toolbar-btn:disabled {
  opacity: 0.65;
  cursor: not-allowed;
  transform: none;
}

.profile-layout {
  display: grid;
  grid-template-columns: minmax(280px, 340px) minmax(0, 1fr);
  gap: 1.25rem;
  margin-top: 1rem;
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
  padding-bottom: 1.25rem;
  border-bottom: 1px solid #e4ecf8;
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
  font-weight: 800;
}

.identity-card p {
  margin: 0.35rem 0 0;
  color: #64748b;
  font-size: 0.9rem;
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
  font-weight: 600;
}

.summary-list strong {
  color: #0f172a;
  font-size: 0.98rem;
  font-weight: 700;
}

.success-text { color: #15803d; }
.danger-text { color: #b91c1c; }
.warning-text { color: #b45309; }

.card-head,
.actions-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.card-head h2 {
  margin: 0;
  color: #111827;
  font-weight: 900;
  letter-spacing: -0.04em;
}

.eyebrow {
  margin: 0 0 0.45rem;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.76rem;
  font-weight: 900;
}

.form-grid {
  display: grid;
  gap: 1rem;
  margin-top: 1.5rem;
  margin-bottom: 1.5rem;
}

.form-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.form-grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }

.field {
  display: grid;
  gap: 0.5rem;
}

.field span {
  color: #334155;
  font-weight: 700;
  font-size: 0.9rem;
}

.field input {
  width: 100%;
  min-height: 3rem;
  padding: 0.85rem 1rem;
  border-radius: 0.95rem;
  border: 1px solid #dbe4f0;
  background: #fff;
  color: #0f172a;
  outline: none;
  font-size: 0.95rem;
  transition: all 0.2s;
}

.field input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

.field input:disabled {
  background: #f1f5f9;
  color: #64748b;
  cursor: not-allowed;
}

.password-note {
  margin: 0 0 1.5rem;
  color: #64748b;
  font-size: 0.9rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.65rem;
  min-height: 3rem;
  padding: 0 1.25rem;
  border-radius: 0.95rem;
  font-weight: 800;
  border: none;
  cursor: pointer;
  transition: 0.2s ease;
  font-size: 0.95rem;
}

.btn-primary {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  color: #fff;
  box-shadow: 0 12px 24px rgba(37, 99, 235, 0.22);
}
.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 16px 32px rgba(37, 99, 235, 0.3);
}

.btn:disabled {
  opacity: 0.65;
  cursor: not-allowed;
  box-shadow: none;
}

@media (max-width: 1280px) {
  .form-grid-3 { grid-template-columns: 1fr; }
}

@media (max-width: 900px) {
  .profile-layout { grid-template-columns: 1fr; }
  .form-grid-2 { grid-template-columns: 1fr; }
  .card-head, .actions-row { flex-direction: column; align-items: stretch; }
}
</style>
