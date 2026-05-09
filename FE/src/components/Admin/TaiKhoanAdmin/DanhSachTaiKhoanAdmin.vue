<template>
  <div class="admin-account-page">
    <section class="hero">
      <div>
        <p class="eyebrow">Quản trị tài khoản nội bộ</p>
        <h1>Tài khoản Admin</h1>
        <p class="subtitle">Quản lý danh sách quản trị viên, phân vai trò, khóa mở tài khoản và kiểm soát thông tin đăng
          nhập nội bộ.</p>
      </div>
      <div class="hero-actions">
        <button class="btn btn-ghost" type="button" @click="taiDuLieu" :disabled="dangTai">
          <i class="fas fa-rotate-right" :class="{ 'fa-spin': dangTai }"></i>
          <span>{{ dangTai ? 'Đang đồng bộ...' : 'Tải lại' }}</span>
        </button>
        <button class="btn btn-primary" type="button" @click="openModal()">
          <i class="fas fa-plus"></i>
          <span>Thêm admin</span>
        </button>
      </div>
    </section>

    <section class="stats-grid">
      <article class="stats-card">
        <i class="fas fa-user-shield"></i>
        <div><span>Tổng tài khoản</span><strong>{{ admins.length }}</strong></div>
      </article>
      <article class="stats-card">
        <i class="fas fa-user-check"></i>
        <div><span>Đang hoạt động</span><strong>{{ activeAdminsCount }}</strong></div>
      </article>
      <article class="stats-card">
        <i class="fas fa-user-lock"></i>
        <div><span>Đang khóa</span><strong>{{ blockedAdminsCount }}</strong></div>
      </article>
      <article class="stats-card">
        <i class="fas fa-briefcase"></i>
        <div><span>Chức vụ khả dụng</span><strong>{{ roles.length }}</strong></div>
      </article>
    </section>

    <section class="card">
      <div class="toolbar">
        <label class="search-box">
          <i class="fas fa-search"></i>
          <input v-model.trim="searchKeyword" type="text" placeholder="Tìm theo mã, họ tên, email hoặc số điện thoại">
        </label>
      </div>

      <div v-if="filteredAdmins.length" class="table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Mã admin</th>
              <th>Thông tin</th>
              <th>Liên hệ</th>
              <th>Chức vụ</th>
              <th>Trạng thái</th>
              <th class="text-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="admin in filteredAdmins" :key="adminKey(admin)">
              <td>
                <strong>{{ adminKey(admin) }}</strong>
                <small>{{ formatDate(admin.created_at) }}</small>
              </td>
              <td>
                <strong>{{ admin.Ho_va_ten || 'Chưa cập nhật' }}</strong>
                <small>{{ normalizeGender(admin.Gioi_tinh) }} · {{ formatDate(admin.Ngay_sinh) }}</small>
              </td>
              <td>
                <strong>{{ admin.Email || 'Chưa có email' }}</strong>
                <small>{{ admin.so_dien_thoai || 'Chưa có số điện thoại' }}</small>
              </td>
              <td>
                <strong>{{ roleName(admin.ma_chuc_vu) }}</strong>
                <small>{{ admin.ma_chuc_vu || 'Chưa gán chức vụ' }}</small>
              </td>
              <td>
                <span :class="['pill', isBlocked(admin) ? 'pill-danger' : 'pill-success']">
                  {{ isBlocked(admin) ? 'Đang khóa' : 'Hoạt động' }}
                </span>
              </td>
              <td>
                <div class="actions-inline justify-center">
                  <button class="icon-btn" type="button" title="Chỉnh sửa tài khoản" @click="openModal(admin)">
                    <i class="fas fa-pen"></i>
                  </button>
                  <button class="icon-btn" type="button"
                    :title="isBlocked(admin) ? 'Mở khóa tài khoản' : 'Khóa tài khoản'" :disabled="isBusy(admin)"
                    @click="toggleStatus(admin)">
                    <i class="fas" :class="isBlocked(admin) ? 'fa-lock-open' : 'fa-lock'"></i>
                  </button>
                  <button class="icon-btn icon-btn-danger" type="button" title="Xóa tài khoản" :disabled="isBusy(admin)"
                    @click="deleteAdmin(admin)">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="empty-state">
        <i class="fas fa-user-shield"></i>
        <p>Không có tài khoản admin nào khớp với bộ lọc hiện tại.</p>
      </div>
    </section>

    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <article class="modal-card modal-card-wide">
        <div class="card-head">
          <div>
            <p class="eyebrow">Tài khoản quản trị</p>
            <h2>{{ form.Ma_admin ? 'Cập nhật tài khoản admin' : 'Tạo tài khoản admin mới' }}</h2>
          </div>
          <button class="icon-btn" type="button" @click="closeModal"><i class="fas fa-times"></i></button>
        </div>

        <div class="form-grid form-grid-2">
          <label class="field">
            <span>Họ và tên</span>
            <input v-model.trim="form.Ho_va_ten" type="text" placeholder="Ví dụ: Nguyễn Văn A">
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
            <select v-model="form.ma_chuc_vu">
              <option value="" disabled>Chọn chức vụ</option>
              <option v-for="role in roles" :key="roleKey(role)" :value="roleKey(role)">
                {{ role.ten_chuc_vu || role.Ten_chuc_vu }} ({{ roleKey(role) }})
              </option>
            </select>
          </label>
          <label v-if="!form.Ma_admin" class="field field-full">
            <span>Mật khẩu khởi tạo</span>
            <input v-model="form.Mat_khau" type="password" placeholder="Tối thiểu 8 ký tự">
          </label>
        </div>

        <div class="modal-actions">
          <button class="btn btn-ghost btn-small" type="button" @click="closeModal">Hủy</button>
          <button class="btn btn-primary btn-small" type="button" @click="saveAdmin" :disabled="dangLuu || !canSubmit">
            <i v-if="dangLuu" class="fas fa-spinner fa-spin"></i>
            <span>{{ dangLuu ? 'Đang lưu...' : (form.Ma_admin ? 'Lưu thay đổi' : 'Tạo tài khoản') }}</span>
          </button>
        </div>
      </article>
    </div>
  </div>
