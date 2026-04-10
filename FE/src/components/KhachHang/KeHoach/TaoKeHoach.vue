<template>
  <div class="plan-form-page">
    <section class="form-shell">
      <div class="form-header">
        <div>
          <p class="form-header__eyebrow">Khởi tạo kế hoạch</p>
          <h1>Tạo kế hoạch mới</h1>
          <p>Thiết lập thông tin cơ bản để nhóm hành trình có thể bắt đầu theo dõi lịch trình và ngân sách.</p>
        </div>
        <router-link class="back-link" to="/khach-hang/ke-hoach">Quay về danh sách</router-link>
      </div>

      <div v-if="thongBaoLoi" class="notice notice--error">
        <i class="fas fa-circle-exclamation"></i>
        <span>{{ thongBaoLoi }}</span>
      </div>

      <div v-if="thongBaoThanhCong" class="notice notice--success">
        <i class="fas fa-circle-check"></i>
        <span>{{ thongBaoThanhCong }}</span>
      </div>

      <form class="form-card" @submit.prevent="guiBieuMau">
        <div class="form-grid">
          <label class="field">
            <span>Mã kế hoạch</span>
            <input v-model.trim="form.ma_ke_hoach" type="text" placeholder="Ví dụ: KH101">
            <small v-if="errors.ma_ke_hoach">{{ errors.ma_ke_hoach }}</small>
          </label>

          <label class="field">
            <span>Nhóm hành trình</span>
            <select v-model="form.ma_nhom">
              <option value="">Chọn nhóm hành trình</option>
              <option v-for="group in groups" :key="group.id" :value="group.id">
                {{ group.name }} ({{ group.id }})
              </option>
            </select>
            <small v-if="errors.ma_nhom">{{ errors.ma_nhom }}</small>
          </label>

          <label class="field field--full">
            <span>Tên kế hoạch</span>
            <input v-model.trim="form.ten_ke_hoach" type="text" placeholder="Nhập tên kế hoạch">
            <small v-if="errors.ten_ke_hoach">{{ errors.ten_ke_hoach }}</small>
          </label>

          <label class="field">
            <span>Số người</span>
            <input v-model.number="form.so_nguoi" type="number" min="1">
            <small v-if="errors.so_nguoi">{{ errors.so_nguoi }}</small>
          </label>

          <label class="field">
            <span>Trạng thái</span>
            <select v-model.number="form.trang_thai">
              <option :value="1">Đang hoạt động</option>
              <option :value="0">Tạm dừng</option>
            </select>
          </label>

          <label class="field">
            <span>Ngày bắt đầu</span>
            <input v-model="form.ngay_bat_dau" type="date">
            <small v-if="errors.ngay_bat_dau">{{ errors.ngay_bat_dau }}</small>
          </label>

          <label class="field">
            <span>Ngày kết thúc</span>
            <input v-model="form.ngay_ket_thuc" type="date">
            <small v-if="errors.ngay_ket_thuc">{{ errors.ngay_ket_thuc }}</small>
          </label>

          <label class="field field--full">
            <span>Ngân sách dự kiến</span>
            <input v-model="form.ngan_sach_du_kien" type="number" min="0" step="1000" placeholder="Ví dụ: 25000000">
            <small v-if="errors.ngan_sach_du_kien">{{ errors.ngan_sach_du_kien }}</small>
          </label>
        </div>

        <div class="form-actions">
          <button class="secondary-btn" type="button" @click="$router.push('/khach-hang/ke-hoach')">Hủy</button>
          <button class="primary-btn" type="submit" :disabled="isSubmitting">
            <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
            <span>{{ isSubmitting ? "Đang lưu..." : "Lưu kế hoạch" }}</span>
          </button>
        </div>
      </form>
    </section>
  </div>

</template>

