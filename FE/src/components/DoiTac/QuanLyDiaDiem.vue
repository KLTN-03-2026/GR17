<template>
  <PartnerShell title="Quản lý địa điểm"
    subtitle="Tạo, cập nhật và xóa địa điểm của đối tác, theo dõi trạng thái duyệt theo từng bản ghi."
    :partner="partner" :loading="loading">
    <template #headerActions>
      <button class="toolbar-btn" type="button" @click="openCreateForm">
        <i class="fas fa-plus"></i>
        Tạo địa điểm mới
      </button>
      <button class="toolbar-btn toolbar-btn--ghost" type="button" @click="loadPage">
        <i class="fas fa-rotate"></i>
        Làm mới
      </button>
    </template>

    <section class="filters">
      <label class="field">
        <span>Tìm kiếm</span>
        <input v-model.trim="filters.keyword" type="text" placeholder="Tên hoặc mã địa điểm">
      </label>
      <label class="field">
        <span>Loại</span>
        <select v-model="filters.type">
          <option value="">Tất cả</option>
          <option value="1">Địa điểm du lịch</option>
          <option value="2">Khách sạn</option>
          <option value="3">Nhà hàng</option>
        </select>
      </label>
      <label class="field">
        <span>Trạng thái duyệt</span>
        <select v-model="filters.status">
          <option value="">Tất cả</option>
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
      <table v-if="filteredLocations.length" class="table">
        <thead>
          <tr>
            <th>Mã</th>
            <th>Tên địa điểm</th>
            <th>Loại</th>
            <th>Địa chỉ</th>
            <th>Trạng thái duyệt</th>
            <th>Cập nhật</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="location in filteredLocations" :key="location.ma_dia_diem">
            <td>{{ location.ma_dia_diem }}</td>
            <td>
              <strong>{{ location.ten_dia_diem }}</strong>
              <small v-if="location.ly_do_tu_choi" class="reject-note">{{ location.ly_do_tu_choi }}</small>
            </td>
            <td>{{ mapLoaiLabel(location.loai) }}</td>
            <td>{{ location.dia_chi || "-" }}</td>
            <td>
              <span :class="['badge', `badge--${mapStatusTone(location.trang_thai_duyet)}`]">
                {{ mapStatusLabel(location.trang_thai_duyet) }}
              </span>
            </td>
            <td>{{ formatDateTime(location.updated_at || location.created_at) }}</td>
            <td class="actions">
              <button type="button" @click="openEditForm(location)">Sửa</button>
              <!-- <button 
                v-if="location.loai != 1" 
                type="button" 
                class="action-secondary" 
                @click="openDichVuModal(location)"
              >
                Dịch vụ
              </button> -->
              <button type="button" class="action-danger" :disabled="deletingLocationId === location.ma_dia_diem"
                @click="deleteLocation(location)">
                {{ deletingLocationId === location.ma_dia_diem ? "Đang xóa..." : "Xóa" }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else class="empty-box">Không có địa điểm phù hợp bộ lọc.</div>
    </section>

    <div v-if="showForm" class="modal-overlay" @click.self="closeForm">
      <form class="partner-modal" @submit.prevent="saveForm">
        <header class="partner-modal__head">
          <h3>{{ editingLocationId ? "Chỉnh sửa địa điểm" : "Tạo địa điểm mới" }}</h3>
          <button type="button" @click="closeForm">
            <i class="fas fa-xmark"></i>
          </button>
        </header>

        <section class="partner-modal__body">
          <label class="field">
            <span>Tên địa điểm *</span>
            <input v-model.trim="form.ten_dia_diem" type="text" required>
          </label>
          <div class="field-grid">
            <label class="field">
              <span>Loại *</span>
              <select v-model.number="form.loai">
                <option :value="1">Địa điểm du lịch</option>
                <option :value="2">Khách sạn</option>
                <option :value="3">Nhà hàng</option>
              </select>
            </label>
            <label class="field">
              <span>Số điện thoại</span>
              <input v-model.trim="form.sdt" type="text" placeholder="0xxxxxxxxx">
            </label>
          </div>
          <label class="field">
            <span>Địa chỉ *</span>
            <input v-model.trim="form.dia_chi" type="text" required>
          </label>
          <div class="field-grid">
            <label class="field">
              <span>Kinh độ *</span>
              <input v-model.number="form.kinh_do" type="number" step="0.000001" required>
            </label>
            <label class="field">
              <span>Vĩ độ *</span>
              <input v-model.number="form.vi_do" type="number" step="0.000001" required>
            </label>
          </div>
          <div class="field-grid">
            <label class="field">
              <span>Giờ mở cửa</span>
              <input v-model="form.gio_mo_cua" type="time">
            </label>
            <label class="field">
              <span>Giờ đóng cửa</span>
              <input v-model="form.gio_dong_cua" type="time">
            </label>
          </div>
          <div class="field-grid">
            <label class="field">
              <span>Giá giao động</span>
              <input v-model.number="form.gia_giao_dong" type="number" min="0">
            </label>
            <label class="field">
              <span>Thời gian tham quan</span>
              <input v-model.trim="form.thoi_gian_tham_quan" type="text" placeholder="Ví dụ: 2 giờ">
            </label>
          </div>
          <label class="field">
            <span>Hình ảnh (URL)</span>
            <input v-model.trim="form.hinh_anh" type="text">
          </label>
          <label class="field">
            <span>Mô tả</span>
            <textarea v-model.trim="form.mo_ta" rows="3"></textarea>
          </label>
        </section>

        <p v-if="formError" class="form-error">{{ formError }}</p>

        <footer class="partner-modal__foot">
          <button type="button" class="action-secondary" @click="closeForm">Hủy</button>
          <button type="submit" class="action-primary" :disabled="saving">
            {{ saving ? "Đang lưu..." : "Lưu địa điểm" }}
          </button>
        </footer>
      </form>
    </div>

    <!-- Modal quản lý Dịch Vụ Địa Điểm -->
    <DichVuDiaDiemModal v-if="showDichVuModal" :location="selectedLocationForDichVu" @close="closeDichVuModal" />
  </PartnerShell>
</template>

<script>
import PartnerShell from "./layout/PartnerShell.vue";
import DichVuDiaDiemModal from "./DichVuDiaDiemModal.vue";
import { showConfirm } from "../../services/appDialog";
import { toastError, toastInfo, toastSuccess } from "./shared/partnerToast";
import {
  createPartnerLocation,
  deletePartnerLocation,
  fetchPartnerLocations,
  fetchPartnerSession,
  formatDateTime,
  mapStatusLabel,
  mapStatusTone,
  updatePartnerLocation,
} from "./shared/partnerApi";

function newLocationForm() {
  return {
    ten_dia_diem: "",
    loai: 1,
    dia_chi: "",
    sdt: "",
    kinh_do: 106.7,
    vi_do: 10.77,
    gio_mo_cua: "",
    gio_dong_cua: "",
    gia_giao_dong: null,
    hinh_anh: "",
    mo_ta: "",
    thoi_gian_tham_quan: "",
  };
}

import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({ position: 'top-right' });

export default {
  name: "PartnerQuanLyDiaDiem",
  components: {
    PartnerShell,
    DichVuDiaDiemModal,
  },
  data() {
    return {
      loading: false,
      saving: false,
      deletingLocationId: "",
      partner: null,
      locations: [],
      filters: {
        keyword: "",
        type: "",
        status: "",
      },
      showForm: false,
      editingLocationId: "",
      form: newLocationForm(),
      errorMessage: "",
      formError: "",

      showDichVuModal: false,
      selectedLocationForDichVu: null,
    };
  },
  computed: {
    filteredLocations() {
      const keyword = this.filters.keyword.toLowerCase();
      return [...this.locations]
        .filter((location) => {
          const typeMatch = !this.filters.type || String(location?.loai || "") === this.filters.type;
          const statusMatch = !this.filters.status
            || String(location?.trang_thai_duyet || "").toLowerCase() === this.filters.status;
          if (!typeMatch || !statusMatch) return false;

          if (!keyword) return true;
          return (
            String(location?.ma_dia_diem || "").toLowerCase().includes(keyword)
            || String(location?.ten_dia_diem || "").toLowerCase().includes(keyword)
            || String(location?.dia_chi || "").toLowerCase().includes(keyword)
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
    formatDateTime,
    mapStatusLabel,
    mapStatusTone,
    mapLoaiLabel(loai) {
      if (Number(loai) === 1) return "Địa điểm du lịch";
      if (Number(loai) === 2) return "Khách sạn";
      if (Number(loai) === 3) return "Nhà hàng";
      return "Không xác định";
    },
    resetMessages() {
      // cleared errorMessage
      this.formError = "";
    },
    async loadPage() {
      this.loading = true;
      this.resetMessages();
      try {
        const [partner, locations] = await Promise.all([
          fetchPartnerSession(),
          fetchPartnerLocations(),
        ]);

        this.partner = partner;
        this.locations = locations;
      } catch (error) {
        toaster.error(error?.message || "Không thể tải dữ liệu quản lý địa điểm.");
      } finally {
        this.loading = false;
      }
    },
    openCreateForm() {
      this.editingLocationId = "";
      this.form = newLocationForm();
      this.formError = "";
      this.showForm = true;
    },
    openEditForm(location) {
      this.editingLocationId = location?.ma_dia_diem || "";
      this.form = {
        ten_dia_diem: location?.ten_dia_diem || "",
        loai: Number(location?.loai || 1),
        dia_chi: location?.dia_chi || "",
        sdt: location?.sdt || "",
        kinh_do: location?.kinh_do ? Number(location.kinh_do) : 106.7,
        vi_do: location?.vi_do ? Number(location.vi_do) : 10.77,
        gio_mo_cua: location?.gio_mo_cua || "",
        gio_dong_cua: location?.gio_dong_cua || "",
        gia_giao_dong: location?.gia_giao_dong ? Number(location.gia_giao_dong) : null,
        hinh_anh: location?.hinh_anh || "",
        mo_ta: location?.mo_ta || "",
        thoi_gian_tham_quan: location?.thoi_gian_tham_quan || "",
      };
      this.formError = "";
      this.showForm = true;
    },
    closeForm() {
      this.showForm = false;
      this.formError = "";
    },
    openDichVuModal(location) {
      this.selectedLocationForDichVu = location;
      this.showDichVuModal = true;
    },
    closeDichVuModal() {
      this.showDichVuModal = false;
      this.selectedLocationForDichVu = null;
    },
    normalizePayload() {
      return {
        ten_dia_diem: this.form.ten_dia_diem,
        loai: Number(this.form.loai),
        dia_chi: this.form.dia_chi,
        sdt: this.form.sdt || null,
        kinh_do: Number(this.form.kinh_do),
        vi_do: Number(this.form.vi_do),
        gio_mo_cua: this.form.gio_mo_cua || null,
        gio_dong_cua: this.form.gio_dong_cua || null,
        gia_giao_dong: this.form.gia_giao_dong || null,
        hinh_anh: this.form.hinh_anh || null,
        mo_ta: this.form.mo_ta || null,
        thoi_gian_tham_quan: this.form.thoi_gian_tham_quan || null,
      };
    },
    async saveForm() {
      if (!this.form.ten_dia_diem || !this.form.dia_chi) {
        this.formError = "Vui lòng nhập đầy đủ tên địa điểm và địa chỉ.";
        return;
      }

      this.saving = true;
      this.formError = "";
      // cleared errorMessage

      try {
        const payload = this.normalizePayload();
        let responsePayload;
        if (this.editingLocationId) {
          responsePayload = await updatePartnerLocation(this.editingLocationId, payload);
          toastSuccess("Đã cập nhật địa điểm. Bản ghi đã chuyển về trạng thái chờ duyệt.");
        } else {
          responsePayload = await createPartnerLocation(payload);
          toastSuccess("Đã tạo địa điểm mới thành công.");
        }

        if (responsePayload?.is_duplicate) {
          toastInfo("Phát hiện địa điểm trùng. Hệ thống đã dùng bản ghi có sẵn.");
        }

        this.closeForm();
        await this.loadPage();
      } catch (error) {
        this.formError = error?.message || "Không thể lưu thông tin địa điểm.";
        toastError(this.formError);
      } finally {
        this.saving = false;
      }
    },
    async deleteLocation(location) {
      if (!location?.ma_dia_diem) return;

      const confirmed = await showConfirm({
        title: "Xác nhận xóa địa điểm",
        message: `Bạn có chắc muốn xóa địa điểm "${location.ten_dia_diem || location.ma_dia_diem}"?`,
        tone: "danger",
        confirmText: "Xóa địa điểm",
        cancelText: "Hủy",
      });
      if (!confirmed) return;

      this.deletingLocationId = location.ma_dia_diem;
      // cleared errorMessage

      try {
        await deletePartnerLocation(location.ma_dia_diem);
        toaster.success("Địa điểm đã được xóa thành công.");
        await this.loadPage();
      } catch (error) {
        toaster.error(error?.message || "Không thể xóa địa điểm.");
      } finally {
        this.deletingLocationId = "";
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
  grid-template-columns: minmax(280px, 1.5fr) repeat(2, minmax(180px, 1fr));
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
  .filters {
    grid-template-columns: 1fr;
  }

  .field-grid {
    grid-template-columns: 1fr;
  }
}
</style>