</template>

<script>
import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({
  position: "top-right",
  duration: 3000,
});
import { goiApi } from "../../../services/httpClient.js";
import { showConfirm } from "../../../services/appDialog";

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

function parseCollection(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
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

function defaultForm() {
  return {
    Ma_admin: "",
    Ho_va_ten: "",
    Email: "",
    Ngay_sinh: "",
    Gioi_tinh: 1,
    ma_chuc_vu: "",
    so_dien_thoai: "",
    Mat_khau: "",
  };
}

export default {
  name: "DanhSachTaiKhoanAdmin",
  data() {
    return {
      admins: [],
      roles: [],
      dangTai: false,
      dangLuu: false,
      searchKeyword: "",
      showModal: false,
      busyIds: [],
      form: defaultForm(),
    };
  },
  computed: {
    filteredAdmins() {
      const keyword = this.searchKeyword.toLowerCase();
      if (!keyword) return this.admins;
      return this.admins.filter((admin) =>
        [
          this.adminKey(admin),
          admin.Ho_va_ten,
          admin.Email,
          admin.so_dien_thoai,
          admin.ma_chuc_vu,
          this.roleName(admin.ma_chuc_vu),
        ]
          .filter(Boolean)
          .some((value) => String(value).toLowerCase().includes(keyword)),
      );
    },
    activeAdminsCount() {
      return this.admins.filter((admin) => !this.isBlocked(admin)).length;
    },
    blockedAdminsCount() {
      return this.admins.filter((admin) => this.isBlocked(admin)).length;
    },
    currentAdminId() {
      const user = parseUser();
      return String(user?.Ma_admin || user?.ma_admin || "");
    },
    canSubmit() {
      if (!this.form.Ho_va_ten || !this.form.Email || !this.form.Ngay_sinh || !this.form.so_dien_thoai || !this.form.ma_chuc_vu) {
        return false;
      }
      if (!this.form.Ma_admin && String(this.form.Mat_khau || "").length < 8) {
        return false;
      }
      return true;
    },
  },
  methods: {
    defaultForm() {
      return defaultForm();
    },
    adminKey(admin) {
      return String(admin?.Ma_admin || admin?.ma_admin || "");
    },
    roleKey(role) {
      return String(role?.ma_chuc_vu || role?.Ma_chuc_vu || "");
    },
    authHeaders() {
      const token = localStorage.getItem("token");
      return {
        Accept: "application/json",
        Authorization: token ? `Bearer ${token}` : "",
      };
    },
    roleName(roleId) {
      const found = this.roles.find((role) => this.roleKey(role) === String(roleId || ""));
      return found?.ten_chuc_vu || found?.Ten_chuc_vu || "Chưa gán chức vụ";
    },
    normalizeGender(value) {
      return Number(value) === 1 ? "Nam" : "Nữ";
    },
    isBlocked(admin) {
      return Number(admin?.is_block ?? admin?.Is_block ?? 0) === 1;
    },
    formatDate(value) {
      if (!value) return "Chưa cập nhật";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return String(value);
      return date.toLocaleDateString("vi-VN");
    },
    formatError(error, fallback) {
      return error?.response?.data?.message || error?.message || fallback;
    },
    isBusy(admin) {
      return this.busyIds.includes(this.adminKey(admin));
    },
    pushBusy(id) {
      if (!this.busyIds.includes(id)) this.busyIds.push(id);
    },
    popBusy(id) {
      this.busyIds = this.busyIds.filter((item) => item !== id);
    },
    async taiDuLieu() {
      this.dangTai = true;
      try {
        const [adminsResponse, rolesResponse] = await Promise.all([
          goiDuLieu(`${API_BASE}/admins`, { headers: this.authHeaders() }),
          goiDuLieu(`${API_BASE}/chuc-vu/all`, { headers: this.authHeaders() }),
        ]);
        this.admins = parseCollection(adminsResponse.data);
        this.roles = parseCollection(rolesResponse.data);
      } catch (error) {
        if (error?.response?.status === 404) {
          this.admins = [];
        } else {
          toaster.error(this.formatError(error, "Đã xảy ra lỗi khi tải danh sách quản trị viên."));
        }
      } finally {
        this.dangTai = false;
      }
    },
    openModal(admin = null) {
      if (!admin) {
        this.form = this.defaultForm();
        this.showModal = true;
        return;
      }

      this.form = {
        Ma_admin: this.adminKey(admin),
        Ho_va_ten: admin.Ho_va_ten || "",
        Email: admin.Email || "",
        Ngay_sinh: admin.Ngay_sinh || "",
        Gioi_tinh: Number(admin.Gioi_tinh ?? 1),
        ma_chuc_vu: String(admin.ma_chuc_vu || ""),
        so_dien_thoai: admin.so_dien_thoai || "",
        Mat_khau: "",
      };
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.form = this.defaultForm();
    },
    async saveAdmin() {
      this.dangLuu = true;
      const payload = {
        Ho_va_ten: this.form.Ho_va_ten,
        Email: this.form.Email,
        Ngay_sinh: this.form.Ngay_sinh,
        Gioi_tinh: Number(this.form.Gioi_tinh),
        ma_chuc_vu: this.form.ma_chuc_vu,
        so_dien_thoai: this.form.so_dien_thoai,
      };

      if (!this.form.Ma_admin) {
        payload.Mat_khau = this.form.Mat_khau;
      }
      try {
        let response;
        if (this.form.Ma_admin) {
          response = await goiDuLieu(`${API_BASE}/admin/${this.form.Ma_admin}`, {
            method: "PUT",
            body: payload,
            headers: this.authHeaders(),
          });
          const updated = response?.data?.data || payload;
          this.admins = this.admins.map((item) =>
            this.adminKey(item) === this.form.Ma_admin ? { ...item, ...updated } : item,
          );
          if (this.form.Ma_admin === this.currentAdminId) {
            const current = parseUser() || {};
            saveUser({ ...current, ...updated });
          }
          this.closeModal();
          toaster.success(response?.data?.message || "Thông tin quản trị viên đã được cập nhật.");
        } else {
          response = await goiDuLieu(`${API_BASE}/admin/store`, {
            method: "POST",
            body: payload,
            headers: this.authHeaders(),
          });
          const created = response?.data?.data;
          if (created) this.admins = [created, ...this.admins];
          this.closeModal();
          toaster.success(response?.data?.message || "Tài khoản admin mới đã được tạo thành công.");
        }
      } catch (error) {
        toaster.error(this.formatError(error, "Không thể lưu thông tin tài khoản admin."));
      } finally {
        this.dangLuu = false;
      }
    },
    async toggleStatus(admin) {
      const adminId = this.adminKey(admin);
      if (adminId === this.currentAdminId) {
        toaster.warning("Bạn đang đăng nhập bằng tài khoản này. Hãy dùng một tài khoản quản trị khác nếu cần khóa nó.");
        return;
      }

      const nextStatus = this.isBlocked(admin) ? 0 : 1;
      const confirmed = await showConfirm({
        title: nextStatus === 1 ? "Khóa tài khoản admin" : "Mở khóa tài khoản admin",
        message: nextStatus === 1
          ? `Tài khoản ${admin.Ho_va_ten} sẽ tạm thời không thể đăng nhập.`
          : `Tài khoản ${admin.Ho_va_ten} sẽ được hoạt động trở lại.`,
        confirmText: nextStatus === 1 ? "Khóa tài khoản" : "Mở khóa",
        cancelText: "Hủy",
        tone: nextStatus === 1 ? "danger" : "primary",
      });
      if (!confirmed) return;

      this.pushBusy(adminId);
      try {
        const response = await goiDuLieu(`${API_BASE}/admin/${adminId}/status`, {
          method: "PATCH",
          body: { is_block: nextStatus },
          headers: this.authHeaders(),
        });
        const updated = response?.data?.data || { ...admin, is_block: nextStatus };
        this.admins = this.admins.map((item) => (this.adminKey(item) === adminId ? { ...item, ...updated } : item));
        toaster.success(response?.data?.message || "Trạng thái tài khoản admin đã được cập nhật.");
      } catch (error) {
        toaster.error(this.formatError(error, "Đã xảy ra lỗi khi thay đổi trạng thái tài khoản admin."));
      } finally {
        this.popBusy(adminId);
      }
    },
    async deleteAdmin(admin) {
      const adminId = this.adminKey(admin);
      if (adminId === this.currentAdminId) {
        toaster.warning("Bạn đang đăng nhập bằng tài khoản này. Hãy dùng một tài khoản quản trị khác nếu cần xóa nó.");
        return;
      }

      const confirmed = await showConfirm({
        title: "Xóa tài khoản admin",
        message: `Bạn có chắc muốn xóa tài khoản ${admin.Ho_va_ten} không? Hành động này không thể hoàn tác.`,
        confirmText: "Xóa tài khoản",
        cancelText: "Hủy",
        tone: "danger",
      });
      if (!confirmed) return;

      this.pushBusy(adminId);
      try {
        const response = await goiDuLieu(`${API_BASE}/admin/${adminId}`, {
          method: "DELETE",
          headers: this.authHeaders(),
        });
        this.admins = this.admins.filter((item) => this.adminKey(item) !== adminId);
        toaster.success(response?.data?.message || "Tài khoản admin đã được xóa thành công.");
      } catch (error) {
        toaster.error(this.formatError(error, "Không thể xóa tài khoản admin này."));
      } finally {
        this.popBusy(adminId);
      }
    },
  },
  mounted() {
    this.taiDuLieu();
  },
};
</script>

<style scoped>
.admin-account-page {
  min-height: 100%;
  padding: 2rem 2.25rem 2.5rem;
  background: radial-gradient(circle at top right, rgba(37, 99, 235, .08), transparent 24%), linear-gradient(180deg, #f7f9fe 0%, #eef3fb 100%);
}

.hero,
.hero-actions,
.toolbar,
.card-head,
.modal-actions {
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
  margin: 0 0 .45rem;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: .14em;
  font-size: .76rem;
  font-weight: 900;
}

.hero h1,
.card-head h2 {
  margin: 0;
  color: #111827;
  font-weight: 900;
  letter-spacing: -.04em;
}

.hero h1 {
  font-size: clamp(2.2rem, 4vw, 3rem);
  line-height: 1;
}

.subtitle {
  margin: .7rem 0 0;
  color: #60728f;
  font-size: 1rem;
  max-width: 760px;
}

.stats-grid,
.form-grid {
  display: grid;
  gap: 1rem;
}

.stats-grid {
  grid-template-columns: repeat(4, minmax(0, 1fr));
  margin-bottom: 1.5rem;
}

.stats-card,
.card,
.modal-card {
  background: rgba(255, 255, 255, .94);
  border: 1px solid rgba(219, 228, 240, .9);
  border-radius: 1.25rem;
  box-shadow: 0 24px 50px rgba(15, 23, 42, .08);
}

.stats-card {
  padding: 1.15rem 1.25rem;
  display: flex;
  gap: .9rem;
  align-items: center;
}

.stats-card i {
  width: 3rem;
  height: 3rem;
  border-radius: 1rem;
  display: grid;
  place-items: center;
  color: #fff;
  font-size: 1.1rem;
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
}

.stats-card:nth-child(2) i {
  background: linear-gradient(135deg, #16a34a, #15803d);
}

.stats-card:nth-child(3) i {
  background: linear-gradient(135deg, #ef4444, #dc2626);
}

.stats-card:nth-child(4) i {
  background: linear-gradient(135deg, #7c3aed, #6d28d9);
}

.stats-card span {
  color: #64748b;
  font-size: .88rem;
}

.stats-card strong {
  display: block;
  margin-top: .2rem;
  color: #0f172a;
  font-size: 1.4rem;
}

.card,
.modal-card {
  padding: 1.2rem;
}

.search-box {
  flex: 1;
  display: flex;
  align-items: center;
  gap: .75rem;
  min-height: 3rem;
  padding: 0 1rem;
  border: 1px solid #dbe4f0;
  border-radius: .95rem;
  background: #fff;
  color: #64748b;
}

.search-box input {
  width: 100%;
  border: none;
  outline: none;
  background: transparent;
  color: #0f172a;
  font-size: .96rem;
}

.btn,
.icon-btn {
  border: none;
  cursor: pointer;
  transition: .2s ease;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: .65rem;
  min-height: 3rem;
  padding: 0 1.1rem;
  border-radius: .95rem;
  font-weight: 800;
}

.btn-small {
  min-height: 2.75rem;
}

.btn-primary {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  color: #fff;
  box-shadow: 0 12px 24px rgba(37, 99, 235, .22);
}

.btn-ghost {
  border: 1px solid #dbe4f0;
  background: rgba(255, 255, 255, .88);
  color: #334155;
}

.btn:disabled,
.icon-btn:disabled {
  opacity: .65;
  cursor: not-allowed;
  box-shadow: none;
}

.table-wrap {
  overflow-x: auto;
}

.admin-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 980px;
}

.admin-table th,
.admin-table td {
  padding: 1rem .85rem;
  border-bottom: 1px solid #eef2f7;
  vertical-align: middle;
  text-align: left;
}

.admin-table th {
  color: #64748b;
  font-size: .8rem;
  text-transform: uppercase;
  letter-spacing: .08em;
}

.admin-table td strong,
.admin-table td small {
  display: block;
}

.admin-table td strong {
  color: #0f172a;
  font-size: .97rem;
}

.admin-table td small {
  margin-top: .25rem;
  color: #64748b;
  font-size: .86rem;
}

.text-center {
  text-align: center !important;
}

.justify-center {
  justify-content: center;
}

.actions-inline {
  display: flex;
  width: 100%;
  gap: .45rem;
  justify-content: center;
  align-items: center;
  flex-wrap: nowrap;
}

.icon-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: .85rem;
  background: #f8fafc;
  color: #334155;
  border: 1px solid #e2e8f0;
}

.icon-btn-danger {
  color: #b91c1c;
  background: rgba(254, 242, 242, .96);
  border-color: rgba(252, 165, 165, .55);
}

.pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 1.9rem;
  padding: 0 .8rem;
  border-radius: 999px;
  font-size: .78rem;
  font-weight: 800;
}

.pill-success {
  background: rgba(34, 197, 94, .14);
  color: #15803d;
}

.pill-danger {
  background: rgba(239, 68, 68, .12);
  color: #b91c1c;
}

.empty-state {
  min-height: 240px;
  display: grid;
  place-items: center;
  gap: .65rem;
  text-align: center;
  border: 1px dashed #dbe4f0;
  border-radius: 1rem;
  color: #64748b;
}

.empty-state i {
  font-size: 1.4rem;
  color: #94a3b8;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  padding: 1rem;
  background: rgba(15, 23, 42, .52);
  display: grid;
  place-items: center;
}

.modal-card {
  width: min(100%, 760px);
}

.form-grid {
  margin: 1rem 0 1.25rem;
}

.form-grid-2 {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.field {
  display: grid;
  gap: .5rem;
}

.field-full {
  grid-column: 1 / -1;
}

.field span {
  color: #334155;
  font-weight: 700;
}

.field input,
.field select {
  width: 100%;
  min-height: 3rem;
  padding: .85rem 1rem;
  border-radius: .95rem;
  border: 1px solid #dbe4f0;
  background: #fff;
  color: #0f172a;
  outline: none;
}

@media (max-width: 1200px) {
  .stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 768px) {
  .admin-account-page {
    padding: 1rem;
  }

  .hero,
  .hero-actions,
  .toolbar,
  .card-head,
  .modal-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .form-grid-2,
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>
