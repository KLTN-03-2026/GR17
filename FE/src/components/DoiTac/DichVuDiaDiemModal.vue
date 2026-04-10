<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="partner-modal dich-vu-modal">
      <header class="partner-modal__head">
        <div>
          <h3>Quản lý Dịch vụ / Bảng giá</h3>
          <p class="subtitle">Địa điểm: <strong>{{ location?.ten_dia_diem }}</strong></p>
        </div>
        <button type="button" @click="$emit('close')">
          <i class="fas fa-xmark"></i>
        </button>
      </header>

      <section class="partner-modal__body">
        <div v-if="loading" class="loading-state">
          <i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...
        </div>
        <div v-else>
          <!-- List Services -->
          <div class="services-list" v-if="services.length">
            <div
              v-for="service in services"
              :key="service.ma_dich_vu"
              class="service-card"
            >
              <div class="service-info">
                <h4>{{ service.ten_dich_vu }}</h4>
                <p v-if="service.mo_ta">{{ service.mo_ta }}</p>
                <div class="meta">
                  <span class="price">{{ formatCurrency(service.gia) }}</span>
                  <span class="max-pax"><i class="fas fa-user"></i> Tối đa: {{ service.so_nguoi_toi_da }} người</span>
                </div>
              </div>
              <div class="actions">
                <button type="button" class="action-secondary" @click="editService(service)">Sửa</button>
                <button type="button" class="action-danger" @click="deleteService(service)">Xóa</button>
              </div>
            </div>
          </div>
          <div v-else class="empty-state">
            <p>Chưa có dịch vụ nào được định nghĩa cho địa điểm này.</p>
          </div>

          <!-- Add / Edit Form -->
          <form class="service-form" @submit.prevent="saveService">
            <h4>{{ editingForm.ma_dich_vu ? 'Chỉnh sửa dịch vụ' : 'Thêm dịch vụ mới' }}</h4>
            <div class="field-grid">
              <label class="field">
                <span>Tên dịch vụ *</span>
                <input v-model.trim="editingForm.ten_dich_vu" type="text" required placeholder="Ví dụ: Phòng Standard, Combo lẩu hải sản">
              </label>
              <label class="field">
                <span>Giá tiền (VNĐ) *</span>
                <input v-model.number="editingForm.gia" type="number" min="0" required>
              </label>
            </div>
            <div class="field-grid">
              <label class="field">
                <span>Số người tối đa *</span>
                <input v-model.number="editingForm.so_nguoi_toi_da" type="number" min="1" required>
              </label>
            </div>
            <label class="field">
              <span>Mô tả dịch vụ</span>
              <textarea v-model.trim="editingForm.mo_ta" rows="2" placeholder="Chi tiết dịch vụ..."></textarea>
            </label>

             <p v-if="errorMsg" class="form-error">{{ errorMsg }}</p>

            <div class="form-actions">
              <button type="button" v-if="editingForm.ma_dich_vu" class="action-secondary" @click="cancelEdit">Hủy sửa</button>
              <button type="submit" class="action-primary" :disabled="saving">
                {{ saving ? "Đang lưu..." : (editingForm.ma_dich_vu ? "Cập nhật dịch vụ" : "Thêm dịch vụ") }}
              </button>
            </div>
          </form>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import { goiApi } from "../../services/httpClient";
import { showConfirm, showAlert } from "../../services/appDialog";

