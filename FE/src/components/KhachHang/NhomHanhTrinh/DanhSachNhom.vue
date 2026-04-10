<template>
  <div class="group-page">
    <div class="group-layout">
      <CustomerSidebar />

      <section class="group-content">
        <header class="group-hero">
          <div>
            <p class="group-hero__eyebrow">Không gian nhóm cá nhân</p>
            <h1>Danh sách nhóm hành trình</h1>
            <p class="group-hero__subtitle">
              Quản lý các nhóm mà bạn đang tham gia, cập nhật tên nhóm và điều hướng nhanh đến màn quản lý thành viên.
            </p>
          </div>

          <div class="group-hero__actions">
            <button class="primary-link" type="button" @click="openCreateDialog">
              <i class="fas fa-plus"></i>
              <span>Tạo nhóm mới</span>
            </button>
            <button class="ghost-button" type="button" :disabled="dangTai" @click="taiNhom">
              <i class="fas fa-rotate-right"></i>
              <span>{{ dangTai ? "Đang tải..." : "Tải lại" }}</span>
            </button>
          </div>
        </header>

        <section class="stats-row">
          <article class="stats-tile">
            <span>Tổng nhóm</span>
            <strong>{{ totalGroups }}</strong>
          </article>
          <article class="stats-tile">
            <span>Vai trò nhóm trưởng</span>
            <strong>{{ soNhomTruongNhom }}</strong>
          </article>
          <article class="stats-tile">
            <span>Tổng thành viên</span>
            <strong>{{ totalMembers }}</strong>
          </article>
        </section>

        <section class="toolbar">
          <label class="toolbar__search">
            <i class="fas fa-search"></i>
            <input
              v-model.trim="search"
              type="text"
              placeholder="Tìm theo mã nhóm, tên nhóm..."
            >
          </label>
        </section>

        <div v-if="thongBaoLoi" class="notice notice--error">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ thongBaoLoi }}</span>
        </div>

        <div v-if="thongBaoThanhCong" class="notice notice--success">
          <i class="fas fa-circle-check"></i>
          <span>{{ thongBaoThanhCong }}</span>
        </div>

        <section v-if="dangTai" class="loading-grid">
          <div class="loading-card"></div>
          <div class="loading-card"></div>
          <div class="loading-card"></div>
        </section>

        <section v-else-if="filteredGroups.length" class="group-table-wrap">
          <table class="group-table">
            <thead>
              <tr>
                <th>Nhóm</th>
                <th>Mã nhóm</th>
                <th>Vai trò</th>
                <th>Thành viên</th>
                <th>Tạo lúc</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="group in filteredGroups" :key="group.maThanhVienNhom">
                <td>
                  <strong>{{ group.tenNhom }}</strong>
                </td>
                <td>{{ group.maNhom }}</td>
                <td>
                  <span class="role-badge" :class="group.roleValue === 1 ? 'is-leader' : 'is-member'">
                    {{ group.roleLabel }}
                  </span>
                </td>
                <td>{{ group.soLuongThanhVien }}</td>
                <td>{{ group.createdAtLabel }}</td>
                <td>
                  <div class="row-actions">
                    <router-link
                      class="row-action"
                      :to="`/khach-hang/nhom-hanh-trinh/${group.maNhom}/members`"
                      title="Quản lý thành viên"
                    >
                      <i class="fas fa-users-gear"></i>
                    </router-link>
                    <button class="row-action" type="button" title="Đổi tên nhóm" @click="openEditDialog(group)">
                      <i class="fas fa-pen"></i>
                    </button>
                    <button class="row-action row-action--danger" type="button" title="Xóa nhóm" @click="openDeleteDialog(group)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </section>

        <section v-else class="empty-state">
          <i class="fas fa-users"></i>
          <h2>Chưa có nhóm hành trình phù hợp</h2>
          <p>{{ emptyMessage }}</p>
          <button class="primary-link" type="button" @click="openCreateDialog">
            Tạo nhóm đầu tiên
          </button>
        </section>
      </section>
    </div>

    <div v-if="createDialogOpen" class="dialog-backdrop" @click.self="closeDialogs">
      <section class="dialog-card">
        <p class="dialog-card__eyebrow">Tạo nhóm mới</p>
        <h2>Khởi tạo nhóm hành trình</h2>
        <label class="field">
          <span>Tên nhóm</span>
          <input v-model.trim="form.ten_nhom" type="text" placeholder="Nhập tên nhóm hành trình">
          <small v-if="errors.ten_nhom">{{ errors.ten_nhom }}</small>
        </label>
        <div class="dialog-card__actions">
          <button class="ghost-button" type="button" :disabled="isSubmitting" @click="closeDialogs">Hủy</button>
          <button class="primary-link" type="button" :disabled="isSubmitting" @click="createGroup">
            <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
            <span>{{ isSubmitting ? "Đang tạo..." : "Xác nhận tạo" }}</span>
          </button>
        </div>
      </section>
    </div>

    <div v-if="editDialogOpen" class="dialog-backdrop" @click.self="closeDialogs">
      <section class="dialog-card">
        <p class="dialog-card__eyebrow">Cập nhật nhóm</p>
        <h2>Đổi tên nhóm {{ nhomDangChon?.maNhom }}</h2>
        <label class="field">
          <span>Tên nhóm</span>
          <input v-model.trim="form.ten_nhom" type="text" placeholder="Nhập tên nhóm mới">
          <small v-if="errors.ten_nhom">{{ errors.ten_nhom }}</small>
        </label>
        <div class="dialog-card__actions">
          <button class="ghost-button" type="button" :disabled="isSubmitting" @click="closeDialogs">Hủy</button>
          <button class="primary-link" type="button" :disabled="isSubmitting" @click="updateGroup">
            <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
            <span>{{ isSubmitting ? "Đang lưu..." : "Lưu thay đổi" }}</span>
          </button>
        </div>
      </section>
    </div>

    <div v-if="deleteDialogOpen" class="dialog-backdrop" @click.self="closeDialogs">
      <section class="dialog-card dialog-card--danger">
        <p class="dialog-card__eyebrow">Xác nhận xóa nhóm</p>
        <h2>Xóa nhóm {{ nhomDangChon?.tenNhom }}?</h2>
        <p class="dialog-card__text">
          Thao tác này sẽ xóa nhóm khỏi hệ thống. Vui lòng xác nhận để tiếp tục.
        </p>
        <div class="dialog-card__actions">
          <button class="ghost-button" type="button" :disabled="isSubmitting" @click="closeDialogs">Hủy</button>
          <button class="danger-btn" type="button" :disabled="isSubmitting" @click="deleteGroup">
            <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
            <span>{{ isSubmitting ? "Đang xóa..." : "Xóa nhóm" }}</span>
          </button>
        </div>
      </section>
    </div>
  </div>

