<template>
  <div class="admin-profile-page">
    <section class="hero">
      <div>
        <p class="eyebrow">Tài khoản cá nhân</p>
        <h1>Hồ sơ Admin</h1>
        <p class="subtitle">Cập nhật thông tin cá nhân, kiểm tra trạng thái tài khoản và đổi mật khẩu quản trị tại một nơi duy nhất.</p>
      </div>
      <button class="btn btn-ghost" type="button" @click="fetchProfile" :disabled="isLoadingProfile">
        <i class="fas fa-rotate-right" :class="{ 'fa-spin': isLoadingProfile }"></i>
        <span>{{ isLoadingProfile ? 'Đang tải...' : 'Làm mới hồ sơ' }}</span>
      </button>
    </section>

    <section class="profile-layout">
      <aside class="profile-summary card">
        <div class="identity-card">
          <div class="identity-avatar">{{ initials }}</div>
          <div>
            <h2>{{ profile.Ho_va_ten || 'Tài khoản admin' }}</h2>
            <p>{{ profile.Email || 'Chưa có email' }}</p>
          </div>
        </div>
        <div class="summary-list">
          <div>
            <span>Mã tài khoản</span>
            <strong>{{ adminId || 'Chưa xác định' }}</strong>
          </div>
          <div>
            <span>Chức vụ</span>
            <strong>{{ roleName }}</strong>
          </div>
          <div>
            <span>Trạng thái</span>
            <strong :class="isBlocked ? 'danger-text' : 'success-text'">{{ isBlocked ? 'Đang khóa' : 'Hoạt động' }}</strong>
          </div>
          <div>
            <span>Số điện thoại</span>
            <strong>{{ profile.so_dien_thoai || 'Chưa cập nhật' }}</strong>
          </div>
        </div>
      </aside>

      <div class="profile-main">
        <section class="card">
          <div class="card-head">
            <div>
              <p class="eyebrow">Thông tin cá nhân</p>
              <h2>Cập nhật hồ sơ</h2>
            </div>
          </div>

          <div class="form-grid form-grid-2">
            <label class="field">
              <span>Họ và tên</span>
              <input v-model.trim="form.Ho_va_ten" type="text" placeholder="Nhập họ và tên">
            </label>
            <label class="field">
              <span>Email</span>
              <input v-model.trim="form.Email" type="email" placeholder="admin@smarttravel.vn">
            </label>
            <label class="field">
              <span>Số điện thoại</span>
              <input v-model.trim="form.so_dien_thoai" type="text" placeholder="0xxxxxxxxx">
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
            <label class="field">
              <span>Chức vụ</span>
              <input :value="roleName" type="text" readonly>
            </label>
          </div>

          <div class="actions-row">
            <button class="btn btn-primary" type="button" @click="saveProfile" :disabled="isSavingProfile || !canSaveProfile">
              <i v-if="isSavingProfile" class="fas fa-spinner fa-spin"></i>
              <span>{{ isSavingProfile ? 'Đang lưu...' : 'Lưu hồ sơ' }}</span>
            </button>
          </div>
        </section>

        <section class="card">
          <div class="card-head">
            <div>
              <p class="eyebrow">Bảo mật</p>
              <h2>Đổi mật khẩu</h2>
            </div>
          </div>

          <div class="form-grid">
            <label class="field">
              <span>Mật khẩu hiện tại</span>
              <input v-model="passwordForm.current_password" type="password" placeholder="Nhập mật khẩu hiện tại">
            </label>
            <label class="field">
              <span>Mật khẩu mới</span>
              <input v-model="passwordForm.new_password" type="password" placeholder="Tối thiểu 8 ký tự">
            </label>
            <label class="field">
              <span>Xác nhận mật khẩu mới</span>
              <input v-model="passwordForm.new_password_confirmation" type="password" placeholder="Nhập lại mật khẩu mới">
            </label>
          </div>

          <div class="actions-row">
            <button class="btn btn-primary" type="button" @click="changePassword" :disabled="isChangingPassword || !canChangePassword">
              <i v-if="isChangingPassword" class="fas fa-spinner fa-spin"></i>
              <span>{{ isChangingPassword ? 'Đang cập nhật...' : 'Đổi mật khẩu' }}</span>
            </button>
          </div>
        </section>
      </div>
    </section>
  </div>
</template>

<script>
import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({
  position: "top-right",
  duration: 3000,
});
import { goiApi } from "../../../services/httpClient.js";


const API_BASE = "/api";

