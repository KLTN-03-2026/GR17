<template>
  <div>
    <!-- Nút Floating (Trôi nổi) -->
    <button class="bug-report-button" @click="openModal" title="Báo cáo lỗi / Góp ý">
      <i class="fa-solid fa-bug"></i>
    </button>

    <!-- Modal Form -->
    <div v-if="isModalOpen" class="bug-report-modal-overlay" @click.self="closeModal">
      <div class="bug-report-modal">
        <div class="modal-header">
          <h3>Báo cáo lỗi / Góp ý</h3>
          <button class="close-btn" @click="closeModal">&times;</button>
        </div>

        <form @submit.prevent="submitReport">
          <div class="form-group">
            <label for="loai">Loại vấn đề <span class="text-danger">*</span></label>
            <select id="loai" v-model="formData.loai" required>
              <option value="Lỗi">Báo cáo Lỗi</option>
              <option value="Góp ý">Góp ý cải thiện</option>
              <option value="Khác">Khác</option>
            </select>
          </div>

          <div class="form-group">
            <label for="tieu_de">Tiêu đề <span class="text-danger">*</span></label>
            <input type="text" id="tieu_de" v-model="formData.tieu_de" placeholder="Tóm tắt vấn đề..." required />
          </div>

          <div class="form-group">
            <label for="mo_ta">Mô tả chi tiết <span class="text-danger">*</span></label>
            <textarea id="mo_ta" v-model="formData.mo_ta" rows="4" placeholder="Vui lòng mô tả chi tiết vấn đề bạn gặp phải..." required></textarea>
          </div>

          <div class="form-actions">
            <button type="button" class="btn btn-secondary" @click="closeModal">Hủy</button>
            <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
              {{ isSubmitting ? 'Đang gửi...' : 'Gửi báo cáo' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { createToaster } from "@meforma/vue-toaster";
import axios from "axios";

export default {
  name: "BugReportWidget",
  data() {
    return {
      isModalOpen: false,
      isSubmitting: false,
      formData: {
        loai: "Lỗi",
        tieu_de: "",
        mo_ta: "",
        url_trang_loi: "",
      },
      toaster: createToaster({ position: "top-right" }),
    };
  },
  methods: {
    openModal() {
      this.isModalOpen = true;
      this.formData.url_trang_loi = window.location.href;
    },
    closeModal() {
      this.isModalOpen = false;
      this.resetForm();
    },
    resetForm() {
      this.formData = {
        loai: "Lỗi",
        tieu_de: "",
        mo_ta: "",
        url_trang_loi: "",
      };
    },
    async submitReport() {
      this.isSubmitting = true;
      try {
        const token = localStorage.getItem('customer_token');
        const headers = {};
        if (token) {
          headers['Authorization'] = `Bearer ${token}`;
        }

        const response = await axios.post("http://127.0.0.1:8000/api/khach-hang/phan-hoi", this.formData, { headers });
        
        if (response.data.success) {
          this.toaster.success(response.data.message || "Cảm ơn bạn đã gửi phản hồi!");
          this.closeModal();
        }
      } catch (error) {
        console.error("Lỗi khi gửi báo cáo:", error);
        this.toaster.error("Có lỗi xảy ra khi gửi báo cáo. Vui lòng thử lại sau.");
      } finally {
        this.isSubmitting = false;
      }
    },
  },
};
</script>

<style scoped>
/* Nút trôi nổi */
.bug-report-button {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: #ef4444; /* Màu đỏ nổi bật cho bug */
  color: white;
  border: none;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  z-index: 9999;
  transition: transform 0.2s, background-color 0.2s;
}

.bug-report-button:hover {
  transform: scale(1.1);
  background-color: #dc2626;
}

/* Modal Overlay */
.bug-report-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
  backdrop-filter: blur(2px);
}

/* Modal Box */
.bug-report-modal {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  padding: 24px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Header */
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 12px;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  color: #1f2937;
  font-weight: 600;
}

.close-btn {
  background: none;
  border: none;
  font-size: 24px;
  color: #9ca3af;
  cursor: pointer;
  padding: 0;
  line-height: 1;
}

.close-btn:hover {
  color: #4b5563;
}

/* Form Styles */
.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  font-weight: 500;
  color: #374151;
  font-size: 0.875rem;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
  color: #1f2937;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.text-danger {
  color: #ef4444;
}

/* Actions */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 24px;
}

.btn {
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 500;
  font-size: 0.875rem;
  cursor: pointer;
  border: none;
  transition: background-color 0.2s;
}

.btn-secondary {
  background-color: #f3f4f6;
  color: #4b5563;
}

.btn-secondary:hover {
  background-color: #e5e7eb;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background-color: #2563eb;
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
</style>