</template>

<script>
import CustomerSidebar from '../CustomerSidebar.vue';
import { goiApi } from '../../../services/httpClient.js';
import {
  API_BASE,
  buildHeaders,
  createInitials,
  formatDateDisplay,
  getStoredCustomerId,
  getStoredUser,
  chuanHoaDanhSach,
} from "../../Shared/customerSession";

function normalizeMembershipRow(row) {
  return {
    maThanhVienNhom: String(row?.Ma_thanh_vien || row?.ma_thanh_vien || ""),
    maNhom: String(row?.Ma_nhom || row?.ma_nhom || row?.nhom?.Ma_nhom || ""),
    tenNhom: row?.nhom?.ten_nhom || row?.ten_nhom || "Nhóm chưa xác định",
    roleValue: Number(row?.vai_tro ?? 0),
    roleLabel: Number(row?.vai_tro ?? 0) === 1 ? "Nhóm trưởng" : "Thành viên",
    soLuongThanhVien: 0,
    createdAtLabel: formatDateDisplay(row?.created_at, true),
  };
}
export default {
  name: "DanhSachNhóm",
  components: {
    CustomerSidebar,
  },
  data() {
    return {
      dangTai: false,
      isSubmitting: false,
      thongBaoLoi: "",
      thongBaoThanhCong: "",
      maKhachHang: "",
      groups: [],
      search: "",
      createDialogOpen: false,
      editDialogOpen: false,
      deleteDialogOpen: false,
      nhomDangChon: null,
      form: {
        ten_nhom: "",
      },
      errors: {
        ten_nhom: "",
      },
    };
  },
  computed: {
    hoSoKhachHang() {
      return getStoredUser() || {};
    },
    filteredGroups() {
      const keyword = String(this.search || "").toLowerCase();
      if (!keyword) return this.groups;

      return this.groups.filter((group) => {
        const haystack = [group.maNhom, group.tenNhom, group.roleLabel].join(" ").toLowerCase();
        return haystack.includes(keyword);
      });
    },
    totalGroups() {
      return this.groups.length;
    },
    soNhomTruongNhom() {
      return this.groups.filter((group) => group.roleValue === 1).length;
    },
    totalMembers() {
      return this.groups.reduce((sum, group) => sum + Number(group.soLuongThanhVien || 0), 0);
    },
    emptyMessage() {
      if (!this.maKhachHang) {
        return "Bạn cần đăng nhập tài khoản khách hàng để đồng bộ danh sách nhóm hành trình.";
      }

      if (this.search) {
        return "Không tìm thấy nhóm nào khớp với bộ lọc hiện tại.";
      }

      return "Tài khoản hiện tại chưa có nhóm hành trình nào. Bạn có thể tạo nhóm mới ngay bên dưới.";
    },
  },
  methods: {
    clearMessages() {
      this.thongBaoLoi = "";
      this.thongBaoThanhCong = "";
    },
    resetForm() {
      this.form = { ten_nhom: "" };
      this.errors = { ten_nhom: "" };
    },
    validateForm() {
      this.errors = { ten_nhom: "" };
      if (!String(this.form.ten_nhom || "").trim()) {
        this.errors.ten_nhom = "Tên nhóm là bắt buộc.";
      } else if (String(this.form.ten_nhom).trim().length > 100) {
        this.errors.ten_nhom = "Tên nhóm không được vượt quá 100 ký tự.";
      }
      return !this.errors.ten_nhom;
    },
    async taiSoLuongThanhVien(maNhom) {
      try {
        const phanHoi = await goiApi(`${API_BASE}/thanh-vien-nhom/nhom/${encodeURIComponent(maNhom)}`, {
          headers: buildHeaders(),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok) {
          return 0;
        }

        return chuanHoaDanhSach(duLieuPhanHoi).length;
      } catch {
        return 0;
      }
    },
    async taiNhom() {
      this.dangTai = true;
      this.clearMessages();
      this.maKhachHang = getStoredCustomerId();

      if (!this.maKhachHang) {
        this.groups = [];
        this.dangTai = false;
        this.thongBaoLoi = "Không tìm thấy mã khách hàng trong phiên đăng nhập hiện tại.";
        return;
      }

      try {
        const phanHoi = await goiApi(
          `${API_BASE}/thanh-vien-nhom/search?Ma_khach_hang=${encodeURIComponent(this.maKhachHang)}`,
          { headers: buildHeaders() },
        );
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok) {
          if (phanHoi.status === 404) {
            this.groups = [];
            return;
          }
          throw new Error(duLieuPhanHoi?.message || "Không thể tải danh sách nhóm.");
        }

        const rows = chuanHoaDanhSach(duLieuPhanHoi).map((row) => normalizeMembershipRow(row));
        const uniqueByGroup = new Map();

        rows.forEach((item) => {
          if (!uniqueByGroup.has(item.maNhom)) {
            uniqueByGroup.set(item.maNhom, item);
          }
        });

        const baseGroups = Array.from(uniqueByGroup.values());
        const withCounts = await Promise.all(
          baseGroups.map(async (group) => ({
            ...group,
            soLuongThanhVien: await this.taiSoLuongThanhVien(group.maNhom),
          })),
        );

        this.groups = withCounts.sort((a, b) => a.tenNhom.localeCompare(b.tenNhom, "vi"));
      } catch (error) {
        this.groups = [];
        this.thongBaoLoi = error.message || "Không thể tải danh sách nhóm.";
      } finally {
        this.dangTai = false;
      }
    },
    openCreateDialog() {
      this.resetForm();
      this.nhomDangChon = null;
      this.clearMessages();
      this.createDialogOpen = true;
    },
    openEditDialog(group) {
      this.resetForm();
      this.nhomDangChon = group;
      this.form.ten_nhom = group.tenNhom;
      this.clearMessages();
      this.editDialogOpen = true;
    },
    openDeleteDialog(group) {
      this.nhomDangChon = group;
      this.clearMessages();
      this.deleteDialogOpen = true;
    },
    closeDialogs(force = false) {
      if (this.isSubmitting && !force) return;
      this.createDialogOpen = false;
      this.editDialogOpen = false;
      this.deleteDialogOpen = false;
      this.nhomDangChon = null;
    },
    async createGroup() {
      if (!this.validateForm()) return;

      this.isSubmitting = true;
      this.clearMessages();

      let maNhomMoiTao = "";

      try {
        const phanHoiTaoMoi = await goiApi(`${API_BASE}/nhom`, {
          method: "POST",
          headers: buildHeaders(true),
          body: JSON.stringify({
            ten_nhom: this.form.ten_nhom.trim(),
          }),
        });
        const duLieuTaoMoi = await phanHoiTaoMoi.json().catch(() => ({}));

        if (!phanHoiTaoMoi.ok) {
          throw new Error(duLieuTaoMoi?.message || "Không thể tạo nhóm mới.");
        }

        maNhomMoiTao = String(
          duLieuTaoMoi?.data?.Ma_nhom ||
          duLieuTaoMoi?.data?.ma_nhom ||
          "",
        ).trim();

        if (!maNhomMoiTao) {
          throw new Error("Tạo nhóm thành công nhưng không nhận được mã nhóm.");
        }

        const phanHoiThanhVien = await goiApi(`${API_BASE}/thanh-vien-nhom`, {
          method: "POST",
          headers: buildHeaders(true),
          body: JSON.stringify({
            Ma_nhom: maNhomMoiTao,
            Ma_khach_hang: this.maKhachHang,
            vai_tro: 1,
          }),
        });
        const duLieuThanhVien = await phanHoiThanhVien.json().catch(() => ({}));

        if (!phanHoiThanhVien.ok) {
          try {
            await goiApi(`${API_BASE}/nhom/${encodeURIComponent(maNhomMoiTao)}`, {
              method: "DELETE",
              headers: buildHeaders(),
            });
          } catch {
            // Ignore rollback error but keep original context in UI.
          }

          throw new Error(duLieuThanhVien?.message || "Tạo nhóm thành công nhưng không thể gắn nhóm cho tài khoản hiện tại.");
        }

        this.thongBaoThanhCong = "Đã tạo nhóm mới thành công.";
        this.closeDialogs(true);
        await this.taiNhom();
      } catch (error) {
        this.thongBaoLoi = error.message || "Không thể tạo nhóm mới.";
      } finally {
        this.isSubmitting = false;
      }
    },
    async updateGroup() {
      if (!this.validateForm()) return;
      if (!this.nhomDangChon?.maNhom) return;

      this.isSubmitting = true;
      this.clearMessages();

      try {
        const phanHoi = await goiApi(`${API_BASE}/nhom/${encodeURIComponent(this.nhomDangChon.maNhom)}`, {
          method: "PUT",
          headers: buildHeaders(true),
          body: JSON.stringify({
            ten_nhom: this.form.ten_nhom.trim(),
          }),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể cập nhật nhóm.");
        }

        this.thongBaoThanhCong = `Đã cập nhật nhóm ${this.nhomDangChon.maNhom} thành công.`;
        this.closeDialogs(true);
        await this.taiNhom();
      } catch (error) {
        this.thongBaoLoi = error.message || "Không thể cập nhật nhóm.";
      } finally {
        this.isSubmitting = false;
      }
    },
    async deleteGroup() {
      if (!this.nhomDangChon?.maNhom) return;

      this.isSubmitting = true;
      this.clearMessages();

      try {
        const phanHoi = await goiApi(`${API_BASE}/nhom/${encodeURIComponent(this.nhomDangChon.maNhom)}`, {
          method: "DELETE",
          headers: buildHeaders(),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể xóa nhóm.");
        }

        this.thongBaoThanhCong = `Đã xóa nhóm ${this.nhomDangChon.maNhom} thành công.`;
        this.closeDialogs(true);
        await this.taiNhom();
      } catch (error) {
        this.thongBaoLoi = error.message || "Không thể xóa nhóm.";
      } finally {
        this.isSubmitting = false;
      }
    },
  },
  mounted() {
    this.taiNhom();
  },
};
</script>

<style scoped>
.group-page {
  background:
    radial-gradient(circle at top right, rgba(12, 104, 150, 0.08), transparent 26%),
    linear-gradient(180deg, #eef5ff 0%, #f5f8ff 100%);
}

.group-layout {
  width: min(1440px, calc(100% - 24px));
  margin: 0 auto;
  padding: 0 0 40px;
  display: grid;
  grid-template-columns: 264px minmax(0, 1fr);
  gap: 18px;
  min-height: calc(100vh - 68px);
}

.group-content {
  padding: 28px 6px 0 0;
}

.group-hero {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  align-items: flex-start;
  gap: 18px;
  margin-bottom: 24px;
}

.group-hero__eyebrow {
  margin: 0 0 10px;
  color: #4f25f4;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.82rem;
  font-weight: 900;
}

.group-hero h1 {
  margin: 0;
  color: #111827;
  font-size: clamp(2.1rem, 3.3vw, 3rem);
  line-height: 1.06;
  font-weight: 900;
  letter-spacing: -0.05em;
  max-width: 720px;
}

.group-hero__subtitle {
  margin: 10px 0 0;
  color: #5f6f89;
  max-width: 640px;
  font-size: 1rem;
  line-height: 1.7;
}

.group-hero__actions {
  display: flex;
  align-items: center;
  gap: 12px;
  padding-top: 8px;
}

.primary-link,
.ghost-button,
.danger-btn {
  min-height: 48px;
  padding: 0 22px;
  border-radius: 18px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  text-decoration: none;
  font-weight: 800;
}

.primary-link {
  border: 0;
  background: linear-gradient(135deg, #0b7198 0%, #0b63a0 100%);
  color: #ffffff;
  box-shadow: 0 16px 28px rgba(11, 99, 160, 0.22);
}

.ghost-button {
  border: 1px solid #d7e2f0;
  background: rgba(255, 255, 255, 0.82);
  color: #24415d;
}

.danger-btn {
  border: 0;
  background: linear-gradient(135deg, #dc2626 0%, #be123c 100%);
  color: #ffffff;
}

.stats-row {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
  margin-bottom: 20px;
}

.stats-tile,
.toolbar,
.group-table-wrap,
.empty-state,
.notice,
.loading-card,
.dialog-card {
  border-radius: 24px;
  border: 1px solid #e2eaf5;
  background: rgba(255, 255, 255, 0.9);
  box-shadow: 0 16px 34px rgba(31, 65, 114, 0.06);
}

.stats-tile {
  padding: 22px;
}

.stats-tile span {
  color: #617891;
  font-weight: 700;
}

.stats-tile strong {
  display: block;
  margin-top: 10px;
  color: #0d2f57;
  font-size: 1.95rem;
  line-height: 1;
  font-weight: 900;
}

.toolbar {
  padding: 18px;
  margin-bottom: 18px;
}

.toolbar__search {
  min-height: 54px;
  padding: 0 18px;
  border-radius: 18px;
  border: 1px solid #dfe7f3;
  background: #f8fbff;
  display: flex;
  align-items: center;
  gap: 12px;
}

.toolbar__search input {
  border: 0;
  outline: none;
  background: transparent;
  color: #24415d;
  width: 100%;
}

.notice {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 18px;
  margin-bottom: 18px;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
}

.notice--success {
  background: #ecfdf5;
  color: #047857;
}

.loading-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
}

.loading-card {
  min-height: 220px;
}

.group-table-wrap {
  overflow: hidden;
}

.group-table {
  width: 100%;
  border-collapse: collapse;
}

.group-table th,
.group-table td {
  padding: 14px 16px;
  text-align: left;
  border-bottom: 1px solid #e6edf8;
  color: #2c405e;
}

.group-table th {
  font-size: 0.82rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #6c7e95;
}

.group-table tbody tr:last-child td {
  border-bottom: 0;
}

.role-badge {
  min-height: 30px;
  padding: 0 12px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  font-size: 0.8rem;
  font-weight: 700;
}

.role-badge.is-leader {
  background: #ecfdf5;
  color: #047857;
}

.role-badge.is-member {
  background: #eff6ff;
  color: #1d4ed8;
}

.row-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.row-action {
  width: 34px;
  height: 34px;
  border: 0;
  border-radius: 10px;
  background: #eff6ff;
  color: #2f5f9d;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
}

.row-action--danger {
  background: #fff1f2;
  color: #be123c;
}

.empty-state {
  min-height: 260px;
  display: grid;
  place-items: center;
  text-align: center;
  gap: 10px;
  padding: 28px;
}

.empty-state i {
  width: 72px;
  height: 72px;
  border-radius: 22px;
  display: grid;
  place-items: center;
  background: #e7f2ff;
  color: #0b63a0;
  font-size: 1.5rem;
}

.empty-state p {
  max-width: 560px;
  margin: 0;
  color: #617891;
  line-height: 1.7;
}

.dialog-backdrop {
  position: fixed;
  inset: 0;
  z-index: 50;
  background: rgba(15, 23, 42, 0.5);
  backdrop-filter: blur(8px);
  display: grid;
  place-items: center;
  padding: 20px;
}

.dialog-card {
  width: min(520px, 100%);
  padding: 24px;
}

.dialog-card__eyebrow {
  margin: 0;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  font-size: 0.76rem;
  font-weight: 900;
}

.dialog-card h2 {
  margin: 10px 0 0;
  color: #111827;
  font-weight: 900;
}

.dialog-card__text {
  margin: 10px 0 0;
  color: #64748b;
  line-height: 1.6;
}

.field {
  display: grid;
  gap: 8px;
  margin-top: 18px;
}

.field span {
  color: #1f2937;
  font-weight: 700;
}

.field input {
  min-height: 48px;
  border-radius: 14px;
  border: 1px solid #dbe4f0;
  padding: 0 14px;
  background: #f8faff;
  outline: none;
}

.field small {
  color: #dc2626;
}

.dialog-card__actions {
  margin-top: 20px;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

@media (max-width: 1200px) {
  .group-layout {
    grid-template-columns: 1fr;
  }

  .profile-panel {
    border-right: 0;
    border-bottom: 1px solid #e4ecf8;
  }

  .profile-panel__menu {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 860px) {
  .group-layout {
    width: calc(100% - 20px);
  }

  .group-hero {
    grid-template-columns: 1fr;
  }

  .group-hero__actions {
    width: 100%;
    flex-wrap: wrap;
  }

  .stats-row,
  .loading-grid,
  .profile-panel__menu {
    grid-template-columns: 1fr;
  }

  .group-table-wrap {
    overflow-x: auto;
  }
}
</style>