async function goiDuLieu(url, { method = "GET", body, params, headers } = {}) {
  const phanHoi = await goiApi(url, { method, body, params, headers });
  const duLieu = await phanHoi.json();

  if (!phanHoi.ok) {
    const loi = new Error(duLieu?.message || "Không thể hoàn tất yêu cầu.");
    loi.response = { data: duLieu, status: phanHoi.status };
    throw loi;
  }

  return { data: duLieu, status: phanHoi.status };
}

function parseUser() {
  try {
    return JSON.parse(localStorage.getItem("user") || "null");
  } catch {
    return null;
  }
}

function saveUser(user) {
  if (user && typeof user === "object") {
    localStorage.setItem("user", JSON.stringify(user));
    window.dispatchEvent(new CustomEvent("admin-profile-updated"));
  }
}

function parseCollection(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
}

export default {
  name: "HoSoAdmin",
  data() {
    return {
      profile: {},
      roles: [],
      form: {
        Ho_va_ten: "",
        Email: "",
        Ngay_sinh: "",
        Gioi_tinh: 1,
        so_dien_thoai: "",
        ma_chuc_vu: "",
      },
      passwordForm: {
        current_password: "",
        new_password: "",
        new_password_confirmation: "",
      },
      isLoadingProfile: false,
      isSavingProfile: false,
      isChangingPassword: false,
    };
  },
  computed: {
    adminId() {
      const user = parseUser();
      return String(user?.Ma_admin || user?.ma_admin || "");
    },
    initials() {
      const name = String(this.profile.Ho_va_ten || "A").trim();
      const parts = name.split(/\s+/).filter(Boolean);
      return parts.slice(0, 2).map((part) => part.charAt(0).toUpperCase()).join("") || "AD";
    },
    roleName() {
      const found = this.roles.find((role) => String(role?.ma_chuc_vu || role?.Ma_chuc_vu || "") === String(this.profile.ma_chuc_vu || ""));
      return found?.ten_chuc_vu || found?.Ten_chuc_vu || (this.profile.ma_chuc_vu || "Chưa gán chức vụ");
    },
    isBlocked() {
      return Number(this.profile?.is_block ?? 0) === 1;
    },
    canSaveProfile() {
      return Boolean(this.form.Ho_va_ten && this.form.Email && this.form.Ngay_sinh && this.form.so_dien_thoai);
    },
    canChangePassword() {
      return Boolean(
        this.passwordForm.current_password &&
        this.passwordForm.new_password &&
        this.passwordForm.new_password_confirmation &&
        this.passwordForm.new_password.length >= 8,
      );
    },
  },
  methods: {
    authHeaders() {
      const token = localStorage.getItem("token");
      return {
        Accept: "application/json",
        Authorization: token ? `Bearer ${token}` : "",
      };
    },
    formatError(error, fallback) {
      return error?.response?.data?.message || error?.message || fallback;
    },
    syncForm() {
      this.form = {
        Ho_va_ten: this.profile.Ho_va_ten || "",
        Email: this.profile.Email || "",
        Ngay_sinh: this.profile.Ngay_sinh || "",
        Gioi_tinh: Number(this.profile.Gioi_tinh ?? 1),
        so_dien_thoai: this.profile.so_dien_thoai || "",
        ma_chuc_vu: this.profile.ma_chuc_vu || "",
      };
    },
    async fetchProfile() {
      if (!this.adminId) {
        toaster.error("Không xác định được mã admin hiện tại trong bộ nhớ trình duyệt.");
        return;
      }

      this.isLoadingProfile = true;
      try {
        const [profileResponse, rolesResponse] = await Promise.all([
          goiDuLieu(`${API_BASE}/admin/${this.adminId}`, { headers: this.authHeaders() }),
          goiDuLieu(`${API_BASE}/chuc-vu/all`, { headers: this.authHeaders() }),
        ]);
        this.profile = profileResponse?.data?.data || {};
        this.roles = parseCollection(rolesResponse.data);
        this.syncForm();
        const current = parseUser() || {};
        saveUser({ ...current, ...this.profile });
      } catch (error) {
        toaster.error(this.formatError(error, "Đã xảy ra lỗi khi tải thông tin tài khoản admin."));
      } finally {
        this.isLoadingProfile = false;
      }
    },
    async saveProfile() {
      if (!this.adminId) return;
      this.isSavingProfile = true;
      try {
        const response = await goiDuLieu(`${API_BASE}/admin/${this.adminId}`, {
          method: "PUT",
          body: this.form,
          headers: this.authHeaders(),
        });
        this.profile = { ...this.profile, ...(response?.data?.data || this.form) };
        this.syncForm();
        const current = parseUser() || {};
        saveUser({ ...current, ...this.profile });
        toaster.success(response?.data?.message || "Thông tin cá nhân đã được lưu thành công.");
      } catch (error) {
        toaster.error(this.formatError(error, "Không thể cập nhật hồ sơ admin."));
      } finally {
        this.isSavingProfile = false;
      }
    },
    async changePassword() {
      if (!this.adminId) return;
      if (this.passwordForm.new_password !== this.passwordForm.new_password_confirmation) {
        toaster.warning("Mật khẩu mới và phần xác nhận phải trùng nhau.");
        return;
      }

      this.isChangingPassword = true;
      try {
        const response = await goiDuLieu(`${API_BASE}/admin/password`, {
          method: "PUT",
          body: this.passwordForm,
          headers: this.authHeaders(),
        });
        this.passwordForm = {
          current_password: "",
          new_password: "",
          new_password_confirmation: "",
        };
        toaster.success(response?.data?.message || "Mật khẩu quản trị đã được cập nhật thành công.");
      } catch (error) {
        toaster.error(this.formatError(error, "Không thể thay đổi mật khẩu cho tài khoản này."));
      } finally {
        this.isChangingPassword = false;
      }
    },
  },
  mounted() {
    this.fetchProfile();
  },
};
</script>

