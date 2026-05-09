<template>
  <PartnerShell
    title="Quản lý tour"
    subtitle="Tạo, chỉnh sửa, xóa và gửi duyệt tour thuộc tài khoản đối tác của bạn."
    :partner="partner"
    :loading="loading"
  >
    <template #headerActions>
      <button class="toolbar-btn" type="button" @click="openCreateForm">
        <i class="fas fa-plus"></i>
        Tạo tour mới
      </button>
      <button class="toolbar-btn toolbar-btn--ghost" type="button" @click="loadPage">
        <i class="fas fa-rotate"></i>
        Làm mới
      </button>
    </template>

    <section class="filters">
      <label class="field">
        <span>Tìm kiếm</span>
        <input v-model.trim="filters.keyword" type="text" placeholder="Tên tour hoặc mã tour">
      </label>
      <label class="field">
        <span>Trạng thái duyệt</span>
        <select v-model="filters.status">
          <option value="">Tất cả</option>
          <option value="draft">Bản nháp</option>
          <option value="pending_approval">Chờ duyệt</option>
          <option value="approved">Đã duyệt</option>
          <option value="rejected">Từ chối</option>
        </select>
      </label>
    </section>

    <section v-if="errorMessage" class="alert-box">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ errorMessage }}</span>
    </section>

    <section class="table-card">
      <table v-if="filteredTours.length" class="table">
        <thead>
          <tr>
            <th>Mã tour</th>
            <th>Tên tour</th>
            <th>Giá</th>
            <th>Trạng thái duyệt</th>
            <th>Hiển thị</th>
            <th>Cập nhật</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tour in filteredTours" :key="tour.ma_tour">
            <td>{{ tour.ma_tour }}</td>
            <td>
              <strong>{{ tour.ten_tour || "-" }}</strong>
              <small v-if="tour.ly_do_tu_choi" class="reject-note">{{ tour.ly_do_tu_choi }}</small>
            </td>
            <td>{{ formatCurrency(tour.so_tien) }}</td>
            <td>
              <span :class="['badge', `badge--${mapStatusTone(tour.trang_thai_duyet)}`]">
                {{ mapStatusLabel(tour.trang_thai_duyet) }}
              </span>
            </td>
            <td>{{ tour.trang_thai_hien_thi ? "Đang hiển thị" : "Đang ẩn" }}</td>
            <td>{{ formatDateTime(tour.updated_at || tour.created_at) }}</td>
            <td class="actions">
              <button type="button" @click="openEditForm(tour)">Sửa</button>
              <button
                type="button"
                class="action-danger"
                :disabled="deletingTourId === tour.ma_tour"
                @click="deleteTour(tour)"
              >
                {{ deletingTourId === tour.ma_tour ? "Đang xóa..." : "Xóa" }}
              </button>
              <button
                v-if="canSubmit(tour)"
                type="button"
                class="action-primary"
                :disabled="submittingTourId === tour.ma_tour"
                @click="submitTour(tour)"
              >
                {{ submittingTourId === tour.ma_tour ? "Đang gửi..." : "Gửi duyệt" }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else class="empty-box">Không có tour phù hợp bộ lọc.</div>
    </section>

    <div v-if="showForm" class="modal-overlay" @click.self="closeForm">
      <form class="partner-modal" @submit.prevent="saveForm">
        <header class="partner-modal__head">
          <h3>{{ editingTourId ? "Chỉnh sửa tour" : "Tạo tour mới" }}</h3>
          <button type="button" @click="closeForm">
            <i class="fas fa-xmark"></i>
          </button>
        </header>

        <section class="partner-modal__body">
          <label class="field">
            <span>Tên tour *</span>
            <input v-model.trim="form.ten_tour" type="text" required>
          </label>
          <label class="field">
            <span>Mô tả</span>
            <textarea v-model.trim="form.mo_ta" rows="3"></textarea>
          </label>
          <div class="field-grid">
            <label class="field">
              <span>Số ngày</span>
              <input v-model.number="form.so_ngay" type="number" min="1">
            </label>
            <label class="field">
              <span>Số người</span>
              <input v-model.number="form.so_nguoi" type="number" min="1">
            </label>
            <label class="field">
              <span>Mã tag</span>
              <input v-model.trim="form.ma_tag" type="text">
            </label>
          </div>
          <label class="field">
            <span>Hình ảnh (URL)</span>
            <input v-model.trim="form.hinh_anh" type="text">
          </label>
        </section>

        <p v-if="formError" class="form-error">{{ formError }}</p>

        <footer class="partner-modal__foot">
          <button type="button" class="action-secondary" @click="closeForm">Hủy</button>
          <button type="submit" class="action-primary" :disabled="saving">
            {{ saving ? "Đang lưu..." : "Lưu tour" }}
          </button>
        </footer>
      </form>
    </div>
  </PartnerShell>
</template>

<script>
import PartnerShell from "./layout/PartnerShell.vue";
import { showConfirm } from "../../services/appDialog";
import { toastError, toastSuccess } from "./shared/partnerToast";
import {
  createPartnerTour,
  deletePartnerTour,
  fetchPartnerSession,
  fetchPartnerTours,
  formatCurrency,
  formatDateTime,
  mapStatusLabel,
  mapStatusTone,
  submitPartnerTour,
  updatePartnerTour,
} from "./shared/partnerApi";

function newTourForm() {
  return {
    ten_tour: "",
    mo_ta: "",
    hinh_anh: "",
    so_ngay: 2,
    so_nguoi: 10,
    ma_tag: "",
  };
}

import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({ position: 'top-right' });

export default {
  name: "PartnerQuanLyTour",
  components: {
    PartnerShell,
  },
  data() {
    return {
      loading: false,
      saving: false,
      submittingTourId: "",
      deletingTourId: "",
      partner: null,
      tours: [],
      filters: {
        keyword: "",
        status: "",
      },
      showForm: false,
      editingTourId: "",
      form: newTourForm(),
      errorMessage: "",
      formError: "",
    };
  },
  computed: {
    filteredTours() {
      const keyword = this.filters.keyword.toLowerCase();
      return [...this.tours]
        .filter((tour) => {
          const statusMatch = !this.filters.status
            || String(tour?.trang_thai_duyet || "").toLowerCase() === this.filters.status;
          if (!statusMatch) return false;

          if (!keyword) return true;
          return (
            String(tour?.ma_tour || "").toLowerCase().includes(keyword)
            || String(tour?.ten_tour || "").toLowerCase().includes(keyword)
            || String(tour?.ly_do_tu_choi || "").toLowerCase().includes(keyword)
          );
        })
        .sort((a, b) => {
          const ad = new Date(a?.updated_at || a?.created_at || 0).getTime();
          const bd = new Date(b?.updated_at || b?.created_at || 0).getTime();
          return bd - ad;
        });
    },
  },
  methods: {
    formatCurrency,
    formatDateTime,
    mapStatusLabel,
    mapStatusTone,
    canSubmit(tour) {
      const status = String(tour?.trang_thai_duyet || "").toLowerCase();
      return status === "draft" || status === "rejected";
    },
    resetMessages() {
      // cleared errorMessage
      this.formError = "";
    },
    async loadPage() {
      this.loading = true;
      this.resetMessages();
      try {
        const [partner, tours] = await Promise.all([
          fetchPartnerSession(),
          fetchPartnerTours(),
        ]);
        this.partner = partner;
        this.tours = tours;
      } catch (error) {
        toaster.error(error?.message || "Không thể tải dữ liệu quản lý tour.");
      } finally {
        this.loading = false;
      }
    },
    openCreateForm() {
      this.editingTourId = "";
      this.form = newTourForm();
      this.formError = "";
      this.showForm = true;
    },
    openEditForm(tour) {
      this.editingTourId = tour?.ma_tour || "";
      this.form = {
        ten_tour: tour?.ten_tour || "",
        mo_ta: tour?.mo_ta || "",
        hinh_anh: tour?.hinh_anh || "",
        so_ngay: tour?.so_ngay ? Number(tour.so_ngay) : null,
        so_nguoi: tour?.so_nguoi ? Number(tour.so_nguoi) : null,
        ma_tag: tour?.ma_tag || "",
      };
      this.formError = "";
      this.showForm = true;
    },
    closeForm() {
      this.showForm = false;
      this.formError = "";
    },
    normalizePayload() {
      return {
        ten_tour: this.form.ten_tour,
        mo_ta: this.form.mo_ta || null,
        hinh_anh: this.form.hinh_anh || null,
        so_ngay: this.form.so_ngay || null,
        so_nguoi: this.form.so_nguoi || null,
        ma_tag: this.form.ma_tag || null,
      };
    },
    async saveForm() {
      if (!this.form.ten_tour) {
        this.formError = "Vui lòng nhập tên tour.";
        return;
      }

      this.saving = true;
      this.formError = "";
      // cleared errorMessage

      try {
        const payload = this.normalizePayload();
        if (this.editingTourId) {
          await updatePartnerTour(this.editingTourId, payload);
          toastSuccess("Đã cập nhật tour. Tour đã chuyển về trạng thái bản nháp.");
        } else {
          await createPartnerTour(payload);
          toastSuccess("Đã tạo tour mới thành công.");
        }

        this.closeForm();
        await this.loadPage();
      } catch (error) {
        this.formError = error?.message || "Không thể lưu thông tin tour.";
        toastError(this.formError);
      } finally {
        this.saving = false;
      }
    },
    async submitTour(tour) {
      if (!tour?.ma_tour) return;

      const confirmed = await showConfirm({
        title: "Xác nhận gửi duyệt tour",
        message: `Bạn có chắc muốn gửi duyệt tour "${tour.ten_tour || tour.ma_tour}"?`,
        tone: "warning",
        confirmText: "Gửi duyệt",
        cancelText: "Hủy",
      });
      if (!confirmed) return;

      this.submittingTourId = tour.ma_tour;
      // cleared errorMessage

      try {
        await submitPartnerTour(tour.ma_tour);
        toaster.success("Tour đã được gửi duyệt thành công.");
        await this.loadPage();
      } catch (error) {
        toaster.error(error?.message || "Không thể gửi duyệt tour.");
      } finally {
        this.submittingTourId = "";
      }
    },
    async deleteTour(tour) {
      if (!tour?.ma_tour) return;

      const confirmed = await showConfirm({
        title: "Xác nhận xóa tour",
        message: `Bạn có chắc muốn xóa tour "${tour.ten_tour || tour.ma_tour}"?`,
        tone: "danger",
        confirmText: "Xóa tour",
        cancelText: "Hủy",
      });
      if (!confirmed) return;

      this.deletingTourId = tour.ma_tour;
      // cleared errorMessage

      try {
        await deletePartnerTour(tour.ma_tour);
        toaster.success("Tour đã được xóa thành công.");
        await this.loadPage();
      } catch (error) {
        toaster.error(error?.message || "Không thể xóa tour.");
      } finally {
        this.deletingTourId = "";
      }
    },
  },
  mounted() {
    this.loadPage();
  },
};
</script>

<style scoped>
.toolbar-btn {
  min-height: 2.75rem;
  border: 0;
  border-radius: 0.75rem;
  background: #1d4ed8;
  color: #fff;
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
  background: #1e40af;
  transform: translateY(-1px);
}

.toolbar-btn--ghost {
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
}

.toolbar-btn--ghost:hover {
  background: #dbeafe;
}

.filters {
  display: grid;
  grid-template-columns: minmax(280px, 1.5fr) repeat(1, minmax(180px, 1fr));
  gap: 0.75rem;
  margin-bottom: 0.5rem;
  align-items: end;
}

.field {
  display: grid;
  gap: 0.5rem;
}

.field span {
  font-weight: 700;
  color: #475569;
  font-size: 0.875rem;
}

.field input,
.field select,
.field textarea {
  min-height: 2.75rem;
  border-radius: 0.75rem;
  border: 1px solid #e2e8f0;
  background: #f8fbff;
  padding: 0 1rem;
  color: #1e293b;
  font-size: 0.875rem;
  transition: border-color 0.2s, outline 0.2s;
}

.field input:focus,
.field select:focus,
.field textarea:focus {
  outline: 2px solid rgba(37, 99, 235, 0.2);
  border-color: #2563eb;
}

.alert-box {
  margin: 0.5rem 0;
  padding: 1rem 1.5rem;
  border-radius: 1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  border: 1px solid #fecaca;
  background: #fef2f2;
  color: #b91c1c;
  font-weight: 500;
}

.table-card {
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  background: #ffffff;
  overflow: auto;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table th,
.table td {
  padding: 1.25rem 1rem;
  border-bottom: 1px solid #f1f5f9;
  text-align: left;
  vertical-align: middle;
}

.table th {
  color: #64748b;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 700;
  background: #f8fafc;
}

.table tbody tr:hover {
  background-color: #f8fbff;
}

.table td strong {
  color: #0f172a;
  font-weight: 700;
  display: block;
}

.reject-note {
  display: block;
  margin-top: 6px;
  color: #b91c1c;
  font-size: 0.75rem;
  font-weight: 500;
}

.badge {
  min-height: 1.5rem;
  padding: 0 0.75rem;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 700;
  white-space: nowrap;
}

.badge--approved {
  color: #15803d;
  background: #dcfce7;
}

.badge--rejected {
  color: #b91c1c;
  background: #fee2e2;
}

.badge--pending {
  color: #ea580c;
  background: #ffedd5;
}

.badge--draft {
  color: #475569;
  background: #f1f5f9;
}

.actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.actions button,
.action-secondary,
.action-primary,
.action-danger {
  min-height: 2rem;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  background: #fff;
  color: #475569;
  padding: 0 0.75rem;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.15s ease;
}

.actions button:hover,
.action-secondary:hover {
  background: #f1f5f9;
  color: #0f172a;
}

.action-primary {
  border: 0;
  color: #fff;
  background: #1d4ed8;
}

.action-primary:hover {
  background: #1e40af;
  color: #fff;
}

.action-primary:disabled {
  background: #94a3b8;
  cursor: not-allowed;
}

.action-danger {
  border-color: #fca5a5 !important;
  color: #dc2626 !important;
  background: #fef2f2 !important;
}

.action-danger:hover {
  background: #fee2e2 !important;
}

.empty-box {
  min-height: 240px;
  display: grid;
  place-items: center;
  color: #64748b;
  font-weight: 500;
  background: #f8fafc;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 100;
  background: rgba(15, 23, 42, 0.4);
  backdrop-filter: blur(4px);
  padding: 1.5rem;
  display: grid;
  place-items: center;
  overflow-y: auto;
}

.partner-modal {
  width: min(800px, 100%);
  display: block;
  border-radius: 1rem;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.partner-modal__head {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #f8fafc;
}

.partner-modal__head h3 {
  margin: 0;
  color: #0f172a;
  font-size: 1.25rem;
  font-weight: 800;
}

.partner-modal__head button {
  border: 0;
  background: transparent;
  color: #64748b;
  font-size: 1.25rem;
  cursor: pointer;
  transition: color 0.15s ease;
  display: grid;
  place-items: center;
}

.partner-modal__head button:hover {
  color: #0f172a;
}

.partner-modal__body {
  padding: 1.5rem;
  display: grid;
  gap: 1rem;
  max-height: calc(100vh - 200px);
  overflow-y: auto;
}

.field-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.form-error {
  margin: 0;
  padding: 0 1.5rem 1rem;
  color: #dc2626;
  font-weight: 500;
  font-size: 0.875rem;
}

.partner-modal__foot {
  padding: 1.25rem 1.5rem;
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  border-top: 1px solid #f1f5f9;
  background: #f8fafc;
}

@media (max-width: 768px) {
  .filters,
  .field-grid {
    grid-template-columns: 1fr;
  }
}
</style>