<script>
import { goiApi } from '../../../services/httpClient.js';
import {
  API_BASE,
  buildHeaders,
  createEmptyPlanForm,
  getStoredCustomerId,
  mapGroupsFromMembership,
  validatePlanForm,
} from "./planShared";
export default {
  name: "TaoKeHoach",
  data() {
    return {
      isSubmitting: false,
      thongBaoLoi: "",
      thongBaoThanhCong: "",
      maKhachHang: "",
      form: createEmptyPlanForm(),
      errors: {},
      groups: [],
    };
  },
  methods: {
    async taiNhom() {
      try {
        this.maKhachHang = getStoredCustomerId();
        if (!this.maKhachHang) {
          throw new Error("Không tìm thấy mã khách hàng trong phiên đăng nhập hiện tại.");
        }

        const phanHoi = await goiApi(
          `${API_BASE}/thanh-vien-nhom/search?Ma_khach_hang=${encodeURIComponent(this.maKhachHang)}`,
          {
            headers: buildHeaders(),
          },
        );
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể tải danh sách nhóm.");
        }

        this.groups = mapGroupsFromMembership(duLieuPhanHoi);
        if (!this.groups.length) {
          throw new Error("Tài khoản hiện tại chưa tham gia nhóm hành trình nào để tạo kế hoạch.");
        }

        if (!this.form.ma_nhom) {
          this.form.ma_nhom = this.groups[0]?.id || "";
        }
      } catch (error) {
        this.thongBaoLoi = error.message || "Không thể tải danh sách nhóm.";
      }
    },
    async guiBieuMau() {
      this.errors = validatePlanForm(this.form);
      this.thongBaoLoi = "";
      this.thongBaoThanhCong = "";

      const isAllowedGroup = this.groups.some((group) => group.id === String(this.form.ma_nhom || ""));
      if (!isAllowedGroup) {
        this.errors.ma_nhom = "Nhóm hành trình không thuộc tài khoản hiện tại.";
      }

      if (Object.keys(this.errors).length) return;

      this.isSubmitting = true;

      try {
        const phanHoi = await goiApi(`${API_BASE}/ke-hoach`, {
          method: "POST",
          headers: buildHeaders(true),
          body: JSON.stringify({
            ...this.form,
            ma_khach_hang: this.maKhachHang || getStoredCustomerId(),
            so_nguoi: Number(this.form.so_nguoi),
            ngan_sach_du_kien: Number(this.form.ngan_sach_du_kien),
            trang_thai: Number(this.form.trang_thai),
          }),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể tạo kế hoạch mới.");
        }

        this.thongBaoThanhCong = "Đã tạo kế hoạch thành công. Đang chuyển sang màn chỉnh sửa.";
        const newId = duLieuPhanHoi?.data?.ma_ke_hoach || this.form.ma_ke_hoach;
        setTimeout(() => {
          this.$router.push({
            path: "/khach-hang/ke-hoach",
            query: { created: newId },
          });
        }, 700);
      } catch (error) {
        this.thongBaoLoi = error.message || "Không thể tạo kế hoạch mới.";
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
.plan-form-page {
  width: min(1040px, calc(100% - 40px));
  margin: 0 auto;
  padding: 32px 0 48px;
}

.form-shell {
  display: grid;
  gap: 18px;
}

.form-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
}

.form-header__eyebrow {
  margin: 0 0 8px;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  font-size: 0.76rem;
  font-weight: 900;
}

.form-header h1 {
  margin: 0;
  color: #111827;
  font-size: clamp(2rem, 4vw, 3rem);
  line-height: 1.02;
  font-weight: 900;
}

.form-header p {
  margin: 14px 0 0;
  max-width: 720px;
  color: #64748b;
  line-height: 1.7;
}

.back-link,
.primary-btn,
.secondary-btn {
  min-height: 46px;
  padding: 0 18px;
  border-radius: 14px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  text-decoration: none;
  font-weight: 800;
}

.back-link,
.secondary-btn {
  border: 1px solid #e5ebf4;
  background: rgba(255, 255, 255, 0.9);
  color: #334155;
}

.primary-btn {
  border: 0;
  background: linear-gradient(135deg, #1d4ed8 0%, #4338ca 100%);
  color: #ffffff;
  box-shadow: 0 14px 28px rgba(37, 99, 235, 0.22);
}

.form-card,
.notice {
  border-radius: 24px;
  border: 1px solid #e5ebf4;
  background: rgba(255, 255, 255, 0.94);
  box-shadow: 0 18px 36px rgba(15, 23, 42, 0.06);
}

.notice {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 18px;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
}

.notice--success {
  background: #ecfdf5;
  color: #047857;
}

.form-card {
  padding: 24px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 18px;
}

.field {
  display: grid;
  gap: 8px;
}

.field--full {
  grid-column: 1 / -1;
}

.field span {
  color: #1f2937;
  font-weight: 700;
}

.field input,
.field select {
  min-height: 52px;
  padding: 0 16px;
  border: 1px solid #dbe4f0;
  border-radius: 16px;
  background: #f8faff;
  color: #111827;
  outline: none;
}

.field input:focus,
.field select:focus {
  border-color: #1d4ed8;
  background: #ffffff;
  box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
}

.field small {
  color: #dc2626;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 22px;
}

@media (max-width: 768px) {
  .plan-form-page {
    width: calc(100% - 24px);
    padding-top: 20px;
  }

  .form-header,
  .form-actions,
  .form-grid {
    grid-template-columns: 1fr;
    flex-direction: column;
    align-items: stretch;
  }
}
</style>