export default {
  name: "DichVuDiaDiemModal",
  props: {
    location: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      loading: false,
      saving: false,
      services: [],
      errorMsg: "",
      editingForm: this.getEmptyForm(),
    };
  },
  mounted() {
    this.fetchServices();
  },
  methods: {
    getEmptyForm() {
      return {
        ma_dich_vu: "",
        ten_dich_vu: "",
        mo_ta: "",
        so_nguoi_toi_da: 2,
        gia: 0,
      };
    },
    formatCurrency(val) {
      if (!val) return "0 VNĐ";
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
    },
    async fetchServices() {
      if (!this.location?.ma_dia_diem) return;
      this.loading = true;
      try {
        const res = await goiApi(`/doi-tac/dia-diem/${this.location.ma_dia_diem}/dich-vu`);
        if (res.data?.success) {
          this.services = res.data.data;
        }
      } catch (err) {
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
    editService(srv) {
      this.errorMsg = "";
      this.editingForm = {
        ma_dich_vu: srv.ma_dich_vu,
        ten_dich_vu: srv.ten_dich_vu,
        mo_ta: srv.mo_ta,
        so_nguoi_toi_da: srv.so_nguoi_toi_da,
        gia: srv.gia,
      };
    },
    cancelEdit() {
      this.editingForm = this.getEmptyForm();
      this.errorMsg = "";
    },
    async saveService() {
      // Validate
      if (!this.editingForm.ten_dich_vu) {
        this.errorMsg = "Vui lòng nhập tên dịch vụ"; return;
      }
      this.errorMsg = "";
      this.saving = true;

      try {
        const payload = {
          ma_dia_diem: this.location.ma_dia_diem,
          ten_dich_vu: this.editingForm.ten_dich_vu,
          mo_ta: this.editingForm.mo_ta,
          so_nguoi_toi_da: this.editingForm.so_nguoi_toi_da,
          gia: this.editingForm.gia,
        };

        if (this.editingForm.ma_dich_vu) {
          // Update
          const url = `/doi-tac/dia-diem/${this.location.ma_dia_diem}/dich-vu/${this.editingForm.ma_dich_vu}`;
          const res = await goiApi(url, { method: "PUT", data: payload });
        } else {
          // Create
          const url = `/doi-tac/dia-diem/${this.location.ma_dia_diem}/dich-vu`;
          const res = await goiApi(url, { method: "POST", data: payload });
        }

        await this.fetchServices();
        this.cancelEdit();
      } catch (err) {
        this.errorMsg = err.response?.data?.message || err.message || "Có lỗi xảy ra";
      } finally {
        this.saving = false;
      }
    },
    async deleteService(srv) {
      const ok = await showConfirm({
        title: "Xóa dịch vụ",
        message: `Bạn có chắc chắn muốn xóa dịch vụ "${srv.ten_dich_vu}"?`,
        tone: "danger",
        confirmText: "Xóa ngay",
      });
      if (!ok) return;

      try {
        const url = `/doi-tac/dia-diem/${this.location.ma_dia_diem}/dich-vu/${srv.ma_dich_vu}`;
        await goiApi(url, { method: "DELETE" });
        this.fetchServices();
      } catch (err) {
        showAlert({ title: "Lỗi", message: "Không thể xóa: " + err.message, tone: "danger" });
      }
    }
  }
};
</script>

<style scoped>
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
  width: min(700px, 100%);
  display: block;
  border-radius: 1rem;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.partner-modal__head {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #f8fafc;
}

.partner-modal__head h3 {
  margin: 0 0 0.25rem 0;
  color: #0f172a;
  font-size: 1.125rem;
  font-weight: 800;
}

.subtitle {
  margin: 0;
  font-size: 0.875rem;
  color: #64748b;
}

.partner-modal__head button {
  border: 0;
  background: transparent;
  color: #64748b;
  font-size: 1.25rem;
  cursor: pointer;
}

.partner-modal__head button:hover {
  color: #0f172a;
}

.partner-modal__body {
  padding: 1.5rem;
  max-height: 70vh;
  overflow-y: auto;
}

.loading-state, .empty-state {
  text-align: center;
  padding: 2rem;
  color: #64748b;
  background: #f8fafc;
  border-radius: 0.5rem;
  margin-bottom: 1.5rem;
}

.services-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 2rem;
}

.service-card {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  background: #fff;
  transition: box-shadow 0.2s;
}

.service-card:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.service-info h4 {
  margin: 0 0 0.25rem 0;
  font-size: 1rem;
  color: #0f172a;
}
.service-info p {
  margin: 0 0 0.5rem 0;
  font-size: 0.875rem;
  color: #475569;
}
.meta {
  display: flex;
  gap: 1rem;
  font-size: 0.875rem;
}
.price {
  font-weight: 700;
  color: #ea580c;
}
.max-pax {
  color: #15803d;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.actions button {
  padding: 0.4rem 0.75rem;
  font-size: 0.8rem;
  border-radius: 0.4rem;
  cursor: pointer;
  border: 1px solid #e2e8f0;
  background: #fff;
  font-weight: 600;
}
.actions .action-secondary:hover {
  background: #f1f5f9;
}
.actions .action-danger {
  color: #dc2626;
  border-color: #fca5a5;
  background: #fef2f2;
}
.actions .action-danger:hover {
  background: #fee2e2;
}

.service-form {
  border-top: 2px dashed #e2e8f0;
  padding-top: 1.5rem;
}
.service-form h4 {
  margin: 0 0 1rem 0;
  color: #334155;
}

.field-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-bottom: 1rem;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.field span {
  font-size: 0.875rem;
  font-weight: 600;
  color: #475569;
}
.field input, .field textarea {
  padding: 0.6rem 1rem;
  border: 1px solid #cbd5e1;
  border-radius: 0.5rem;
  background: #f8fafc;
  outline: none;
}
.field input:focus, .field textarea:focus {
  border-color: #2563eb;
  background: #fff;
}

.form-error {
  color: #dc2626;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 1rem;
}
.action-primary {
  background: #2563eb;
  color: #fff;
  border: none;
  padding: 0.6rem 1.25rem;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
}
.action-primary:hover:not(:disabled) {
  background: #1d4ed8;
}
.action-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.action-secondary {
  background: #fff;
  color: #475569;
  border: 1px solid #cbd5e1;
  padding: 0.6rem 1.25rem;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
}
.action-secondary:hover {
  background: #f1f5f9;
}
</style>