<style scoped>
.admin-profile-page { min-height: 100%; padding: 2rem 2.25rem 2.5rem; background: radial-gradient(circle at top left, rgba(37,99,235,.08), transparent 24%), linear-gradient(180deg, #f7f9fe 0%, #eef3fb 100%); }
.hero, .card-head, .actions-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
.hero { margin-bottom: 1.5rem; align-items: flex-start; }
.eyebrow { margin: 0 0 .45rem; color: #2453ff; text-transform: uppercase; letter-spacing: .14em; font-size: .76rem; font-weight: 900; }
.hero h1, .card-head h2 { margin: 0; color: #111827; font-weight: 900; letter-spacing: -.04em; }
.hero h1 { font-size: clamp(2.2rem, 4vw, 3rem); line-height: 1; }
.subtitle { margin: .7rem 0 0; color: #60728f; font-size: 1rem; max-width: 760px; }
.profile-layout { display: grid; grid-template-columns: minmax(280px, 340px) minmax(0, 1fr); gap: 1.25rem; }
.card { background: rgba(255,255,255,.94); border: 1px solid rgba(219,228,240,.9); border-radius: 1.25rem; box-shadow: 0 24px 50px rgba(15,23,42,.08); padding: 1.2rem; }
.profile-main { display: grid; gap: 1.25rem; }
.identity-card { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem; }
.identity-avatar { width: 72px; height: 72px; border-radius: 24px; display: grid; place-items: center; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; font-size: 1.45rem; font-weight: 900; box-shadow: 0 18px 32px rgba(37,99,235,.28); }
.identity-card h2 { margin: 0; color: #0f172a; font-size: 1.35rem; }
.identity-card p { margin: .35rem 0 0; color: #64748b; }
.summary-list { display: grid; gap: .95rem; }
.summary-list span { display: block; color: #64748b; font-size: .85rem; margin-bottom: .25rem; }
.summary-list strong { color: #0f172a; font-size: .98rem; }
.success-text { color: #15803d; }
.danger-text { color: #b91c1c; }
.form-grid { display: grid; gap: 1rem; margin-top: 1rem; }
.form-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.field { display: grid; gap: .5rem; }
.field span { color: #334155; font-weight: 700; }
.field input, .field select { width: 100%; min-height: 3rem; padding: .85rem 1rem; border-radius: .95rem; border: 1px solid #dbe4f0; background: #fff; color: #0f172a; outline: none; }
.btn { display: inline-flex; align-items: center; justify-content: center; gap: .65rem; min-height: 3rem; padding: 0 1.1rem; border-radius: .95rem; font-weight: 800; border: none; cursor: pointer; transition: .2s ease; }
.btn-primary { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; box-shadow: 0 12px 24px rgba(37,99,235,.22); }
.btn-ghost { border: 1px solid #dbe4f0; background: rgba(255,255,255,.88); color: #334155; }
.btn:disabled { opacity: .65; cursor: not-allowed; box-shadow: none; }
@media (max-width: 1080px) { .profile-layout { grid-template-columns: 1fr; } }
@media (max-width: 768px) {
  .admin-profile-page { padding: 1rem; }
  .hero, .card-head, .actions-row { flex-direction: column; align-items: stretch; }
  .form-grid-2 { grid-template-columns: 1fr; }
}
</style>
